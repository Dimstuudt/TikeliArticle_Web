<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

// form state
const form = ref({
  name: '',
  email: '',
  message: ''
})

const submit = () => {
  alert(
    `Pesan terkirim!\n\nNama: ${form.value.name}\nEmail: ${form.value.email}\nPesan: ${form.value.message}`
  )
}
</script>

<template>
  <PublicLayout>
    <Head title="Kontak Kami" />

      <!-- Hero Section: Hubungi -->
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
      Hubungi Tim <span class="text-yellow-300">Kami</span>
    </h1>
    <p class="text-lg md:text-xl text-gray-100 leading-relaxed">
      Punya pertanyaan atau butuh bantuan? Tim kami siap membantu Anda.
    </p>
  </div>
</section>

    <br>

    <!-- Info Kontak -->
    <section class="max-w-6xl mx-auto py-16 px-6 grid md:grid-cols-3 gap-8">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 text-center hover:shadow-lg transition">
        <div class="text-blue-600 dark:text-blue-400 text-3xl mb-4">📍</div>
        <h3 class="font-bold text-lg mb-2">Alamat</h3>
        <p class="text-gray-600 dark:text-gray-300">
          Jl. Merdeka No.123<br />Jakarta, Indonesia
        </p>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 text-center hover:shadow-lg transition">
        <div class="text-blue-600 dark:text-blue-400 text-3xl mb-4">📧</div>
        <h3 class="font-bold text-lg mb-2">Email</h3>
        <p class="text-gray-600 dark:text-gray-300">support@tikeli.com</p>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 text-center hover:shadow-lg transition">
        <div class="text-blue-600 dark:text-blue-400 text-3xl mb-4">📞</div>
        <h3 class="font-bold text-lg mb-2">Telepon</h3>
        <p class="text-gray-600 dark:text-gray-300">+62 812 3456 7890</p>
      </div>
    </section>
    <br>

    <!-- Form Section -->
    <section class="bg-gray-50 dark:bg-gray-900 py-20 px-6">
      <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-10">
        <!-- Left Side -->
        <div class="flex flex-col justify-center">
          <h2 class="text-3xl font-bold mb-4">Kirim Pesan</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            Isi form berikut untuk menghubungi tim kami. Kami akan membalas secepat mungkin.
          </p>

          <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
            <div>
              <label class="block text-sm font-semibold mb-1">Nama</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700 px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
                placeholder="Nama lengkap"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-semibold mb-1">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700 px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
                placeholder="email@contoh.com"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-semibold mb-1">Pesan</label>
              <textarea
                v-model="form.message"
                rows="5"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700 px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
                placeholder="Tulis pesan Anda..."
                required
              ></textarea>
            </div>

            <button
              type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition font-semibold"
            >
              Kirim Pesan
            </button>
          </form>
        </div>

        <!-- Right Side (Ilustrasi) -->
        <div class="flex justify-center items-center">
          <img
            src="https://cdn-icons-png.flaticon.com/512/906/906334.png"
            alt="Contact Illustration"
            class="w-80 h-auto animate-fade-up"
          />
        </div>
      </div>
    </section>

   <!-- FAQ + Maps -->
<section class="max-w-6xl mx-auto py-16 px-6 grid md:grid-cols-2 gap-8 items-stretch">
  <!-- FAQ -->
  <div class="flex flex-col">
    <h2 class="text-3xl font-bold mb-6">Pertanyaan Umum</h2>
    <div class="space-y-4 flex-1">
      <!-- Item -->
      <details class="group bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 cursor-pointer">
        <summary class="flex justify-between items-center font-medium text-lg text-gray-800 dark:text-gray-100">
          Berapa lama biasanya balasan email?
          <span class="transition-transform group-open:rotate-180">▼</span>
        </summary>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
          Tim kami berusaha membalas dalam waktu maksimal 24 jam pada hari kerja.
        </p>
      </details>

      <details class="group bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 cursor-pointer">
        <summary class="flex justify-between items-center font-medium text-lg text-gray-800 dark:text-gray-100">
          Apakah ada nomor darurat?
          <span class="transition-transform group-open:rotate-180">▼</span>
        </summary>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
          Ya, untuk kebutuhan mendesak Anda bisa hubungi WhatsApp <b>+62 812 9999 8888</b>.
        </p>
      </details>

      <details class="group bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 cursor-pointer">
        <summary class="flex justify-between items-center font-medium text-lg text-gray-800 dark:text-gray-100">
          Apakah saya bisa bertemu langsung di kantor?
          <span class="transition-transform group-open:rotate-180">▼</span>
        </summary>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
          Tentu, silakan buat janji temu dulu agar kami bisa melayani lebih baik.
        </p>
      </details>
    </div>
  </div>

  <!-- Map -->
  <div class="rounded-xl overflow-hidden shadow-md flex">
    <iframe
      class="w-full h-full min-h-[400px]"
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d19843.123!2d106.84513!3d-6.20876!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3f8b3f8b3f9%3A0x1234567890abcdef!2sJakarta!5e0!3m2!1sen!2sid!4v1234567890"
      allowfullscreen=""
      loading="lazy">
    </iframe>
  </div>
</section>

  </PublicLayout>
</template>
