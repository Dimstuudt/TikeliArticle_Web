<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { DocumentTextIcon, CheckCircleIcon, ClockIcon, XCircleIcon, EyeIcon } from '@heroicons/vue/24/solid'

// Props dari Laravel
const props = defineProps({
  statsData: Object,        // { totalArtikel, approved, pending, rejected, totalViews }
  dailyCreatedData: Array,  // [{ tanggal, totalCreated }]
  dailyApprovedData: Array  // [{ tanggal, totalApproved }]
})

// Statistik Cards
const stats = ref([
  { title: 'Total Artikel', value: props.statsData.totalArtikel ?? 0, bg: 'from-blue-500 to-blue-600', icon: DocumentTextIcon },
  { title: 'Disetujui', value: props.statsData.approved ?? 0, bg: 'from-green-500 to-green-600', icon: CheckCircleIcon },
  { title: 'Pending Review', value: props.statsData.pending ?? 0, bg: 'from-yellow-500 to-yellow-600', icon: ClockIcon },
  { title: 'Ditolak', value: props.statsData.rejected ?? 0, bg: 'from-red-500 to-red-600', icon: XCircleIcon },
  { title: 'Total Views', value: props.statsData.totalViews ?? 0, bg: 'from-purple-500 to-purple-600', icon: EyeIcon }
])

// Chart.js & vue-chartjs setup
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

// Reactive chart data
const chartData = ref({
  labels: [],
  datasets: [
    {
      label: 'Artikel Dibuat (15 Hari Terakhir)',
      borderColor: 'rgba(37,99,235,0.9)',
      backgroundColor: 'rgba(37,99,235,0.4)',
      tension: 0.3,
      fill: true,
      data: []
    },
    {
      label: 'Artikel Disetujui (15 Hari Terakhir)',
      borderColor: 'rgba(34,197,94,0.9)',
      backgroundColor: 'rgba(34,197,94,0.4)',
      tension: 0.3,
      fill: true,
      data: []
    }
  ]
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top' }
  }
}

// Update chart data saat props berubah
watch(
  [() => props.dailyCreatedData, () => props.dailyApprovedData],
  ([created, approved]) => {
    if (!created || !approved) return

    chartData.value.labels = created.map(item => item.tanggal)
    chartData.value.datasets[0].data = created.map(item => item.totalCreated)
    chartData.value.datasets[1].data = approved.map(item => item.totalApproved)
  },
  { immediate: true }
)
</script>

<template>
  <Head title="Dashboard Operator" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Dashboard Operator</h2>
        <Link
          :href="route('guest.welcome')"
          class="inline-block bg-blue-600 text-white px-4 py-2 text-sm font-medium rounded hover:bg-blue-700 transition"
        >
          Lihat Beranda
        </Link>
      </div>
    </template>

    <div class="py-12">
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

        <!-- Chart -->
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">
            Artikel Dibuat & Disetujui (15 Hari Terakhir)
          </h3>
          <div class="h-72">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Fast Action -->
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
