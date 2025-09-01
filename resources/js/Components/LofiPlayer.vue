<script setup>
import { ref, onMounted } from "vue"
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

onMounted(() => {
  const audio = getAudio()
  if (audio) {
    isPlaying.value = !audio.paused
  }
})
</script>

<template>
  <transition name="fade-scale">
    <div
      v-show="true"
      class="fixed bottom-6 left-6 transition-all duration-300 shadow-xl border dark:border-gray-700 overflow-hidden"
      :class="[
        minimized
          ? 'w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center'
          : 'w-[260px] max-w-[85vw] rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 h-36'
      ]"
    >
      <!-- Minimized Floating Button -->
      <div
        v-if="minimized"
        class="cursor-pointer flex items-center justify-center w-full h-full relative"
        @click="minimized = false"
      >
        <Music v-if="!isPlaying" class="w-6 h-6 text-white drop-shadow-lg" />

        <!-- Equalizer (jalan kalau play) -->
        <div v-else class="flex gap-[2px] items-end h-5">
          <div class="w-[3px] bg-white rounded animate-eq1"></div>
          <div class="w-[3px] bg-white rounded animate-eq2"></div>
          <div class="w-[3px] bg-white rounded animate-eq3"></div>
        </div>
      </div>

      <!-- Expanded Player -->
      <div v-else class="flex flex-col h-full">
        <!-- Header -->
        <div
          class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700"
        >
          <div class="flex items-center gap-2">
            <Music class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
              Lofi Player
            </span>

            <!-- Equalizer pindah ke sini -->
            <div v-if="isPlaying" class="flex gap-[2px] items-end h-4 ml-2">
              <div class="w-[2px] bg-blue-500 dark:bg-blue-300 rounded animate-eq1"></div>
              <div class="w-[2px] bg-blue-500 dark:bg-blue-300 rounded animate-eq2"></div>
              <div class="w-[2px] bg-blue-500 dark:bg-blue-300 rounded animate-eq3"></div>
            </div>
          </div>
          <button
            class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-transform hover:rotate-180"
            @click="minimized = true"
          >
            <ChevronDown class="w-4 h-4 text-gray-500 dark:text-gray-400" />
          </button>
        </div>

        <!-- Konten -->
        <div class="px-4 py-3 flex flex-col gap-3 flex-1">
          <!-- Play/Pause -->
          <button
            @click.stop="togglePlay"
            class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-100 dark:bg-blue-900/40
                       hover:scale-105 transition-transform"
          >
            <Play v-if="!isPlaying" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            <Pause v-else class="w-5 h-5 text-red-500" />

            <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
              {{ isPlaying ? "Pause" : "Play" }}
              – {{ playlist[currentTrackIndex]?.title || "No Track" }}
            </span>
          </button>

          <!-- Dropdown pilih lagu -->
          <select
            v-if="playlist.length > 0"
            v-model="currentTrackIndex"
            @change="changeTrack(currentTrackIndex)"
            class="text-sm px-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                   bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200
                   focus:ring-2 focus:ring-blue-400 outline-none"
          >
            <option v-for="(track, i) in playlist" :key="i" :value="i">
              {{ track.title }}
            </option>
          </select>

          <span v-else class="text-sm italic text-gray-400">No tracks available</span>
        </div>
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
