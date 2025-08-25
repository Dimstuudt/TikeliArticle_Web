<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="relative min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-100 via-blue-50 to-white overflow-hidden">

    <!-- Background bubbles -->
    <div class="absolute inset-0">
      <div v-for="i in 10" :key="i" class="bubble"></div>
    </div>

    <!-- Card Container -->
    <div class="relative w-full max-w-md bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl animate-fade-in z-10">
      <!-- Logo SVG -->
      <div class="flex justify-center mb-6">
        <Link href="/">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"
               class="h-20 w-20 transition-transform transform hover:scale-110 hover:-rotate-3">
            <rect width="64" height="64" rx="12" fill="#2563EB"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
                  font-size="18" font-family="Arial, sans-serif" fill="white" font-weight="bold">
              Tikeli
            </text>
          </svg>
        </Link>
      </div>

      <slot />
    </div>
  </div>
</template>

<style>
/* Fade-in card */
@keyframes fadeInSlide {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeInSlide 0.8s ease-out forwards;
}

/* Bubble Animations */
@keyframes floatUp {
  0% { transform: translateY(100vh) scale(0.5); opacity: 0.6; }
  50% { opacity: 1; }
  100% { transform: translateY(-20vh) scale(1); opacity: 0; }
}

.bubble {
  position: absolute;
  bottom: -50px;
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 9999px;
  background: rgba(59, 130, 246, 0.3); /* soft blue */
  animation: floatUp 8s ease-in infinite;
}

/* Randomize bubbles */
.bubble:nth-child(1) { left: 10%; animation-duration: 6s; animation-delay: 0s; }
.bubble:nth-child(2) { left: 20%; animation-duration: 9s; animation-delay: 1s; }
.bubble:nth-child(3) { left: 35%; animation-duration: 7s; animation-delay: 2s; }
.bubble:nth-child(4) { left: 50%; animation-duration: 8s; animation-delay: 3s; }
.bubble:nth-child(5) { left: 60%; animation-duration: 10s; animation-delay: 4s; }
.bubble:nth-child(6) { left: 70%; animation-duration: 9s; animation-delay: 1.5s; }
.bubble:nth-child(7) { left: 80%; animation-duration: 7s; animation-delay: 2.5s; }
.bubble:nth-child(8) { left: 30%; animation-duration: 11s; animation-delay: 0.5s; }
.bubble:nth-child(9) { left: 40%; animation-duration: 12s; animation-delay: 1.2s; }
.bubble:nth-child(10){ left: 90%; animation-duration: 8s; animation-delay: 3.8s; }
</style>
