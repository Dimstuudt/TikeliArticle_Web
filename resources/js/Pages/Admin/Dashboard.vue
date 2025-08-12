<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { ChartBarIcon, ClockIcon, UsersIcon, TagIcon, CheckCircleIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  statsData: Object,
  dailyApprovedData: Array
})


const stats = ref([
  { title: 'Total Artikel', value: props.statsData.totalArtikel ?? 0, bg: 'bg-gradient-to-r from-blue-500 to-blue-700', icon: ChartBarIcon },
  { title: 'Pending Approval', value: props.statsData.pending ?? 0, bg: 'bg-gradient-to-r from-yellow-500 to-yellow-700', icon: ClockIcon },
  { title: 'Total User', value: props.statsData.totalUser ?? 0, bg: 'bg-gradient-to-r from-green-500 to-green-700', icon: UsersIcon },
  { title: 'Kategori', value: props.statsData.kategori ?? 0, bg: 'bg-gradient-to-r from-purple-500 to-purple-700', icon: TagIcon },
  { title: 'Total Approved Article', value: props.statsData.approved ?? 0, bg: 'bg-gradient-to-r from-teal-500 to-teal-700', icon: CheckCircleIcon }
])


// Chart.js import
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

// Chart Data
const chartData = ref({
  labels: [],
  datasets: [
    {
      label: 'Approved Article (15 Hari Terakhir)',
      borderColor: '#14b8a6',
      backgroundColor: 'rgba(20, 184, 166, 0.2)',
      fill: true,
      tension: 0.4,
      pointRadius: 4,
      pointHoverRadius: 6,
      data: []
    }
  ]
})

// Chart Options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false, // biar tinggi container ngaruh
  plugins: {
    legend: { position: 'top' },
    title: { display: true, text: 'Approved Article (15 Hari Terakhir)' }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { stepSize: 1 }
    }
  }
}

// Watch perubahan data dari props
watch(() => props.dailyApprovedData, (newVal) => {
  if (!newVal) return
  chartData.value.labels = newVal.map(item => item.tanggal)
  chartData.value.datasets[0].data = newVal.map(item => item.totalApproved)
}, { immediate: true })
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Dashboard Admin
        </h2>
        <Link
          :href="route('guest.welcome')"
          class="inline-block bg-blue-600 text-white px-4 py-2 text-sm font-medium rounded hover:bg-blue-700 transition"
        >
          Lihat Beranda Artikel
        </Link>
      </div>
    </template>



    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

        <!-- Statistik -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
  <div
    v-for="(stat, i) in stats"
    :key="i"
    class="rounded-lg shadow p-4 text-white flex items-center gap-3"
    :class="stat.bg"
  >
    <div class="p-2 bg-white/20 rounded-full">
      <component :is="stat.icon" class="w-6 h-6" />
    </div>
    <div>
      <p class="text-lg font-bold">{{ stat.value }}</p>
      <p class="text-xs opacity-90">{{ stat.title }}</p>
    </div>
  </div>
</div>



        <!-- Info Box -->
        <div class="bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            Anda berhasil login sebagai admin.
          </div>
        </div>

        <!-- Grafik Line Chart -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
          <div class="h-64"> <!-- Ukuran lebih kecil -->
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </div>


      </div>
    </div>
  </AuthenticatedLayout>
</template>
