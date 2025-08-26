<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue'
import {
  ChevronDown, Mail, MapPin, Phone, Loader2, CheckCircle2, ArrowRightCircle
} from 'lucide-vue-next'

// ====== STATE ======
const form = reactive({ name: '', email: '', phone: '', topic: 'General', message: '' })
const topics = ['General', 'Support', 'Partnership', 'Media', 'Other']
const errors = reactive({})
const loading = ref(false)
const showModal = ref(false)
const modalMessage = ref('')

// ====== VALIDATION ======
const validate = () => {
  errors.name = !form.name ? 'Nama diperlukan.' : ''
  errors.email = !form.email ? 'Email diperlukan.' : (/^\S+@\S+\.\S+$/.test(form.email) ? '' : 'Format email tidak valid.')
  errors.message = !form.message ? 'Pesan tidak boleh kosong.' : (form.message.length < 10 ? 'Pesan terlalu singkat (min 10 karakter).' : '')
  errors.phone = form.phone && !/^\+?[0-9\s-()-]{6,20}$/.test(form.phone) ? 'Nomor telepon tidak valid.' : ''
  return !errors.name && !errors.email && !errors.message && !errors.phone
}

// ====== SUBMIT DUMMY LOCALHOST ======
const submit = async () => {
  if (!validate()) return
  loading.value = true
  await new Promise(r => setTimeout(r, 1000)) // simulate request
  loading.value = false
  modalMessage.value = `localhost says: Pesan untuk "${form.topic}" berhasil dikirim. Terima kasih, ${form.name || 'pengguna'}.`
  showModal.value = true

  setTimeout(() => {
    form.name = ''
    form.email = ''
    form.phone = ''
    form.topic = 'General'
    form.message = ''
  }, 400)

  setTimeout(() => { showModal.value = false }, 3000)
}

// ====== SCROLL TO FORM ======
const scrollToForm = () => {
  const el = document.getElementById('contact-form')
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

// ====== TESTIMONIAL CAROUSEL ======
const testimonials = ref([
  { text: 'Support cepat dan membantu — solusinya jelas.', name: 'Rina K.', role: 'Penulis' },
  { text: 'Tim profesional, balasan dalam 24 jam.', name: 'Budi S.', role: 'Pembaca' },
  { text: 'Kemitraan mudah dan transparan.', name: 'Lina P.', role: 'Partner' },
])
const tIndex = ref(0)
let tTimer = null
onMounted(() => { tTimer = setInterval(() => { tIndex.value = (tIndex.value + 1) % testimonials.value.length }, 4500) })
onBeforeUnmount(() => { if (tTimer) clearInterval(tTimer) })

// ====== FAQ ======
const faqs = ref([
  { q: 'Berapa lama balasan biasanya?', a: 'Kami berusaha membalas dalam 24 jam pada hari kerja.' },
  { q: 'Apakah ada nomor darurat?', a: 'Nomor WhatsApp darurat: +62 812 9999 8888 (untuk situasi mendesak).' },
  { q: 'Bolehkah saya berkunjung ke kantor?', a: 'Silakan buat janji terlebih dahulu agar kami dapat menyambut Anda.' },
])

// ====== MICRO STATS ======
const stats = ref([
  { label: 'Balasan Rata-rata', value: '≤ 24 jam' },
  { label: 'Tiket Bulanan', value: '≈ 320' },
  { label: 'Rating Dukungan', value: '4.8/5' },
])
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Kontak — Tikeli</title>
      <meta name="description" content="Hubungi Tikeli — dukungan, kemitraan, media, dan pertanyaan umum. Form interaktif, FAQ, peta, dan testimoni." />
    </Head>

    <!-- HERO -->
    <section class="relative min-h-[64vh] flex items-center justify-center text-center overflow-hidden bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-600 text-white">
      <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)] [background-size:18px_18px]"></div>
      <div class="absolute inset-0 bg-black/20"></div>

      <div class="relative z-10 max-w-4xl px-6">
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
          Butuh Bantuan? <span class="text-yellow-300">Hubungi Kami</span>
        </h1>
        <p class="text-lg md:text-xl text-white/90 mb-6">
          Tim Tikeli siap membantu: dukungan teknis, kemitraan, atau pertanyaan umum.
        </p>

        <div class="flex items-center justify-center gap-3">
          <button @click="scrollToForm" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-yellow-400 text-gray-900 font-semibold hover:bg-yellow-500 transition">
            Kirim Pesan <ArrowRightCircle class="w-5 h-5" />
          </button>
          <a href="#faq" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition">Lihat FAQ</a>
        </div>
      </div>

      <button @click="scrollToForm" class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce bg-white/10 hover:bg-white/20 p-3 rounded-full">
        <ChevronDown class="w-6 h-6 text-white" />
      </button>

      <div class="pointer-events-none absolute -bottom-24 left-1/2 -translate-x-1/2 w-[700px] h-[700px] bg-cyan-400/20 rounded-full blur-3xl"></div>
    </section>

    <!-- CONTACT CARDS + STATS -->
    <section class="max-w-6xl mx-auto px-6 py-10 -mt-14">
        <br><br>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
          <div class="flex items-start gap-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
              <MapPin class="w-6 h-6 text-indigo-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Alamat</p>
              <p class="font-medium text-gray-900 dark:text-white">Jl. Merdeka No.123, Jakarta</p>
              <p class="text-sm text-gray-500 mt-2">Kunjungan hanya dengan janji temu.</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
          <div class="flex items-start gap-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
              <Mail class="w-6 h-6 text-indigo-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Email</p>
              <p class="font-medium text-gray-900 dark:text-white">support@tikeli.com</p>
              <p class="text-sm text-gray-500 mt-2">Sertakan detail dan contoh bila berkaitan dengan bug atau konten.</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
          <div class="flex items-start gap-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
              <Phone class="w-6 h-6 text-indigo-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Telepon</p>
              <p class="font-medium text-gray-900 dark:text-white">+62 812 3456 7890</p>
              <p class="text-sm text-gray-500 mt-2">Jam kerja: 09:00–17:00 WIB.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div v-for="s in stats" :key="s.label" class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow text-center">
          <p class="text-sm text-gray-500">{{ s.label }}</p>
          <p class="font-bold text-lg mt-1">{{ s.value }}</p>
        </div>
      </div>
    </section>

    <!-- FORM + ILLUSTRATION -->
    <section id="contact-form" class="max-w-6xl mx-auto px-6 py-12">
      <div class="grid md:grid-cols-2 gap-10 items-start">
    <!-- FORM CARD -->
