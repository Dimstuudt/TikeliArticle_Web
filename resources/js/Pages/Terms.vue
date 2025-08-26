<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import {
  Shield, Scale, FileText, UserPlus, Users, ClipboardCheck, Ban, Link as LinkIcon,
  FileWarning, AlertTriangle, DollarSign, RefreshCw, Mail, Info, Gavel, ChevronDown, ChevronUp, CheckCircle2
} from 'lucide-vue-next'

// ====== STATE ======
const open = ref({
  definitions: true,
  eligibility: false,
  account: false,
  content: false,
  ip: false,
  payments: false,
  prohibited: false,
  moderation: false,
  warranties: false,
  liability: false,
  indemnity: false,
  law: false,
  changes: false,
})

const lastUpdated = '26 Agustus 2025'

const sections = [
  { id: 'intro', label: 'Pendahuluan' },
  { id: 'definitions', label: 'Definisi' },
  { id: 'eligibility', label: 'Kelayakan Pengguna' },
  { id: 'account', label: 'Akun & Keamanan' },
  { id: 'user-obligations', label: 'Kewajiban Pengguna' },
  { id: 'content', label: 'Konten & Lisensi' },
  { id: 'ip', label: 'Hak Kekayaan Intelektual' },
  { id: 'payments', label: 'Pembayaran & Langganan' },
  { id: 'prohibited', label: 'Larangan Penggunaan' },
  { id: 'moderation', label: 'Moderasi, Penangguhan & Penghapusan' },
  { id: 'warranties', label: 'Pernyataan & Jaminan' },
  { id: 'liability', label: 'Batas Tanggung Jawab' },
  { id: 'indemnity', label: 'Ganti Rugi' },
  { id: 'law', label: 'Hukum yang Berlaku & Sengketa' },
  { id: 'changes', label: 'Perubahan Ketentuan' },
  { id: 'contact', label: 'Kontak' },
]

const activeId = ref('intro')

