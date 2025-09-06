<script setup>
import { ref } from "vue"

const props = defineProps({
  show: Boolean
})

const emit = defineEmits(["close", "submit"])

const form = ref({
  name: "",
  laporan: ""
})

const isSubmitting = ref(false)
const isSuccess = ref(false)

const handleSubmit = async () => {
  if (isSubmitting.value) return
  isSubmitting.value = true

  // Simulasi delay
  await new Promise((r) => setTimeout(r, 1500))

  // Emit data ke parent
  emit("submit", { ...form.value })

  // Tampilkan sukses
  isSuccess.value = true
  isSubmitting.value = false

  // Reset form
  form.value = { name: "", laporan: "" }
}

// Tutup modal
const closeModal = () => {
  isSuccess.value = false
  emit("close")
}
</script>

<template>
  <!-- Overlay -->
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 flex items-center justify-center bg-black/70 z-50 backdrop-blur-sm"
      aria-modal="true"
      role="dialog"
      aria-labelledby="modal-title"
      aria-describedby="modal-description"
    >
      <!-- Modal -->
      <transition name="scale">
        <div
          class="relative max-w-4xl mx-4 grid grid-cols-12 gap-8 px-6 py-4 rounded-3xl shadow-2xl
                 bg-gradient-to-tr from-white via-indigo-50 to-indigo-100
                 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900
                 h-auto"
        >
          <!-- Close Button -->
          <button
            @click="closeModal"
            aria-label="Close modal"
            :disabled="isSubmitting"
            class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-2 z-30 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
              viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

        <!-- Left Side: Mascot -->
<div class="col-span-5 flex items-start justify-center relative -top-10">
  <transition name="fade">
    <img
      :src="!isSuccess ? '/mascot.png' : '/mascot2.png'"
      alt="Mascot"
      :class="[
        'w-70 h-90 object-contain drop-shadow-[0_25px_25px_rgba(0,0,0,0.25)] rounded-3xl transform transition-transform duration-500 hover:scale-105',
        isSuccess ? 'translate-x-8' : ''  // 👉 geser kanan kalau mascot2
      ]"
      draggable="false"
    />
  </transition>
  <div class="absolute inset-0 pointer-events-none animate-float"></div>
</div>

          <!-- Right Side -->
          <div class="col-span-7 flex flex-col justify-center">
            <transition name="slide-fade" mode="out-in">
              <!-- Form -->
              <div v-if="!isSuccess" key="form">
                <h2
                  id="modal-title"
                  class="text-4xl font-extrabold text-gray-900 dark:text-white mb-6 select-none"
                >
                  Ada apa nih?
                </h2>

                <p
                  id="modal-description"
                  class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed"
                >
                  Silakan isi nama dan laporkan apa yang ingin kamu sampaikan. Kami siap mendengarkan!
                </p>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                  <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                      Nama
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      placeholder="Masukkan nama lengkapmu"
                      required
                      autocomplete="name"
                      :disabled="isSubmitting"
                      class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-3 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    />
                  </div>

                  <div>
                    <label for="laporan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                      Laporan
                    </label>
                    <textarea
                      id="laporan"
                      v-model="form.laporan"
                      placeholder="Tulis laporanmu di sini..."
                      required
                      rows="6"
                      :disabled="isSubmitting"
                      class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-3 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm resize-none focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    ></textarea>
                  </div>

                  <div class="flex justify-end">
                    <button
                      type="submit"
                      :disabled="isSubmitting"
                      class="inline-flex items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-800 text-white font-semibold px-8 py-3 shadow-lg transition transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <template v-if="isSubmitting">
                        <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Mengirim...
                      </template>
                      <template v-else>
                        SUBMIT
                      </template>
                    </button>
                  </div>
                </form>
              </div>

             <!-- Success -->
<div v-else key="success" class="flex flex-col items-center justify-center space-y-6 py-10 select-none">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-green-500 drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
  </svg>
  <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white pl-4">
    Laporan berhasil dikirim!
  </h3>
  <p class="text-gray-600 dark:text-gray-300 max-w-md text-center pl-4">
    Terima kasih atas laporannya. Kami akan segera menindaklanjuti.
  </p>
  <button
    @click="closeModal"
    class="mt-4 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition"
  >
    OK
  </button>
</div>

            </transition>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<style scoped>
/* Fade overlay and mascot fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Scale modal */
.scale-enter-active,
.scale-leave-active {
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.scale-enter-from {
  transform: scale(0.85);
  opacity: 0;
}
.scale-leave-to {
  transform: scale(0.85);
  opacity: 0;
}

/* Slide + fade content (form and success) */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: opacity 0.35s ease, transform 0.35s ease;
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Scrollbar for textarea */
textarea::-webkit-scrollbar {
  width: 8px;
}
textarea::-webkit-scrollbar-track {
  background: transparent;
}
textarea::-webkit-scrollbar-thumb {
  background-color: rgba(99, 102, 241, 0.5); /* indigo-500 */
  border-radius: 4px;
  border: 2px solid transparent;
  background-clip: content-box;
}
.dark textarea::-webkit-scrollbar-thumb {
  background-color: rgba(139, 92, 246, 0.7); /* indigo-400 */
}

/* Input and textarea focus ring enhancement */
input:focus,
textarea:focus {
  outline-offset: 2px;
}

/* Button active state */
button:active {
  transform: scale(0.95);
  transition-duration: 100ms;
}

/* Floating animation for mascot */
@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
}
.animate-float {
  animation: float 4s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .col-span-5 {
    grid-column: span 12 / span 12;
  }
  .col-span-7 {
    grid-column: span 12 / span 12;
  }
  .w-70 {
    width: 17.5rem; /* 280px */
  }
  .h-90 {
    height: 22.5rem; /* 360px */
  }
  .p-10 {
    padding: 2rem /* 32px */;
  }
  h2 {
    font-size: 2.25rem /* 36px */;
  }
}
</style>
