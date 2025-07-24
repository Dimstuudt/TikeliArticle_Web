<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';


</script>

<template>
  <div class="min-h-screen w-full flex items-center justify-center bg-gradient-to-b from-blue-100 via-white to-white">
    <div class="w-full max-w-md bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-xl">
      <div class="flex justify-center mb-6">
        <Link href="/">
          <ApplicationLogo class="h-16 w-16 text-blue-600" />
        </Link>
      </div>

      <slot />
    </div>
  </div>
</template>