const scrollToId = (id) => {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

let observer
onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          activeId.value = entry.target.id
        }
      })
    },
    { rootMargin: '-40% 0px -55% 0px', threshold: [0, 0.25, 0.5, 1] }
  )
  sections.forEach((s) => {
    const el = document.getElementById(s.id)
    if (el) observer.observe(el)
  })
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
})
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Syarat & Ketentuan — Tikeli</title>
      <meta name="description" content="Syarat dan Ketentuan penggunaan layanan Tikeli: hak dan kewajiban pengguna, kebijakan konten, pembayaran, pembatasan, penyelesaian sengketa, dan kontak dukungan." />
    </Head>

    <!-- ===== HERO ===== -->
    <section class="relative overflow-hidden py-24 px-6 text-center bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600">
      <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)] [background-size:20px_20px]" />
      <div class="absolute inset-0 bg-black/20" />

      <div class="relative z-10 max-w-5xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur text-white text-sm mb-4 animate-pop">
          <Scale class="w-4 h-4" /> Syarat & Ketentuan
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-white tracking-tight animate-fade-up">
          Syarat & <span class="text-yellow-300">Ketentuan</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-100 leading-relaxed max-w-3xl mx-auto animate-fade-up [animation-delay:120ms]">
          Ketentuan hukum yang mengatur akses dan penggunaan <strong>Tikeli</strong>. Baca dengan saksama karena ini
          menjadi dasar hubungan Anda dengan kami saat menggunakan fitur, konten, maupun layanan terkait.
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

      <div class="pointer-events-none absolute -bottom-20 left-1/2 -translate-x-1/2 w-[700px] h-[700px] bg-cyan-400/20 rounded-full blur-3xl"></div>
    </section>

    <!-- ===== CONTENT WRAPPER ===== -->
    <section class="max-w-6xl mx-auto px-6 py-14">
      <div class="grid lg:grid-cols-[260px,1fr] gap-8">
        <!-- TOC -->
        <aside class="lg:sticky lg:top-24 h-max rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/70 dark:bg-gray-900/60 backdrop-blur p-4 hidden lg:block">
          <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">Daftar Isi</p>
          <nav class="space-y-1">
            <button
              v-for="s in sections"
              :key="s.id"
              @click="scrollToId(s.id)"
              class="w-full text-left px-3 py-2 rounded-lg text-sm transition"
              :class="activeId === s.id ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-200 font-medium' : 'hover:bg-gray-100 dark:hover:bg-gray-800'"
              :aria-current="activeId === s.id ? 'true' : 'false'"
            >
              {{ s.label }}
            </button>
          </nav>
        </aside>

        <!-- MAIN -->
        <div class="space-y-10">
          <!-- Intro -->
          <section id="intro" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <FileText class="w-6 h-6 text-blue-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">1. Pendahuluan</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Dokumen Syarat & Ketentuan ini ("Ketentuan") adalah perjanjian yang mengikat antara Anda dan
                  <strong>Tikeli</strong> terkait akses serta penggunaan situs, aplikasi, produk, dan layanan kami
                  (secara bersama-sama disebut "Layanan"). Dengan membuat akun, mengakses, atau menggunakan Layanan,
                  Anda menyatakan telah membaca, memahami, dan setuju untuk terikat pada Ketentuan ini beserta kebijakan
                  yang dirujuk, termasuk Kebijakan Privasi. Jika Anda tidak menyetujui Ketentuan, Anda tidak boleh
                  menggunakan Layanan.
                </p>
              </div>
            </div>
          </section>

          <!-- Definitions (Accordion) -->
          <section id="definitions" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.definitions = !open.definitions" :aria-expanded="open.definitions">
              <div class="flex items-center gap-3">
                <ClipboardCheck class="w-6 h-6 text-emerald-600" />
                <h2 class="text-2xl font-bold">2. Definisi</h2>
              </div>
              <component :is="open.definitions ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.definitions" class="px-6 pb-6 space-y-3 transition-[height]">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Kecuali dinyatakan lain, istilah berikut memiliki arti: "Pengguna" berarti individu atau badan hukum yang
                mengakses Layanan; "Konten" berarti teks, gambar, audio, video, kode, tautan, atau materi lain yang
                diunggah, diposting, dikirim, disimpan, dibagikan, atau ditampilkan melalui Layanan; "Perjanjian"
                berarti Ketentuan ini dan kebijakan terkait; "Hari Kerja" berarti Senin–Jumat tidak termasuk hari libur
                nasional; "Wilayah" berarti yurisdiksi tempat Anda berdomisili atau menggunakan Layanan.
              </p>
            </div>
          </section>

          <!-- Eligibility (Accordion) -->
          <section id="eligibility" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.eligibility = !open.eligibility" :aria-expanded="open.eligibility">
              <div class="flex items-center gap-3">
                <UserPlus class="w-6 h-6 text-sky-600" />
                <h2 class="text-2xl font-bold">3. Kelayakan Pengguna</h2>
              </div>
              <component :is="open.eligibility ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.eligibility" class="px-6 pb-6 space-y-3">
              <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                <li>Anda berusia minimal 13 tahun (atau usia minimum lain yang ditetapkan hukum di wilayah Anda) dan, jika di bawah usia mayoritas, menggunakan Layanan dengan persetujuan orang tua/wali.</li>
                <li>Jika bertindak untuk badan usaha, Anda menyatakan berwenang mewakili dan mengikat badan usaha tersebut pada Perjanjian ini.</li>
                <li>Anda tidak termasuk dalam daftar yang dilarang menerima layanan digital berdasarkan sanksi atau peraturan yang berlaku.</li>
              </ul>
            </div>
          </section>

          <!-- Account & Security (Accordion) -->
          <section id="account" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.account = !open.account" :aria-expanded="open.account">
              <div class="flex items-center gap-3">
                <Users class="w-6 h-6 text-indigo-600" />
                <h2 class="text-2xl font-bold">4. Akun & Keamanan</h2>
              </div>
              <component :is="open.account ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.account" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Untuk menggunakan fitur tertentu, Anda harus membuat akun dan memberikan informasi yang akurat,
                lengkap, serta mutakhir. Anda bertanggung jawab menjaga kerahasiaan kredensial dan semua aktivitas
                yang terjadi di bawah akun Anda. Segera beri tahu kami jika menduga adanya akses tidak sah.
              </p>
              <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                <li>Dilarang membagi kata sandi atau mengizinkan pihak ketiga menggunakan akun Anda.</li>
                <li>Kami dapat meminta verifikasi identitas untuk tujuan keamanan atau kepatuhan hukum.</li>
                <li>Kami berhak menangguhkan atau menutup akun yang melanggar Ketentuan atau menimbulkan risiko.</li>
              </ul>
            </div>
          </section>

          <!-- User Obligations (static) -->
          <section id="user-obligations" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <Shield class="w-6 h-6 text-emerald-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">5. Kewajiban Pengguna</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-3">
                  Saat menggunakan Layanan, Anda setuju untuk bertindak secara bertanggung jawab, menghormati hak orang
                  lain, dan mematuhi hukum yang berlaku. Anda bertanggung jawab atas Konten yang Anda unggah atau
                  tindakan yang Anda lakukan melalui Layanan.
                </p>
                <ul class="grid sm:grid-cols-2 gap-3">
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4"><CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2"/> Gunakan data yang akurat dan perbarui bila ada perubahan.</li>
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4"><CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2"/> Hormati HKI, privasi, dan hak moral pihak lain.</li>
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4"><CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2"/> Patuhi pedoman komunitas dan kebijakan moderasi.</li>
                  <li class="rounded-xl border border-gray-200 dark:border-gray-800 p-4"><CheckCircle2 class="w-5 h-5 inline -mt-1 mr-2"/> Amankan perangkat dan jaringan yang Anda gunakan untuk mengakses Layanan.</li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Content & License (Accordion) -->
          <section id="content" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.content = !open.content" :aria-expanded="open.content">
              <div class="flex items-center gap-3">
                <LinkIcon class="w-6 h-6 text-cyan-600" />
                <h2 class="text-2xl font-bold">6. Konten & Lisensi</h2>
              </div>
              <component :is="open.content ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.content" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Anda memiliki hak atas Konten yang Anda buat dan unggah. Dengan mengirimkan Konten ke Layanan, Anda
                memberi <strong>Tikeli</strong> lisensi non-eksklusif, berlaku global, bebas royalti, dapat
                disublicensikan dan dialihkan, untuk menggunakan, mereproduksi, meng-host, menyimpan, mengubah,
                menerbitkan, mengkomunikasikan, dan menampilkan Konten semata-mata untuk mengoperasikan, mempromosikan,
                dan meningkatkan Layanan. Lisensi berakhir bila Konten dihapus, kecuali sejauh Konten telah dibagikan,
                digunakan untuk kepentingan penegakan kebijakan, atau diwajibkan penyimpanannya oleh hukum.
              </p>
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Anda menyatakan dan menjamin bahwa Anda memiliki semua hak yang diperlukan untuk memberikan lisensi
                tersebut dan bahwa Konten tidak melanggar hukum atau hak pihak mana pun.
              </p>
            </div>
          </section>

          <!-- IP (Accordion) -->
          <section id="ip" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.ip = !open.ip" :aria-expanded="open.ip">
              <div class="flex items-center gap-3">
                <Gavel class="w-6 h-6 text-violet-600" />
                <h2 class="text-2xl font-bold">7. Hak Kekayaan Intelektual</h2>
              </div>
              <component :is="open.ip ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.ip" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Layanan beserta seluruh konten yang disediakan oleh <strong>Tikeli</strong> (termasuk namun tidak
                terbatas pada desain, antarmuka, perangkat lunak, basis data, logo, dan merek dagang) dilindungi oleh
                hukum HKI. Kecuali diizinkan secara tegas, Anda tidak boleh menyalin, memodifikasi, membuat karya
                turunan, mendistribusikan, menjual, menyewakan, atau mengeksploitasi bagian mana pun dari Layanan.
              </p>
            </div>
          </section>

          <!-- Payments (Accordion) -->
          <section id="payments" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.payments = !open.payments" :aria-expanded="open.payments">
              <div class="flex items-center gap-3">
                <DollarSign class="w-6 h-6 text-amber-600" />
                <h2 class="text-2xl font-bold">8. Pembayaran & Langganan</h2>
              </div>
              <component :is="open.payments ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.payments" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Beberapa fitur mungkin berbayar ("Paket Berbayar"). Biaya, pajak, siklus penagihan, dan kebijakan
                pembatalan ditampilkan pada saat pembelian. Kecuali dinyatakan lain, biaya tidak dapat dikembalikan dan
                langganan diperpanjang otomatis hingga Anda membatalkan melalui pengaturan akun.
              </p>
              <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                <li>Kami dapat mengubah harga dan fitur dengan pemberitahuan sebelumnya.</li>
                <li>Pajak, biaya bank, atau biaya pihak ketiga ditanggung pengguna.</li>
                <li>Kegagalan penagihan dapat menyebabkan penghentian akses ke Paket Berbayar.</li>
              </ul>
            </div>
          </section>

          <!-- Prohibited (Accordion) -->
          <section id="prohibited" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.prohibited = !open.prohibited" :aria-expanded="open.prohibited">
              <div class="flex items-center gap-3">
                <Ban class="w-6 h-6 text-rose-600" />
                <h2 class="text-2xl font-bold">9. Larangan Penggunaan</h2>
              </div>
              <component :is="open.prohibited ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.prohibited" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Tanpa membatasi hal lain dalam Ketentuan, Anda dilarang:</p>
              <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                <li>Mengunggah Konten yang melanggar hukum, memfitnah, pornografi eksplisit, kebencian, kekerasan, atau yang melanggar hak pihak lain.</li>
                <li>Mengelabui, menipu, phising, atau mengumpulkan kredensial, data pribadi, atau pembayaran secara tidak sah.</li>
                <li>Melakukan reverse engineering, scraping berlebihan, crawling tak wajar, atau serangan keamanan (DDoS, injeksi, brute force).</li>
                <li>Mengunggah malware, kode berbahaya, atau mengganggu integritas/ ketersediaan Layanan.</li>
                <li>Menggunakan Layanan untuk spam, promosi agresif, atau skema piramida.</li>
                <li>Menyalahgunakan sistem pelaporan, upvote/downvote, atau metrik komunitas.</li>
              </ul>
            </div>
          </section>

          <!-- Moderation & Suspension (Accordion) -->
          <section id="moderation" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.moderation = !open.moderation" :aria-expanded="open.moderation">
              <div class="flex items-center gap-3">
                <FileWarning class="w-6 h-6 text-orange-600" />
                <h2 class="text-2xl font-bold">10. Moderasi, Penangguhan & Penghapusan</h2>
              </div>
              <component :is="open.moderation ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.moderation" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Kami berhak meninjau, menandai, menurunkan, membatasi visibilitas, menangguhkan, atau menghapus Konten
                dan/atau akun berdasarkan kebijakan internal, laporan pengguna, atau kewajiban hukum. Kami berupaya
                memberi pemberitahuan bila memungkinkan, namun hal ini dapat dilakukan tanpa pemberitahuan untuk menjaga
                keamanan dan kepatuhan.
              </p>
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Jika akun ditangguhkan/ditutup, Anda tetap bertanggung jawab atas kewajiban yang timbul sebelum tanggal
                penangguhan/penutupan. Kami dapat menyimpan catatan yang diperlukan untuk tujuan hukum, audit, dan
                keamanan sesuai Kebijakan Privasi.
              </p>
            </div>
          </section>

          <!-- Warranties (Accordion) -->
          <section id="warranties" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.warranties = !open.warranties" :aria-expanded="open.warranties">
              <div class="flex items-center gap-3">
                <AlertTriangle class="w-6 h-6 text-yellow-600" />
                <h2 class="text-2xl font-bold">11. Pernyataan & Jaminan</h2>
              </div>
              <component :is="open.warranties ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.warranties" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                LAYANAN DISEDIAKAN "SEBAGAIMANA ADANYA" DAN "SEBAGAIMANA TERSEDIA" TANPA JAMINAN APA PUN, BAIK YANG
                TERSURAT MAUPUN TERSIRAT, TERMASUK NAMUN TIDAK TERBATAS PADA JAMINAN KELAYAKAN DIPERDAGANGKAN, KECUKUPAN
                UNTUK TUJUAN TERTENTU, ATAU TANPA PELANGGARAN. Kami tidak menyatakan atau menjamin bahwa Layanan akan
                bebas dari kesalahan, aman, tidak terputus, atau sesuai dengan harapan khusus Anda.
              </p>
            </div>
          </section>

          <!-- Liability (Accordion) -->
          <section id="liability" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.liability = !open.liability" :aria-expanded="open.liability">
              <div class="flex items-center gap-3">
                <Scale class="w-6 h-6 text-indigo-600" />
                <h2 class="text-2xl font-bold">12. Batas Tanggung Jawab</h2>
              </div>
              <component :is="open.liability ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.liability" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Sepanjang diizinkan hukum, <strong>Tikeli</strong> beserta afiliasi, direktur, karyawan, dan mitranya
                tidak bertanggung jawab atas kerugian tidak langsung, insidental, khusus, konsekuensial, kehilangan
                keuntungan, kehilangan data, gangguan usaha, kerusakan reputasi, atau biaya pengadaan pengganti yang
                timbul dari atau terkait penggunaan Layanan.
              </p>
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Tanggung jawab agregat kami terhadap klaim apa pun dibatasi pada jumlah yang Anda bayarkan dalam 6 (enam)
                bulan terakhir untuk Layanan yang relevan, atau Rp5.000.000 (mana yang lebih rendah), kecuali jika hukum
                di wilayah Anda menetapkan standar minimum yang berbeda.
              </p>
            </div>
          </section>

          <!-- Indemnity (Accordion) -->
          <section id="indemnity" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.indemnity = !open.indemnity" :aria-expanded="open.indemnity">
              <div class="flex items-center gap-3">
                <Shield class="w-6 h-6 text-emerald-600" />
                <h2 class="text-2xl font-bold">13. Ganti Rugi</h2>
              </div>
              <component :is="open.indemnity ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.indemnity" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Anda setuju untuk membela, mengganti rugi, dan membebaskan <strong>Tikeli</strong> dari klaim, tuntutan,
                kewajiban, kerugian, dan biaya (termasuk biaya penasihat hukum yang wajar) yang timbul dari (a) Konten
                Anda; (b) penggunaan Layanan oleh Anda; (c) pelanggaran terhadap Ketentuan; atau (d) pelanggaran hak pihak lain.
              </p>
            </div>
          </section>

          <!-- Governing Law & Dispute (Accordion) -->
          <section id="law" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.law = !open.law" :aria-expanded="open.law">
              <div class="flex items-center gap-3">
                <Gavel class="w-6 h-6 text-violet-600" />
                <h2 class="text-2xl font-bold">14. Hukum yang Berlaku & Penyelesaian Sengketa</h2>
              </div>
              <component :is="open.law ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.law" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Ketentuan ini diatur oleh hukum Republik Indonesia, tanpa memperhatikan pertentangan kaidah hukumnya.
                Sengketa akan diupayakan penyelesaian secara damai dalam 30 (tiga puluh) Hari Kerja sejak diberitahukan.
                Jika gagal, sengketa diselesaikan melalui arbitrase di Badan Arbitrase Nasional Indonesia (BANI)
                sesuai peraturan BANI yang berlaku, putusannya bersifat final dan mengikat.
              </p>
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm">
                Jika Anda adalah konsumen dan hukum setempat memberikan hak wajib tertentu, ketentuan ini tidak
                dimaksudkan untuk mengesampingkan hak-hak wajib tersebut.
              </p>
            </div>
          </section>

          <!-- Changes (Accordion) -->
          <section id="changes" class="rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <button class="w-full flex items-center justify-between px-6 py-4" @click="open.changes = !open.changes" :aria-expanded="open.changes">
              <div class="flex items-center gap-3">
                <RefreshCw class="w-6 h-6 text-yellow-600" />
                <h2 class="text-2xl font-bold">15. Perubahan Ketentuan</h2>
              </div>
              <component :is="open.changes ? ChevronUp : ChevronDown" class="w-5 h-5" />
            </button>
            <div v-show="open.changes" class="px-6 pb-6 space-y-3">
              <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                Kami dapat memperbarui Ketentuan ini dari waktu ke waktu. Versi terbaru akan dipublikasikan di halaman
                ini dengan penanda tanggal. Perubahan material akan diberitahukan via email atau banner in-app sebelum
                berlaku. Dengan terus menggunakan Layanan setelah tanggal efektif, Anda menyetujui versi yang diperbarui.
              </p>
            </div>
          </section>

          <!-- Contact -->
          <section id="contact" class="rounded-2xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 animate-fade-up">
            <div class="flex items-start gap-4">
              <Mail class="w-6 h-6 text-sky-600 shrink-0" />
              <div>
                <h2 class="text-2xl font-bold mb-2">16. Kontak</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Pertanyaan atau keluhan terkait Ketentuan ini dapat dikirimkan ke:
                </p>
                <div class="mt-3 grid sm:grid-cols-2 gap-3">
                  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                    <a href="mailto:legal@tikeli.app" class="font-medium underline">legal@tikeli.app</a>
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
          <section class="relative overflow-hidden rounded-2xl p-8 text-center bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-600 text-white animate-fade-up">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,white_1px,transparent_1px)] [background-size:20px_20px]" />
            <div class="relative z-10 max-w-2xl mx-auto">
              <h3 class="text-2xl md:text-3xl font-extrabold mb-3">Gunakan Tikeli dengan Bertanggung Jawab</h3>
              <p class="text-gray-100 mb-6">Baca, pahami, dan setujui Syarat & Ketentuan ini sebelum melanjutkan.</p>
              <a href="/register" class="inline-block px-6 py-3 rounded-xl font-semibold shadow bg-yellow-400 hover:bg-yellow-500 text-gray-900 transition hover:scale-105 hover:shadow-lg">Buat Akun</a>
            </div>
          </section>

          <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
            Dokumen ini disediakan sebagai referensi umum dan bukan nasihat hukum. Pertimbangkan konsultasi profesional untuk kebutuhan spesifik.
          </p>
        </div>
      </div>
    </section>

    <!-- Back to top -->
    <button @click="scrollToId('intro')" class="fixed bottom-6 right-6 rounded-full shadow-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-3 hover:scale-105 transition" aria-label="Back to top">
      <ChevronUp class="w-5 h-5" />
    </button>
  </PublicLayout>
</template>

<style>
/* simple entrance animations (no external libs) */
@keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pop { 0% { transform: scale(.96); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
.animate-fade-up { animation: fadeUp .5s ease both; }
.animate-pop { animation: pop .4s cubic-bezier(.2,.8,.2,1) both; }
</style>