<div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-lg">
  <h2 class="text-2xl font-bold mb-2">Kirim Pesan ke Tim Tikeli</h2>
  <p class="text-gray-600 dark:text-gray-400 mb-6">
    Isi formulir dan sebutkan topik agar pesan cepat ditangani.
  </p>

  <form @submit.prevent="submit" class="space-y-4">
    <!-- Nama -->
    <div>
      <label class="block text-sm font-medium mb-1">Nama</label>
      <input
        v-model="form.name"
        type="text"
        placeholder="Nama lengkap"
        class="w-full rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2
               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
               dark:bg-gray-800 dark:text-white placeholder-gray-400"
      />
      <p v-if="errors.name" class="text-sm text-rose-500 mt-1">{{ errors.name }}</p>
    </div>

    <!-- Email & Telepon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          placeholder="email@contoh.com"
          class="w-full rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2
                 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                 dark:bg-gray-800 dark:text-white placeholder-gray-400"
        />
        <p v-if="errors.email" class="text-sm text-rose-500 mt-1">{{ errors.email }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Telepon (opsional)</label>
        <input
          v-model="form.phone"
          type="tel"
          placeholder="+62812xxxx"
          class="w-full rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2
                 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                 dark:bg-gray-800 dark:text-white placeholder-gray-400"
        />
        <p v-if="errors.phone" class="text-sm text-rose-500 mt-1">{{ errors.phone }}</p>
      </div>
    </div>

    <!-- Topik -->
    <div>
      <label class="block text-sm font-medium mb-1">Topik</label>
      <select
        v-model="form.topic"
        class="w-full rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2
               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
               dark:bg-gray-800 dark:text-white"
      >
        <option v-for="t in topics" :key="t">{{ t }}</option>
      </select>
    </div>

    <!-- Pesan -->
    <div>
      <label class="block text-sm font-medium mb-1">Pesan</label>
      <textarea
        v-model="form.message"
        rows="6"
        placeholder="Tulis pesan Anda..."
        class="w-full rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2
               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
               dark:bg-gray-800 dark:text-white placeholder-gray-400"
      ></textarea>
      <p v-if="errors.message" class="text-sm text-rose-500 mt-1">{{ errors.message }}</p>
    </div>

    <!-- Tombol Submit -->
    <div class="flex items-center gap-3">
      <button
        :disabled="loading"
        type="submit"
        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700
               text-white rounded-lg font-medium shadow"
      >
        <span v-if="!loading">Kirim Pesan</span>
        <Loader2 v-if="loading" class="w-4 h-4 animate-spin" />
      </button>

      <div
        v-if="!loading && !showModal"
        class="text-sm text-gray-500"
      >
        Kami akan membalas secepatnya.
      </div>
      <div
        v-if="showModal"
        class="inline-flex items-center gap-2 text-green-600 font-medium"
      >
        <CheckCircle2 class="w-5 h-5" /> Terkirim
      </div>
    </div>
  </form>
</div>


        <!-- ILLUSTRATION + TESTIMONIAL -->
        <div class="space-y-6">
          <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-lg flex flex-col items-center text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/906/906334.png" alt="support illustration" class="w-52 mb-4" />
            <p class="text-gray-600 dark:text-gray-400">Untuk masalah teknis, sertakan URL atau screenshot agar kami dapat membantu lebih cepat.</p>
          </div>

          <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-semibold mb-3">Apa kata mereka</h3>
            <div class="relative min-h-[90px]">
              <transition name="fade-slide" mode="out-in">
                <div :key="tIndex" class="text-gray-700 dark:text-gray-200">
                  <p class="italic">"{{ testimonials[tIndex].text }}"</p>
                  <p class="mt-3 font-semibold">— {{ testimonials[tIndex].name }}, <span class="text-sm text-gray-500">{{ testimonials[tIndex].role }}</span></p>
                </div>
              </transition>

              <div class="absolute right-0 top-0 flex gap-2">
                <button @click="tIndex = (tIndex - 1 + testimonials.length) % testimonials.length" class="p-2 rounded-full bg-white/80 dark:bg-gray-800/60 hover:bg-white dark:hover:bg-gray-700 transition">‹</button>
                <button @click="tIndex = (tIndex + 1) % testimonials.length" class="p-2 rounded-full bg-white/80 dark:bg-gray-800/60 hover:bg-white dark:hover:bg-gray-700 transition">›</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ + MAP -->
    <section id="faq" class="max-w-6xl mx-auto px-6 py-12">
      <div class="grid md:grid-cols-2 gap-8">
        <!-- FAQ -->
        <div>
          <h2 class="text-2xl font-bold mb-4">Pertanyaan Umum</h2>
          <div class="space-y-3">
            <details v-for="(f, i) in faqs" :key="i" class="group bg-white dark:bg-gray-900 rounded-xl p-4 shadow hover:shadow-lg transition">
              <summary class="flex items-center justify-between cursor-pointer list-none">
                <span class="font-medium">{{ f.q }}</span>
                <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
              </summary>
              <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ f.a }}</p>
            </details>
          </div>
        </div>

        <!-- MAP -->
        <div class="rounded-xl overflow-hidden shadow-lg bg-white dark:bg-gray-900">
          <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <MapPin class="w-5 h-5 text-indigo-600" />
              <div>
                <p class="text-sm text-gray-500">Lokasi Kantor</p>
                <p class="font-medium">Jakarta, Indonesia</p>
              </div>
            </div>
            <a href="https://www.google.com/maps" target="_blank" class="text-sm text-indigo-600">Buka di Google Maps</a>
          </div>
          <iframe class="w-full h-80" loading="lazy" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d19843.123!2d106.84513!3d-6.20876!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3f8b3f8b3f9%3A0x1234567890abcdef!2sJakarta!5e0!3m2!1sen!2sid!4v1234567890" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </section>


    <!-- MODAL (dummy success) -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-xl max-w-md w-full text-center">
          <CheckCircle2 class="w-12 h-12 text-green-500 mx-auto mb-4" />
          <h3 class="text-lg font-semibold mb-2">Pesan Terkirim</h3>
          <p class="text-gray-600 dark:text-gray-300 mb-4">{{ modalMessage }}</p>
          <button @click="showModal = false" class="px-5 py-2 bg-indigo-600 text-white rounded-lg">Tutup</button>
        </div>
      </div>
    </transition>

    <!-- FOOTER NOTE -->
    <div class="max-w-6xl mx-auto px-6 py-8 text-center text-xs text-gray-500">
      <p>Untuk kebutuhan khusus (legal, enterprise, partnership), hubungi <a href="mailto:legal@tikeli.app" class="underline">legal@tikeli.app</a>.</p>
    </div>

  </PublicLayout>
</template>

<style>
@keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-up { animation: fadeUp .5s ease both; }

.fade-slide-enter-active, .fade-slide-leave-active { transition: all .35s ease; }
.fade-slide-enter-from { opacity: 0; transform: translateY(8px); }
.fade-slide-enter-to { opacity: 1; transform: translateY(0); }
.fade-slide-leave-from { opacity: 1; transform: translateY(0); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-6px); }

/* modal fade */
.fade-enter-active, .fade-leave-active { transition: opacity .25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.fade-enter-to, .fade-leave-from { opacity: 1; }
</style>
