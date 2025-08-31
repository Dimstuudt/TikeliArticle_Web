<script setup>
import { onMounted } from "vue"
import { getAudio, isPlaying, togglePlay } from "@/Utils/lofiPlayer.js"

onMounted(() => {
  const audio = getAudio()
  isPlaying.value = !audio.paused
})
</script>

<template>
  <div
    class="fixed bottom-6 left-6 flex items-center gap-2 px-4 py-2 rounded-full
           bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700
           text-gray-800 dark:text-gray-200 cursor-pointer
           transition-all duration-300 hover:scale-105"
  >
    <button @click="togglePlay" class="flex items-center gap-2">
      <!-- Play Icon -->
      <svg
        v-if="!isPlaying"
        xmlns="http://www.w3.org/2000/svg"
        class="w-6 h-6 text-blue-600 dark:text-blue-400"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M14.752 11.168l-5.197-3.027A1 1 0 008 9.027v5.946a1 1 0 001.555.832l5.197-3.027a1 1 0 000-1.664z" />
      </svg>

      <!-- Equalizer -->
      <div v-else class="flex items-end gap-[3px] w-6 h-6">
        <span class="w-[3px] h-3 bg-red-500 animate-eq1"></span>
        <span class="w-[3px] h-4 bg-red-500 animate-eq2"></span>
        <span class="w-[3px] h-2 bg-red-500 animate-eq3"></span>
      </div>

      <span class="text-sm font-medium">
        {{ isPlaying ? "Pause Lofi" : "Play Lofi" }}
      </span>
    </button>
  </div>
</template>

<style scoped>
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
