<script setup>
import { ref, onMounted, watch } from "vue"
import {
  getAudio,
  isPlaying,
  togglePlay,
  playlist,
  currentTrackIndex,
  changeTrack,
} from "@/Utils/lofiPlayer.js"

import { Play, Pause, ChevronDown, Music } from "lucide-vue-next"

const minimized = ref(true)
const progress = ref(0) // 0 to 1
const currentTime = ref(0)
const duration = ref(0)

let audio = null

onMounted(() => {
  audio = getAudio()
  if (audio) {
    isPlaying.value = !audio.paused
    duration.value = audio.duration || 0

    audio.addEventListener("timeupdate", () => {
      currentTime.value = audio.currentTime
      duration.value = audio.duration || 0
      progress.value = audio.currentTime / (audio.duration || 1)
    })

    audio.addEventListener("ended", () => {
      if (currentTrackIndex.value < playlist.length - 1) {
        changeTrack(currentTrackIndex.value + 1)
      } else {
        isPlaying.value = false
      }
    })
  }
})

watch(currentTrackIndex, () => {
  if (audio) {
    changeTrack(currentTrackIndex.value)
    progress.value = 0
    currentTime.value = 0
    duration.value = audio.duration || 0
  }
})

const formatTime = (time) => {
  if (!time || isNaN(time)) return "0:00"
  const m = Math.floor(time / 60)
  const s = Math.floor(time % 60)
  return `${m}:${s.toString().padStart(2, "0")}`
}

const onSeek = (event) => {
  if (!audio) return
  const rect = event.currentTarget.getBoundingClientRect()
  const clickX = event.clientX - rect.left
  const newProgress = Math.min(Math.max(clickX / rect.width, 0), 1)
  audio.currentTime = newProgress * (audio.duration || 1)
  progress.value = newProgress
  currentTime.value = audio.currentTime
}
</script>

<template>
  <transition name="fade-scale">
    <div
      v-show="true"
      class="fixed bottom-6 left-6 transition-all duration-300 shadow-xl border dark:border-gray-700 overflow-visible"
      :class="[
        minimized
          ? 'w-12 h-12 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center cursor-pointer'
          : 'w-[280px] max-w-[90vw] rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 min-h-[200px] flex flex-col'
      ]"
    >
      <!-- Minimized Floating Button -->
      <div
        v-if="minimized"
        class="flex items-center justify-center w-full h-full relative"
        @click="minimized = false"
        aria-label="Open Lofi Player"
        role="button"
        tabindex="0"
        @keydown.enter="minimized = false"
      >
        <Music v-if="!isPlaying" class="w-6 h-6 text-white drop-shadow-lg" />

        <div v-else class="flex gap-[3px] items-end h-6">
          <div class="w-[3px] bg-white rounded animate-eq1"></div>
          <div class="w-[3px] bg-white rounded animate-eq2"></div>
          <div class="w-[3px] bg-white rounded animate-eq3"></div>
        </div>
      </div>

      <!-- Expanded Player -->
      <div v-else class="flex flex-col h-full">
        <!-- Header -->
        <header
          class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700"
        >
          <div class="flex items-center gap-3">
            <Music class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 select-none">
              Lofi Player
            </h2>

            <div v-if="isPlaying" class="flex gap-[3px] items-end h-5 ml-2">
              <div class="w-[3px] bg-blue-500 dark:bg-blue-300 rounded animate-eq1"></div>
              <div class="w-[3px] bg-blue-500 dark:bg-blue-300 rounded animate-eq2"></div>
              <div class="w-[3px] bg-blue-500 dark:bg-blue-300 rounded animate-eq3"></div>
            </div>
          </div>
          <button
            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-transform hover:rotate-180 focus:outline-none focus:ring-2 focus:ring-blue-400"
            @click="minimized = true"
            aria-label="Minimize Player"
          >
            <ChevronDown class="w-5 h-5 text-gray-500 dark:text-gray-400" />
          </button>
        </header>

        <!-- Content -->
        <main class="px-5 py-4 flex flex-col gap-4 flex-1 overflow-visible">
          <!-- Play/Pause Button -->
          <button
            @click.stop="togglePlay"
            class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-100 dark:bg-blue-900/40
                       hover:scale-105 transition-transform shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
            aria-pressed="isPlaying"
            aria-label="Play or Pause"
          >
            <Play v-if="!isPlaying" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            <Pause v-else class="w-6 h-6 text-red-500" />

            <span class="text-md font-medium text-gray-900 dark:text-gray-200 truncate">
              {{ isPlaying ? "Pause" : "Play" }}
              – {{ playlist[currentTrackIndex]?.title || "No Track" }}
            </span>
          </button>

          <!-- Progress Bar -->
          <div
            class="relative w-full h-2 rounded bg-gray-300 dark:bg-gray-700 cursor-pointer"
            @click="onSeek"
            aria-label="Audio progress bar"
            role="slider"
            :aria-valuemin="0"
            :aria-valuemax="duration"
            :aria-valuenow="currentTime"
            tabindex="0"
            @keydown.left.prevent="audio.currentTime = Math.max(audio.currentTime - 5, 0)"
            @keydown.right.prevent="audio.currentTime = Math.min(audio.currentTime + 5, duration)"
          >
            <div
              class="h-2 rounded bg-blue-600 dark:bg-blue-400 transition-all"
              :style="{ width: (progress * 100) + '%' }"
            ></div>
          </div>
          <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 select-none font-mono">
            <span>{{ formatTime(currentTime) }}</span>
            <span>{{ formatTime(duration) }}</span>
          </div>

          <!-- Dropdown pilih lagu -->
          <select
            v-if="playlist.length > 0"
            v-model="currentTrackIndex"
            @change="changeTrack(currentTrackIndex)"
            class="text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                   bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200
                   focus:ring-2 focus:ring-blue-400 outline-none"
            aria-label="Select track"
          >
            <option v-for="(track, i) in playlist" :key="i" :value="i">
              {{ track.title }}
            </option>
          </select>

          <span v-else class="text-sm italic text-gray-400 select-none">No tracks available</span>
        </main>
      </div>
    </div>
  </transition>
</template>

<style scoped>
/* Animasi masuk smooth */
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.3s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.9);
}

/* Equalizer */
@keyframes eq1 {
  0%, 100% { height: 4px; }
  50% { height: 14px; }
}
@keyframes eq2 {
  0%, 100% { height: 12px; }
  50% { height: 5px; }
}
@keyframes eq3 {
  0%, 100% { height: 6px; }
  50% { height: 16px; }
}
.animate-eq1 { animation: eq1 1s infinite ease-in-out; }
.animate-eq2 { animation: eq2 1.3s infinite ease-in-out; }
.animate-eq3 { animation: eq3 0.8s infinite ease-in-out; }
</style>
