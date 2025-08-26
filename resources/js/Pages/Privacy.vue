<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
  Shield, Cookie, Users, Database, Bell, FileWarning, Link as LinkIcon,
  RefreshCw, Mail, ChevronDown, ChevronUp, CheckCircle2, Info
} from 'lucide-vue-next'

// accordion states
const open = ref({
  cookies: true,
  rights: true,
  thirdparty: false,
  retention: false,
})

// anchor list for TOC
const sections = [
  { id: 'intro', label: 'Pendahuluan' },
  { id: 'collect', label: 'Informasi yang Dikumpulkan' },
  { id: 'use', label: 'Penggunaan Data' },
  { id: 'cookies', label: 'Cookies' },
  { id: 'thirdparty', label: 'Pihak Ketiga' },
  { id: 'rights', label: 'Hak Pengguna' },
  { id: 'security', label: 'Keamanan Data' },
  { id: 'retention', label: 'Retensi & Penghapusan' },
  { id: 'changes', label: 'Perubahan Kebijakan' },
  { id: 'contact', label: 'Kontak' },
]

const lastUpdated = '26 Agustus 2025'

const scrollToId = (id) => {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <PublicLayout>
    <Head title="Kebijakan Privasi" />

    <!-- ===== HERO ===== -->
    <section
      class="relative overflow-hidden py-24 px-6 text-center bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600"
    >
      <!-- pattern dots -->
      <div
        class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)] [background-size:20px_20px]"
      />
      <!-- soft overlay -->
      <div class="absolute inset-0 bg-black/20" />

      <div class="relative z-10 max-w-5xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur text-white text-sm mb-4 animate-pop">
          <Shield class="w-4 h-4" /> Kebijakan Privasi
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-white tracking-tight animate-fade-up">
          Kebijakan <span class="text-yellow-300">Privasi</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-100 leading-relaxed max-w-3xl mx-auto animate-fade-up [animation-delay:120ms]">
          Bagaimana <strong>Tikeli</strong> mengumpulkan, menggunakan, dan melindungi data Anda—secara
          transparan dan bertanggung jawab.
        </p>

        <div class="mt-6 flex items-center justify-center gap-3 animate-fade-up [animation-delay:220ms]">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-400 text-gray-900 text-sm font-semibold shadow">
            <RefreshCw class="w-4 h-4" /> Last updated: {{ lastUpdated }}
          </span>
          <span class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-sm">
            <Info class="w-4 h-4" /> Berlaku untuk semua pengguna Tikeli
          </span>
        </div>
      </div>

      <!-- gradient blur blobs -->
      <div class="pointer-events-none absolute -bottom-20 left-1/2 -translate-x-1/2 w-[700px] h-[700px] bg-cyan-400/20 rounded-full blur-3xl"></div>
    </section>

    <!-- ===== CONTENT WRAPPER ===== -->
    <section class="max-w-6xl mx-auto px-6 py-14">
      <div class="grid lg:grid-cols-[260px,1fr] gap-8">
        <!-- ==== TOC SIDEBAR ==== -->
        <aside
          class="lg:sticky lg:top-24 h-max rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/70 dark:bg-gray-900/60 backdrop-blur p-4 hidden lg:block"
        >
          <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">Daftar Isi</p>
          <nav class="space-y-1">
            <button
              v-for="s in sections"
              :key="s.id"
              @click="scrollToId(s.id)"
              class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-sm transition"
            >
              {{ s.label }}
            </button>
          </nav>
        </aside>

        <!-- ==== MAIN ==== -->
        <div class="space-y-10">

          <!-- Intro -->
          <section id="intro" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <Shield class="w-6 h-6 text-blue-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">1. Pendahuluan</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Kami berkomitmen melindungi privasi Anda. Kebijakan ini menjelaskan jenis data yang kami kumpulkan,
                  tujuan penggunaannya, dasar pemrosesan, dan pilihan yang Anda miliki terkait data tersebut.
                  Dengan menggunakan Tikeli, Anda dianggap menyetujui ketentuan dalam kebijakan ini.
                </p>
              </div>
            </div>
          </section>

          <!-- Collect -->
          <section id="collect" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <Database class="w-6 h-6 text-indigo-600 shrink-0" />
              <div class="w-full">
                <h2 class="text-2xl font-bold mb-2">2. Informasi yang Dikumpulkan</h2>
                <div class="grid md:grid-cols-2 gap-4">
                  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <p class="font-semibold mb-2">Data yang Anda berikan</p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-1">
                      <li>Data akun (nama, email, password).</li>
                      <li>Profil (bio, foto, tautan).</li>
                      <li>Konten (artikel, komentar, lampiran).</li>
                    </ul>
                  </div>
                  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <p class="font-semibold mb-2">Data yang kami kumpulkan otomatis</p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-1">
                      <li>Data perangkat & log (IP, browser, OS, waktu akses).</li>
                      <li>Data interaksi (lihat/like/simpan artikel).</li>
                      <li>Cookie & teknologi serupa (lihat bagian Cookies).</li>
                    </ul>
                  </div>
                </div>
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                  Catatan: Kami <span class="font-semibold">tidak</span> meminta data sensitif (KTP, finansial) kecuali diwajibkan hukum.
                </div>
              </div>
            </div>
          </section>

          <!-- Use -->
          <section id="use" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <Users class="w-6 h-6 text-emerald-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">3. Penggunaan Data</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-3">
                  Kami menggunakan data untuk:
                </p>
                <ul class="grid sm:grid-cols-2 gap-3">
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2" /> Menyediakan fitur inti (akun, publikasi, komentar).
                  </li>
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2" /> Keamanan & pencegahan penyalahgunaan.
                  </li>
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2" /> Pemberitahuan penting terkait akun & moderasi.
                  </li>
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2" /> Analitik untuk meningkatkan layanan.
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Cookies (Accordion) -->
          <section id="cookies" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button
              class="w-full flex items-center justify-between px-6 py-4"
              @click="open.cookies = !open.cookies"
            >
              <div class="flex items-center gap-3">
                <Cookie class="w-6 h-6 text-amber-600" />
                <h2 class="text-2xl font-bold">4. Cookies</h2>
              </div>
              <component :is="open.cookies ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.cookies" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Kami menggunakan cookies untuk autentikasi sesi, menyimpan preferensi, dan analitik trafik.
                Anda dapat menonaktifkan cookies di pengaturan browser, namun beberapa fitur mungkin tidak optimal.
              </p>
              <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                <p class="font-semibold mb-1">Jenis cookies</p>
                <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-1">
                  <li>Essential: login & keamanan.</li>
                  <li>Functional: preferensi tampilan (tema gelap/terang).</li>
                  <li>Analytics: pemahaman penggunaan fitur.</li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Third party (Accordion) -->
          <section id="thirdparty" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.thirdparty = !open.thirdparty">
              <div class="flex items-center gap-3">
                <LinkIcon class="w-6 h-6 text-cyan-600" />
                <h2 class="text-2xl font-bold">5. Pihak Ketiga</h2>
              </div>
              <component :is="open.thirdparty ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.thirdparty" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Kami dapat menggunakan layanan seperti login sosial atau analitik yang memiliki kebijakan privasi masing-masing.
                Kami hanya berbagi data minimal yang dibutuhkan untuk menjalankan fitur tersebut.
              </p>
              <div class="text-sm text-gray-500 dark:text-gray-400">
                Disarankan meninjau kebijakan dari pihak ketiga yang Anda gunakan.
              </div>
            </div>
          </section>

          <!-- Rights (Accordion) -->
          <section id="rights" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.rights = !open.rights">
              <div class="flex items-center gap-3">
                <Bell class="w-6 h-6 text-emerald-600" />
                <h2 class="text-2xl font-bold">6. Hak Pengguna</h2>
              </div>
              <component :is="open.rights ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.rights" class="px-6 pb-6 space-y-3">
              <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                <li>Akses & koreksi data pribadi Anda.</li>
                <li>Penghapusan akun dan data (kecuali yang diwajibkan disimpan oleh hukum).</li>
                <li>Menonaktifkan komunikasi non-esensial (newsletter, promosi).</li>
                <li>Keberatan atas pemrosesan tertentu sesuai peraturan yang berlaku.</li>
              </ul>
              <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 text-sm text-gray-600 dark:text-gray-300">
                Untuk menjalankan hak-hak ini, lihat bagian <a href="#contact" class="underline">Kontak</a>.
              </div>
            </div>
          </section>

          <!-- Security -->
          <section id="security" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <FileWarning class="w-6 h-6 text-rose-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">7. Keamanan Data</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Kami menerapkan praktik keamanan yang wajar (enkripsi transit, kontrol akses, audit log).
                  Namun, tidak ada metode transmisi di internet yang 100% aman. Kami mendorong penggunaan password kuat
                  dan autentikasi dua langkah jika tersedia.
                </p>
              </div>
            </div>
          </section>

          <!-- Retention -->
          <section id="retention" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.retention = !open.retention">
              <div class="flex items-center gap-3">
                <RefreshCw class="w-6 h-6 text-violet-600" />
                <h2 class="text-2xl font-bold">8. Retensi & Penghapusan</h2>
              </div>
              <component :is="open.retention ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.retention" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Kami menyimpan data selama diperlukan untuk tujuan yang dijelaskan dalam kebijakan ini atau sebagaimana
                diwajibkan hukum. Anda dapat mengajukan penghapusan data; beberapa log agregat mungkin dipertahankan
                untuk keperluan keamanan/akuntabilitas.
              </p>
            </div>
          </section>

          <!-- Changes -->
          <section id="changes" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <RefreshCw class="w-6 h-6 text-yellow-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">9. Perubahan Kebijakan</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Kebijakan ini dapat direvisi sewaktu-waktu. Versi terbaru akan dipublikasikan di halaman ini dengan
                  penanda tanggal. Jika perubahan material dilakukan, kami akan mengirim pemberitahuan melalui email atau banner in-app.
                </p>
              </div>
            </div>
          </section>

          <!-- Contact -->
          <section id="contact" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <Mail class="w-6 h-6 text-sky-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">10. Kontak</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Pertanyaan atau permintaan terkait data pribadi? Hubungi kami:
                </p>
                <div class="mt-3 grid sm:grid-cols-2 gap-3">
                  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                    <a href="mailto:privacy@tikeli.app" class="font-medium underline">privacy@tikeli.app</a>
                  </div>
                  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Formulir</p>
                    <a href="/contact" class="font-medium underline">/contact</a>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- CTA -->
          <section
            class="relative overflow-hidden rounded-2xl p-8 text-center bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600 text-white animate-fade-up"
          >
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)] [background-size:20px_20px]" />
            <div class="relative z-10 max-w-2xl mx-auto">
              <h3 class="text-2xl md:text-3xl font-extrabold mb-3">
                Kendalikan data Anda di <span class="text-yellow-300">Tikeli</span>
              </h3>
              <p class="text-gray-100 mb-6">
                Masuk untuk mengelola preferensi privasi, unduh data, atau ajukan penghapusan akun.
              </p>
              <a
                href="/login"
                class="inline-block px-6 py-3 rounded-xl font-semibold shadow bg-yellow-400 hover:bg-yellow-500 text-gray-900 transition hover:scale-105 hover:shadow-lg"
              >
                Kelola Privasi
              </a>
            </div>
          </section>

          <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
            Dokumen ini disediakan sebagai referensi umum dan bukan nasihat hukum.
          </p>
        </div>
      </div>
    </section>

    <!-- Back to top -->
    <button
      @click="scrollToId('intro')"
      class="fixed bottom-6 right-6 rounded-full shadow-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-3 hover:scale-105 transition"
      aria-label="Back to top"
    >
      <ChevronUp class="w-5 h-5" />
    </button>
  </PublicLayout>
</template>

<style>
/* simple entrance animations (no external libs) */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes pop {
  0% { transform: scale(.96); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-fade-up { animation: fadeUp .5s ease both; }
.animate-pop { animation: pop .4s cubic-bezier(.2,.8,.2,1) both; }
</style>
