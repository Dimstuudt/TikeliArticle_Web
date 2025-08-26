<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import {
  Users, BookOpen, Layers, CheckCircle, Star, Target, Mail, Search, ChevronUp
} from 'lucide-vue-next'

// ====== STATE & DATA ======
const title = 'Tentang Tikeli'
const subtitle = 'Platform berbagi artikel untuk menyalurkan ide, opini, dan informasi bermanfaat.'

const stats = ref([
  { id: 'articles', label: 'Artikel', value: 10000, display: 0 },
  { id: 'authors', label: 'Penulis', value: 5000, display: 0 },
  { id: 'categories', label: 'Kategori', value: 50, display: 0 },
  { id: 'readers', label: 'Pembaca', value: 1000000, display: 0 },
])

const team = ref([
  { id: 1, name: 'Dimas Sandy', role: 'Founder & CEO', avatar: 'https://i.pravatar.cc/150?img=12', bio: 'Memimpin visi produk dan strategi pertumbuhan.' },
  { id: 2, name: 'Andi Wijaya', role: 'CTO', avatar: 'https://i.pravatar.cc/150?img=32', bio: 'Bertanggung jawab atas arsitektur teknis dan platform engineering.' },
  { id: 3, name: 'Sarah Putri', role: 'Marketing Lead', avatar: 'https://i.pravatar.cc/150?img=47', bio: 'Menjalankan strategi pemasaran dan pengembangan komunitas.' },
  { id: 4, name: 'Rina Oktaviani', role: 'Head of Content', avatar: 'https://i.pravatar.cc/150?img=8', bio: 'Mengelola kualitas editorial dan kurasi penulis.' },
  { id: 5, name: 'Fajar Nugroho', role: 'Product Designer', avatar: 'https://i.pravatar.cc/150?img=20', bio: 'Mendesain pengalaman pengguna yang sederhana dan inklusif.' },
])

const filters = ref(['All', 'Leadership', 'Engineering', 'Marketing', 'Editorial', 'Design'])
const activeFilter = ref('All')

const revealMap = ref({})
let observer = null

// helper: count up animation
const animateStats = () => {
  stats.value.forEach((s) => {
    const duration = 1200
    const start = performance.now()
    const from = 0
    const to = s.value
    const step = (now) => {
      const t = Math.min(1, (now - start) / duration)
      s.display = Math.floor(from + (to - from) * easeOutCubic(t))
      if (t < 1) requestAnimationFrame(step)
      else s.display = to
    }
    requestAnimationFrame(step)
  })
}

const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3)

// IntersectionObserver to reveal elements
const setupObserver = () => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const id = entry.target.getAttribute('data-reveal-id')
      if (!id) return
      if (entry.isIntersecting) {
        revealMap.value[id] = true
        if (id === 'stats') animateStats()
      }
    })
  }, { threshold: 0.18 })

  // observe elements
  const nodes = document.querySelectorAll('[data-reveal-id]')
  nodes.forEach((n) => observer.observe(n))
}

onMounted(() => {
  setupObserver()
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
})

const filteredTeam = computed(() => {
  if (activeFilter.value === 'All') return team.value
  const map = {
    Leadership: ['Founder & CEO', 'CTO', 'Marketing Lead'],
    Engineering: ['CTO'],
    Marketing: ['Marketing Lead'],
    Editorial: ['Head of Content'],
    Design: ['Product Designer'],
  }
  const roles = map[activeFilter.value] || []
  return team.value.filter((m) => roles.includes(m.role))
})

