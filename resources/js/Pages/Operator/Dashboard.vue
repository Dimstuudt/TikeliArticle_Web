<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

// Import chart.js & vue-chartjs
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'
import { Line } from 'vue-chartjs'

// register chartjs component
ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

// Dummy labels 15 hari terakhir
const labels = [
  'Day 1','Day 2','Day 3','Day 4','Day 5',
  'Day 6','Day 7','Day 8','Day 9','Day 10',
  'Day 11','Day 12','Day 13','Day 14','Day 15'
]

// Dummy data
const chartData = {
  labels,
  datasets: [
    {
      label: 'Artikel Dibuat',
      data: [2, 3, 1, 4, 2, 5, 1, 0, 3, 2, 4, 1, 2, 3, 5],
      borderColor: 'rgba(37, 99, 235, 0.9)',
      backgroundColor: 'rgba(37, 99, 235, 0.4)',
      tension: 0.3,
      fill: true,
    },
    {
      label: 'Artikel Disetujui',
      data: [1, 2, 0, 3, 1, 4, 1, 0, 2, 1, 3, 1, 1, 2, 4],
      borderColor: 'rgba(34, 197, 94, 0.9)',
      backgroundColor: 'rgba(34, 197, 94, 0.4)',
      tension: 0.3,
      fill: true,
    },
  ],
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
  },
}
</script>

<template>
  <Head title="Dashboard Operator" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Dashboard Operator
        </h2>

        <!-- Tombol ke Halaman Beranda -->
        <Link
          href="/welcome"
          class="inline-block bg-blue-600 text-white px-4 py-2 text-sm font-medium rounded hover:bg-blue-700 transition"
        >
          Lihat Beranda
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 grid grid-cols-1 gap-6">

        <!-- Card Info -->
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
          <p class="text-gray-900">✅ Anda berhasil login sebagai operator.</p>
        </div>

        <!-- Chart Created vs Approved -->
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">
            Artikel Dibuat & Disetujui (15 Hari Terakhir)
          </h3>
          <div class="h-72">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Fast link -->
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Aksi Cepat</h3>
          <Link
            :href="route('operator.articles.create')"
            class="inline-block bg-green-600 text-white px-5 py-2 text-sm font-medium rounded hover:bg-green-700 transition"
          >
            ➕ Buat Artikel Baru
          </Link>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
