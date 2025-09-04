<!-- resources/js/Components/Popups/FlirtyCleanWelcome.vue -->
<script setup>
import { ref, onMounted } from "vue"
import { Link } from "@inertiajs/vue3"

// Rayuan / teaser lines
const lines = [
  "✨ Aku punya artikel yang bisa bikin kamu betah lama-lama di sini...",
  "😉 Jangan kaget kalau artikel ini bikin kamu nggak bisa move on.",
  "📖 Mau aku rekomendasiin bacaan yang cocok sama mood kamu?",
  "🚀 Kalau buka satu artikel aja udah bikin penasaran, gimana kalau kamu login?",
  "🌸 Aku nggak janji bikin kamu jatuh cinta, tapi artikelnya bisa sih.",
  "🔥 Ada banyak hal menarik di sini, masa kamu cuma numpang lewat?",
  "💡 Katanya baca artikel bikin pinter, tapi login bikin tambah kece 😉",
  "🌙 Malam panjang lebih seru ditemani artikel + akun kamu sendiri ✨",
  "🎯 Artikel ini bisa jadi awal cerita seru kamu di sini.",
  "💌 Penasaran nggak kalau aku kasih artikel yang sesuai mood-mu?"
]

// Sub-text ringan
const sublines = [
  "Login dulu biar bisa simpan artikel kesukaanmu 😉",
  "Kalau login, aku bisa kasih rekomendasi personal loh ✨",
  "Tanpa login juga bisa sih, tapi kurang seru. Setuju nggak? 😏",
  "Ayo gabung, siapa tau ketemu inspirasi baru 🌟",
  "Artikel seru udah nunggu, login biar makin asik 🚀",
  "Baca boleh, tapi login bikin kamu dapet pengalaman penuh 💯"
]

// Gradient warna random
const gradients = [
  "from-pink-50 to-red-50 dark:from-gray-800 dark:to-gray-900",
  "from-purple-50 to-indigo-50 dark:from-gray-800 dark:to-indigo-900",
  "from-yellow-50 to-orange-50 dark:from-gray-800 dark:to-yellow-900",
  "from-teal-50 to-green-50 dark:from-gray-800 dark:to-green-900"
]

const show = ref(false)
const closing = ref(false) // animasi keluar
const quote = ref("")
const sub = ref("")
const bg = ref("")
const sessionKey = "flirtyCleanWelcomeShown"

function closePopup() {
  closing.value = true
  setTimeout(() => {
    show.value = false
    closing.value = false
  }, 400) // waktu sama dengan animasi fadeOut
}

function randomPick() {
  quote.value = lines[Math.floor(Math.random() * lines.length)]
  sub.value = sublines[Math.floor(Math.random() * sublines.length)]
  bg.value = gradients[Math.floor(Math.random() * gradients.length)]
}

onMounted(() => {
  if (!sessionStorage.getItem(sessionKey)) {
    setTimeout(() => {
      randomPick()
      show.value = true
      sessionStorage.setItem(sessionKey, "true")

      // Ganti quote tiap 5 detik biar hidup
      setInterval(() => {
        randomPick()
      }, 5000)
    }, 600)
  }
})
</script>

<template>
  <div
    v-if="show"
    class="fixed right-6 bottom-6 z-50 w-80 md:w-96"
    role="dialog"
    aria-label="Welcome popup"
  >
    <div
      class="relative rounded-2xl p-5 md:p-6 shadow-xl border border-opacity-50 transition-all duration-400"
      :class="[ `bg-gradient-to-br ${bg}`, closing ? 'animate-fadeOut' : 'animate-fadeIn']"
    >
      <!-- Close -->
      <button
        @click="closePopup"
        class="absolute top-3 right-3 rounded-full p-1 hover:scale-110 transform transition"
        aria-label="Tutup"
      >
        <svg
          class="h-4 w-4 text-pink-600 dark:text-gray-300"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>

      <!-- Header -->
      <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white animate-bounceSlow">
        👀 Eh, ketemu kamu di sini~
      </h3>
      <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 transition-all duration-500 animate-fadeSlide">
        {{ quote }}
      </p>

      <!-- Body -->
      <p class="mt-4 text-sm md:text-base text-gray-800 dark:text-gray-200 font-medium transition-all duration-500 animate-fadeSlide">
        {{ sub }}
      </p>

      <!-- Actions -->
      <div class="mt-5 grid grid-cols-2 gap-2">
        <Link
          href="/welcome"
          class="col-span-1 inline-flex items-center justify-center px-3 py-2 rounded-lg
                 bg-pink-500 text-white font-semibold text-sm hover:bg-pink-600 transition transform hover:scale-105 hover:animate-wiggle"
          @click.native="closePopup"
        >
          Nanti dulu deh 😏
        </Link>
        <Link
          href="/login"
          class="col-span-1 inline-flex items-center justify-center px-3 py-2 rounded-lg
                 border border-pink-200 dark:border-gray-700 bg-white dark:bg-gray-700
                 text-sm font-medium hover:shadow-lg transition transform hover:scale-105 hover:animate-wiggle"
          @click.native="closePopup"
        >
          Login / Daftar ✨
        </Link>
      </div>

      <!-- Footer kecil -->
      <div class="mt-4 text-xs text-gray-500 dark:text-gray-400 text-center italic animate-pulse">
        Jangan lama-lama mikir ya, pilih salah satu 😉
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes wiggle {
  0%, 100% { transform: rotate(-2deg); }
  50% { transform: rotate(2deg); }
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95) translateY(6px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes fadeOut {
  from { opacity: 1; transform: scale(1) translateY(0); }
  to { opacity: 0; transform: scale(0.95) translateY(6px); }
}
@keyframes fadeSlide {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes bounceSlow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-3px); }
}
.animate-wiggle { animation: wiggle 0.3s ease-in-out; }
.animate-fadeIn { animation: fadeIn 0.4s ease-out; }
.animate-fadeOut { animation: fadeOut 0.4s ease-in; }
.animate-fadeSlide { animation: fadeSlide 0.5s ease-out; }
.animate-bounceSlow { animation: bounceSlow 2s infinite; }
</style>