// smooth scroll to section
const scrollTo = (id) => {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <PublicLayout>
    <Head>
      <title>{{ title }}</title>
      <meta name="description" content="Tentang Tikeli — visi, misi, nilai, tim, dan statistik. Bergabunglah dengan komunitas penulis dan pembaca." />
    </Head>

    <!-- HERO -->
    <section class="relative overflow-hidden py-20 px-6 text-center bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600">
      <div class="absolute inset-0 opacity-18 bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)] [background-size:20px_20px]" />
      <div class="absolute inset-0 bg-black/20"></div>

      <div class="relative z-10 max-w-4xl mx-auto text-white">
        <div class="inline-flex items-center gap-3 px-3 py-1 rounded-full bg-white/10 backdrop-blur mb-4 animate-pop">
          <Users class="w-4 h-4" /> Tentang Kami
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight animate-fade-up">{{ title.replace('Tikeli', '') }}<span class="text-yellow-300">Tikeli</span></h1>
        <p class="text-lg md:text-xl leading-relaxed max-w-3xl mx-auto animate-fade-up [animation-delay:120ms]">{{ subtitle }}</p>
        <div class="mt-6 flex items-center justify-center gap-3 animate-fade-up [animation-delay:220ms]">
          <a href="/register" class="inline-block px-6 py-3 rounded-xl font-semibold shadow bg-yellow-400 hover:bg-yellow-500 text-gray-900 transition hover:scale-105">Mulai Menulis</a>
          <button @click="scrollTo('vision')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white">Lihat Visi & Misi</button>
        </div>
      </div>

      <div class="pointer-events-none absolute -bottom-20 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-cyan-400/20 rounded-full blur-3xl"></div>
    </section>

    <!-- VISION & MISSION -->
    <section id="vision" class="max-w-5xl mx-auto px-6 py-16 text-center" data-reveal-id="vision">
      <div class="animate-fade-up">
        <h2 class="text-3xl font-bold mb-8">Visi & Misi</h2>
        <div class="grid md:grid-cols-2 gap-8">
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <Target class="w-12 h-12 text-blue-600 mx-auto mb-4" />
            <h3 class="text-xl font-semibold mb-2">Visi</h3>
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Menjadi platform terdepan dalam berbagi pengetahuan, inspirasi, dan kreativitas melalui tulisan.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <CheckCircle class="w-12 h-12 text-green-600 mx-auto mb-4" />
            <h3 class="text-xl font-semibold mb-2">Misi</h3>
            <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-left">
              <li>✔ Memberikan ruang untuk semua penulis.</li>
              <li>✔ Menyediakan konten yang relevan dan bermanfaat.</li>
              <li>✔ Menghubungkan pembaca dengan berbagai perspektif.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- CORE VALUES -->
    <section class="bg-gray-50 dark:bg-gray-900 py-16 px-6" data-reveal-id="values">
      <div class="max-w-5xl mx-auto text-center animate-fade-up">
        <h2 class="text-3xl font-bold mb-8">Nilai Utama Kami</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <Star class="w-10 h-10 text-yellow-500 mx-auto mb-4" />
            <h3 class="font-semibold text-lg mb-2">Kualitas</h3>
            <p class="text-gray-600 dark:text-gray-300">Setiap artikel melewati kurasi agar tetap relevan dan inspiratif.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <Users class="w-10 h-10 text-blue-500 mx-auto mb-4" />
            <h3 class="font-semibold text-lg mb-2">Komunitas</h3>
            <p class="text-gray-600 dark:text-gray-300">Kami membangun ruang inklusif untuk semua penulis dan pembaca.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <BookOpen class="w-10 h-10 text-green-500 mx-auto mb-4" />
            <h3 class="font-semibold text-lg mb-2">Pembelajaran</h3>
            <p class="text-gray-600 dark:text-gray-300">Setiap artikel adalah peluang untuk belajar dan berkembang.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- STATISTICS -->
    <section class="max-w-3xl mx-auto px-6 py-16 text-center" data-reveal-id="stats">
      <h2 class="text-3xl font-bold mb-12 animate-fade-up">Sekilas Angka</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div v-for="s in stats" :key="s.id" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex flex-col items-center animate-fade-up">
          <div class="mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h5l2-2h7a2 2 0 012 2v14a2 2 0 01-2 2z" />
            </svg>
          </div>
          <p class="text-3xl font-bold text-blue-600">{{ s.display.toLocaleString() }}{{ s.id === 'readers' ? '+' : '' }}</p>
          <p class="text-gray-600 dark:text-gray-300">{{ s.label }}</p>
        </div>
      </div>
    </section>

   <!-- TEAM -->
<section class="bg-gray-50 dark:bg-gray-900 py-20 px-6" data-reveal-id="team">
  <div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12">
      <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Tim Kami</h2>
      <div class="flex gap-3 mt-4 md:mt-0">
        <button
          v-for="f in filters"
          :key="f"
          @click="activeFilter = f"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-colors',
            activeFilter === f
              ? 'bg-indigo-600 text-white'
              : 'bg-white/70 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
          ]"
        >
          {{ f }}
        </button>
      </div>
    </div>

    <!-- Grid Team -->
    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="(m, index) in filteredTeam"
        :key="m.id"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 transform transition duration-300 hover:-translate-y-2 hover:shadow-xl animate-fade-in"
        :style="{ animationDelay: `${index * 100}ms` }"
      >
        <!-- Avatar -->
        <div class="flex flex-col items-center text-center">
          <img
            :src="m.avatar"
            :alt="m.name"
            class="w-24 h-24 rounded-full object-cover ring-4 ring-indigo-500/20 mb-4"
          />
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ m.name }}</h3>
          <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mt-1">{{ m.role }}</span>
          <p class="text-gray-600 dark:text-gray-300 mt-3 text-sm leading-relaxed line-clamp-3">{{ m.bio }}</p>

          <!-- Social Links -->
          <div class="flex gap-4 mt-4">
            <a v-for="s in m.socials" :key="s.icon" :href="s.url" target="_blank" rel="noopener" class="text-gray-400 hover:text-indigo-500 transition">
              <component :is="s.icon" class="w-5 h-5" />
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


    <!-- WHY JOIN -->
    <section class="max-w-5xl mx-auto px-6 py-16" data-reveal-id="why">
      <div class="grid md:grid-cols-2 gap-8 items-center">
        <div class="animate-fade-up">
          <h2 class="text-3xl font-bold mb-4">Kenapa Bergabung?</h2>
          <p class="text-gray-600 dark:text-gray-300 mb-6">Bergabung di Tikeli artinya mendapatkan audiens, dukungan komunitas, dan tools untuk mengasah kemampuan menulis Anda.</p>

          <ul class="space-y-3">
            <li class="flex items-start gap-3"><CheckCircle class="w-5 h-5 text-green-500 mt-1"/> Publikasi mudah dan cepat.</li>
            <li class="flex items-start gap-3"><CheckCircle class="w-5 h-5 text-green-500 mt-1"/> Dashboard penulis yang intuitif.</li>
            <li class="flex items-start gap-3"><CheckCircle class="w-5 h-5 text-green-500 mt-1"/> Komunitas yang saling menopang.</li>
          </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md animate-pop">
          <h3 class="font-semibold mb-2">Fitur Unggulan</h3>
          <div class="grid gap-3">
            <div class="flex items-start gap-3"><Layers class="w-5 h-5 text-indigo-600 mt-1"/><div><strong>Editor kaya fitur</strong><div class="text-sm text-gray-500">Editor WYSIWYG yang mendukung gambar, embed, dan format rapi.</div></div></div>
            <div class="flex items-start gap-3"><Star class="w-5 h-5 text-yellow-500 mt-1"/><div><strong>Kurikulum kurasi</strong><div class="text-sm text-gray-500">Program kurasi membantu artikel terbaik ditemukan pembaca.</div></div></div>
            <div class="flex items-start gap-3"><Target class="w-5 h-5 text-blue-500 mt-1"/><div><strong>Analitik  sederhana</strong><div class="text-sm text-gray-500">Laporan metrik pembaca untuk membantu peningkatan kualitas.</div></div></div>
          </div>
        </div>
      </div>
    </section>

    <!-- CONTACT CTA -->
    <section class="relative overflow-hidden py-16 px-6 text-center bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600 text-white">
      <div class="absolute inset-0 opacity-18 bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)] [background-size:20px_20px]"></div>
      <div class="absolute inset-0 bg-black/20"></div>

      <div class="relative z-10 max-w-3xl mx-auto animate-fade-up">
        <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Mari Bergabung dengan <span class="text-yellow-300">Tikeli</span></h2>
        <p class="text-gray-100 mb-6">Jadilah bagian dari komunitas kami dan bagikan ide-ide hebatmu.</p>
        <a href="/register" class="inline-block px-8 py-3 rounded-xl font-semibold shadow-md bg-yellow-400 hover:bg-yellow-500 text-gray-900 transition-all duration-300 hover:scale-105 hover:shadow-lg">Mulai Menulis</a>
      </div>
    </section>

    <!-- FOOTER NOTE -->
    <div class="max-w-6xl mx-auto px-6 py-8 text-center text-xs text-gray-500">
      <p>Dokumen ini adalah representasi singkat tentang Tikeli. Untuk peluang kerja sama, sponsorship, atau pertanyaan hukum, hubungi <a href="mailto:legal@tikeli.app" class="underline">legal@tikeli.app</a>.</p>
    </div>

    <!-- back to top -->
    <button @click="scrollTo('vision')" class="fixed bottom-6 right-6 rounded-full shadow-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-3 hover:scale-105 transition" aria-label="Back to top">
      <ChevronUp class="w-5 h-5" />
    </button>
  </PublicLayout>
</template>

<style>
@keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pop { 0% { transform: scale(.96); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
.animate-fade-up { animation: fadeUp .5s ease both; }
.animate-pop { animation: pop .4s cubic-bezier(.2,.8,.2,1) both; }

/* responsive tweaks */
@media (min-width: 768px) {
  .animate-fade-up[animation-delay\:120ms] { animation-delay: .12s; }
}

/* subtle pattern helper (optional) */
.bg-pattern { background-image: radial-gradient(circle, rgba(255,255,255,0.2) 1px, transparent 1px); background-size: 20px 20px; }
</style>
