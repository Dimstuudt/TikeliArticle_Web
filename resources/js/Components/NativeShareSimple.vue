<script setup>
import { ref, onMounted } from 'vue'

// Props dari parent
const props = defineProps({
  url: { type: String, default: '' }, // bisa di-override
  title: { type: String, default: 'Bagikan Artikel' },
  text: { type: String, default: '' },
})

const shareUrl = ref('')
const copied = ref(false)
const error = ref('')

onMounted(() => {
  // Gunakan props.url kalau di-passing, kalau nggak pakai window.location.href
  shareUrl.value = props.url || window.location.href
})

// Share menggunakan Web Share API
async function shareNative() {
  error.value = ''
  if (navigator.share) {
    try {
      await navigator.share({
        title: props.title,
        text: props.text,
        url: shareUrl.value,
      })
    } catch (e) {
      if (e && e.name !== 'AbortError') {
        error.value = e.message || 'Gagal membagikan'
      }
    }
  } else {
    copyLink()
  }
}

// Copy link ke clipboard
async function copyLink() {
  try {
    await navigator.clipboard.writeText(shareUrl.value)
    copied.value = true
    setTimeout(() => (copied.value = false), 1500)
  } catch {
    error.value = 'Gagal menyalin link'
  }
}
</script>

<template>
  <div class="flex items-center gap-2">
    <!-- Tombol Share -->
    <button
      @click="shareNative"
      class="inline-flex items-center gap-1 px-3 py-1 rounded-full
             bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
             text-xs font-semibold cursor-pointer hover:scale-105 transition-all"
      title="Share artikel ini"
    >
      📤 Share
    </button>

    <!-- Tombol Copy -->
    <button
      @click="copyLink"
      class="inline-flex items-center gap-1 px-3 py-1 rounded-full
             bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200
             text-xs font-semibold cursor-pointer hover:scale-105 transition-all"
      title="Salin link artikel"
    >
      <span>📋</span>
      <span v-if="!copied">Copy</span>
      <span v-else class="text-green-600 dark:text-green-400 font-bold">Copied ✅</span>
    </button>

    <!-- Pesan Error -->
    <p v-if="error" class="text-red-500 text-xs ml-2">{{ error }}</p>
  </div>
</template>
