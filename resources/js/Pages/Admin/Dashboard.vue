<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { ChartBarIcon, ClockIcon, UsersIcon, TagIcon, CheckCircleIcon } from '@heroicons/vue/24/solid'

// Props dari Laravel
const props = defineProps({
  statsData: Object,
  dailyApprovedData: Array,
  dailyUsersData: Array
})

// Statistik box data
const stats = ref([
  { title: 'Total Artikel', value: props.statsData.totalArtikel ?? 0, bg: 'from-blue-500 to-blue-600', icon: ChartBarIcon },
  { title: 'Pending Approval', value: props.statsData.pending ?? 0, bg: 'from-yellow-500 to-yellow-600', icon: ClockIcon },
  { title: 'Total User', value: props.statsData.totalUser ?? 0, bg: 'from-green-500 to-green-600', icon: UsersIcon },
  { title: 'Kategori', value: props.statsData.kategori ?? 0, bg: 'from-purple-500 to-purple-600', icon: TagIcon },
  { title: 'Approved Article', value: props.statsData.approved ?? 0, bg: 'from-teal-500 to-teal-600', icon: CheckCircleIcon }
])

// Import Chart.js
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

// Chart data
const chartData = ref({
  labels: [],
  datasets: [
    {
      label: 'Approved Article (15 Hari Terakhir)',
      borderColor: '#14b8a6',
      backgroundColor: 'rgba(20, 184, 166, 0.2)',
      fill: true,
      tension: 0.4,
      pointRadius: 5,
      pointHoverRadius: 7,
      data: []
    },
    {
      label: 'User Baru (15 Hari Terakhir)',
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.2)',
      fill: true,
      tension: 0.4,
      pointRadius: 5,
      pointHoverRadius: 7,
      data: []
    }
  ]
})

// Chart options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top', labels: { font: { size: 12 } } },
    title: {
      display: true,
      text: 'Statistik 15 Hari Terakhir',
      font: { size: 16, weight: 'bold' }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { stepSize: 1 }
    }
  }
}

// Sinkronisasi data chart
watch(
  [() => props.dailyApprovedData, () => props.dailyUsersData],
  ([approved, users]) => {
    if (!approved || !users) return

    chartData.value.labels = approved.map(item => item.tanggal)
    chartData.value.datasets[0].data = approved.map(item => item.totalApproved)
    chartData.value.datasets[1].data = users.map(item => item.totalUsers)
  },
  { immediate: true }
)
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">📊 Dashboard Admin</h2>
        <Link
          :href="route('guest.welcome')"
          class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
        >
          Lihat Beranda Artikel
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

      <!-- Statistik Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
  <div
    v-for="(stat, i) in stats"
    :key="i"
    class="bg-gradient-to-br text-white rounded-lg shadow-md p-4 flex items-center gap-3 transition transform hover:scale-[1.015]"
    :class="stat.bg"
  >
    <div class="p-2.5 bg-white/20 rounded-full">
      <component :is="stat.icon" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-xl font-bold">{{ stat.value }}</p>
      <p class="text-xs opacity-90">{{ stat.title }}</p>
    </div>
  </div>
</div>


        <!-- Grafik -->
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
          <div class="h-72">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
