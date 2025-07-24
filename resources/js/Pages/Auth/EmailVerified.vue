<template>
  <GuestLayout>
    <Head title="Email Verified" />

    <div class="text-center mt-10">
      <h1 class="text-2xl font-bold text-green-600 mb-4">Email Berhasil Diverifikasi 🎉</h1>
      <p class="mb-6">Akun kamu sekarang sudah aktif. Kamu bisa lanjut ke dashboard.</p>
      <Link
        href="/dashboard"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Masuk ke Dashboard
      </Link>
    </div>
  </GuestLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
</script>
