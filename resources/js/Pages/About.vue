<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Users, BookOpen, Layers, CheckCircle, Star, Target } from 'lucide-vue-next'
</script>

<template>
  <PublicLayout>
    <Head title="Tentang Kami" />

  <!-- Hero Section -->
<section class="relative overflow-hidden py-20 px-6 text-center
               bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600">

  <!-- Ornamen titik / pattern -->
  <div class="absolute inset-0 opacity-20
              bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)]
              [background-size:20px_20px]"></div>

  <!-- Overlay gradasi tipis -->
  <div class="absolute inset-0 bg-black/20"></div>

  <div class="relative z-10 max-w-4xl mx-auto">
    <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-white tracking-tight">
      Tentang <span class="text-yellow-300">Tikeli</span>
    </h1>
    <p class="text-lg md:text-xl text-gray-100 leading-relaxed">
      Platform berbagi artikel untuk menyalurkan ide, opini, dan informasi bermanfaat.
    </p>
  </div>
</section>

    <!-- Visi Misi -->
    <section class="max-w-5xl mx-auto px-6 py-16 text-center">
        <br>
      <h2 class="text-3xl font-bold mb-8">Visi & Misi</h2>
      <div class="grid md:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
          <Target class="w-12 h-12 text-blue-600 mx-auto mb-4" />
          <h3 class="text-xl font-semibold mb-2">Visi</h3>
          <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
            Menjadi platform terdepan dalam berbagi pengetahuan, inspirasi, dan kreativitas melalui tulisan.
          </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
          <CheckCircle class="w-12 h-12 text-green-600 mx-auto mb-4" />
          <h3 class="text-xl font-semibold mb-2">Misi</h3>
          <ul class="text-gray-600 dark:text-gray-300 space-y-2">
            <li>✔ Memberikan ruang untuk semua penulis.</li>
            <li>✔ Menyediakan konten yang relevan dan bermanfaat.</li>
            <li>✔ Menghubungkan pembaca dengan berbagai perspektif.</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Core Values -->
    <section class="bg-gray-50 dark:bg-gray-900 py-16 px-6">
      <div class="max-w-5xl mx-auto text-center">
        <br>
        <h2 class="text-3xl font-bold mb-8">Nilai Utama Kami</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <Star class="w-10 h-10 text-yellow-500 mx-auto mb-4" />
            <h3 class="font-semibold text-lg mb-2">Kualitas</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Setiap artikel melewati kurasi agar tetap relevan dan inspiratif.
            </p>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <Users class="w-10 h-10 text-blue-500 mx-auto mb-4" />
            <h3 class="font-semibold text-lg mb-2">Komunitas</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Kami membangun ruang inklusif untuk semua penulis dan pembaca.
            </p>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <BookOpen class="w-10 h-10 text-green-500 mx-auto mb-4" />
            <h3 class="font-semibold text-lg mb-2">Pembelajaran</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Setiap artikel adalah peluang untuk belajar dan berkembang.
            </p>
          </div>
        </div>
      </div>
    </section>

<!-- Statistik -->
<section class="max-w-3xl mx-auto px-6 py-16 text-center">
  <h2 class="text-3xl font-bold mb-12">Sekilas Angka</h2>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex flex-col items-center">
      <!-- Ikon Artikel -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-blue-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h5l2-2h7a2 2 0 012 2v14a2 2 0 01-2 2z" />
      </svg>
      <p class="text-3xl font-bold text-blue-600">10K+</p>
      <p class="text-gray-600 dark:text-gray-300">Artikel</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex flex-col items-center">
      <!-- Ikon Penulis -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8 8 0 1112 20v-2a6 6 0 00-6-6H4a2 2 0 00-2 2v1c0 .552.224 1.053.586 1.414l2.535 2.39z" />
      </svg>
      <p class="text-3xl font-bold text-green-600">5K+</p>
      <p class="text-gray-600 dark:text-gray-300">Penulis</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex flex-col items-center">
      <!-- Ikon Kategori -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-yellow-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
      </svg>
      <p class="text-3xl font-bold text-yellow-600">50+</p>
      <p class="text-gray-600 dark:text-gray-300">Kategori</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex flex-col items-center">
      <!-- Ikon Pembaca -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-purple-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-8 0v2m8 0H9" />
      </svg>
      <p class="text-3xl font-bold text-purple-600">1M+</p>
      <p class="text-gray-600 dark:text-gray-300">Pembaca</p>
    </div>
  </div>
</section>

<!-- Tim -->
<section class="bg-gray-50 dark:bg-gray-900 py-16 px-6">
  <div class="max-w-6xl mx-auto text-center">
    <h2 class="text-3xl font-bold mb-8">Tim Kami</h2>
    <div class="grid md:grid-cols-3 gap-6">

      <!-- CEO -->
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
        <img src="https://i.pravatar.cc/150?img=12" class="w-24 h-24 rounded-full mx-auto mb-4" alt="CEO">
        <h3 class="font-semibold text-lg">Dimas Sandy</h3>
        <p class="text-gray-500">Founder & CEO</p>
      </div>

      <!-- CTO -->
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
        <img src="https://i.pravatar.cc/150?img=32" class="w-24 h-24 rounded-full mx-auto mb-4" alt="CTO">
        <h3 class="font-semibold text-lg">Andi Wijaya</h3>
        <p class="text-gray-500">CTO</p>
      </div>

      <!-- CMO -->
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
        <img src="https://i.pravatar.cc/150?img=47" class="w-24 h-24 rounded-full mx-auto mb-4" alt="CMO">
        <h3 class="font-semibold text-lg">Sarah Putri</h3>
        <p class="text-gray-500">Marketing Lead</p>
      </div>

    </div>
  </div>
</section>

<br>

   <!-- CTA -->
<section
  class="relative overflow-hidden py-20 px-6 text-center
         bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600">

  <!-- Ornamen titik / pattern -->
  <div class="absolute inset-0 opacity-20
              bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)]
              [background-size:20px_20px]"></div>

  <!-- Overlay gradasi tipis biar lembut -->
  <div class="absolute inset-0 bg-black/20"></div>

  <div class="relative z-10 max-w-2xl mx-auto">
    <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-white tracking-tight">
      Mari Bergabung dengan <span class="text-yellow-300">Tikeli</span>
    </h2>

    <p class="text-gray-100 mb-6">
      Jadilah bagian dari komunitas kami dan bagikan ide-ide hebatmu.
    </p>

    <a href="/register"
       class="inline-block px-8 py-3 rounded-xl font-semibold shadow-md
              bg-yellow-400 hover:bg-yellow-500
              text-gray-900 transition-all duration-300
              hover:scale-105 hover:shadow-lg">
      Mulai Menulis
    </a>
  </div>
</section>


</PublicLayout>
</template>

<style>
/* dummy pattern bg */
.bg-pattern {
  background-image: radial-gradient(circle, rgba(255,255,255,0.2) 1px, transparent 1px);
  background-size: 20px 20px;
}
</style>
