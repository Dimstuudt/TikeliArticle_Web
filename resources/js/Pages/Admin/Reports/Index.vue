<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  reports: Object
})
</script>

<template>
  <AuthenticatedLayout>
<template #header>
  <!-- Wrapper full width -->
  <div class="w-full flex justify-between items-center bg-blue-100 border border-blue-200 rounded-lg px-4 py-3 shadow-sm">

    <!-- Judul -->
    <div class="flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg"
           class="h-6 w-6 text-blue-600"
           fill="none"
           viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.534 6.121 1.475M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <h2 class="text-lg sm:text-xl font-semibold text-blue-800 truncate">Laporan User</h2>
    </div>

    <!-- Tombol ke Artikel -->
    <Link
      :href="route('guest.welcome')"
      class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
    >
      📖 Lihat Artikel
    </Link>
  </div>
</template>


    <div class="py-6 max-w-6xl mx-auto">

      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full text-sm text-left border border-gray-200">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="px-4 py-3 border">#</th>
              <th class="px-4 py-3 border">Email</th>
              <th class="px-4 py-3 border">Laporan</th>
              <th class="px-4 py-3 border">Tanggal</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(report, index) in props.reports.data"
              :key="report.id"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-3 border text-center">
                {{ index + 1 }}
              </td>
              <td class="px-4 py-3 border">
                {{ report.email }}
              </td>
              <td class="px-4 py-3 border">
                {{ report.laporan }}
              </td>
              <td class="px-4 py-3 border">
                {{ new Date(report.created_at).toLocaleString() }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
