<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { defineProps, ref } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import dayjs from 'dayjs'
import 'dayjs/locale/id'

dayjs.locale('id')

const props = defineProps({
  article: Object,
  views: Number,
  from: String,
  recommendations: Array,
  likeCount: Number,
  isLiked: Boolean,
  comments: Array,     // 🔹 tambahin props komentar
  canComment: Boolean, // 🔹 tambahin props cek login
})

// state like
const likes = ref(props.likeCount)
const liked = ref(props.isLiked)

// form komentar
const form = useForm({
  body: ''
})

// kirim komentar
const submitComment = () => {
  form.post(route('comments.store', props.article.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    }
  })
}

// navigasi back
const goBack = () => {
  if (props.from) {
    window.location.href = props.from
  } else if (window.history.length > 1) {
    window.history.back()
  } else {
    window.location.href = '/welcome'
  }
}

// format tanggal artikel
const formatDate = (date) => {
  return dayjs(date).format('DD MMMM YYYY')
}

// format tanggal komentar
const formatCommentDate = (date) => {
  return dayjs(date).format('DD MMMM YYYY HH:mm')
}

// like
const toggleLike = () => {
  router.post(route('articles.like', props.article.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      liked.value = !liked.value
      likes.value = liked.value ? likes.value + 1 : likes.value - 1
    }
  })
}
</script>


<template>
  <PublicLayout>
    <Head :title="article.title" />

    <main class="flex-grow max-w-7xl mx-auto px-4 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- KONTEN ARTIKEL (Kiri) -->
        <div class="lg:col-span-2 space-y-6">
  <!-- Ringkasan -->
  <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Ringkasan Artikel</h2>
    <p
      class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed"
      v-html="article.summary ?? 'Artikel ini membahas topik yang menarik dan informatif secara mendalam. Baca selengkapnya di bawah.'"
    ></p>
  </div>

  <!-- Artikel -->
  <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 space-y-6">
    <!-- Judul & Info -->
    <div>
      <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 leading-tight mb-1">
        {{ article.title }}
      </h1>

     <div class="flex flex-wrap items-center gap-2 mb-2">
  <!-- Kategori -->
  <div
    v-if="article.category"
    class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-3 py-1 rounded-full"
  >
    {{ article.category }}
  </div>

  <!-- Views -->
  <div
    class="inline-block bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-semibold px-3 py-1 rounded-full"
  >
    👁️ {{ views ?? 0 }} Views
  </div>

  <!-- Hits -->
  <div
    class="inline-block bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-semibold px-3 py-1 rounded-full"
  >
    🔄 {{ article.hits ?? 0 }} Hits
  </div>

  <!-- Likes -->
  <div
    class="inline-flex items-center bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 text-xs font-semibold px-3 py-1 rounded-full cursor-pointer transition hover:scale-105"
    @click="toggleLike"
  >
    <span class="mr-1">{{ liked ? '❤️' : '🤍' }}</span>
    <span>{{ likes }}</span>
  </div>
</div>


      <p class="text-sm text-gray-500 dark:text-gray-400">
        Oleh <span class="font-medium">{{ article.author.name }}</span> •
        {{ formatDate(article.created_at) }}
      </p>
    </div>

    <hr class="border-t border-gray-200 dark:border-gray-700" />

    <!-- Cover -->
    <div v-if="article.cover">
      <img
        :src="article.cover"
        alt="Cover"
        class="w-full max-h-[400px] object-cover rounded-lg shadow mb-4"
      />
    </div>

    <!-- Konten -->
    <div
      class="max-w-none prose prose-gray dark:prose-invert
        [&_h1]:text-4xl [&_h2]:text-3xl [&_h3]:text-2xl [&_h4]:text-xl [&_h5]:text-lg [&_h6]:text-base
        [&_ul]:list-disc [&_ul]:pl-6
        [&_ol]:list-decimal [&_ol]:pl-6
        [&_pre]:bg-gray-900 [&_pre]:text-white [&_pre]:p-4 [&_pre]:rounded [&_pre]:overflow-x-auto [&_pre]:font-mono [&_pre]:text-sm [&_pre]:my-2
        [&_code]:text-red-500
        [&_p]:text-left
        [&_p.ql-align-center]:text-center
        [&_p.ql-align-right]:text-right
        [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 dark:[&_blockquote]:border-gray-600 [&_blockquote]:pl-4 [&_blockquote]:italic"
    >
      <div v-html="article.content"></div>
    </div>



<!-- Tentang Penulis -->
<div
  class="rounded-xl p-4 shadow-xl flex items-center gap-4
         bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500"
>
  <div
    class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm flex items-center gap-4 p-4 rounded-xl w-full"
  >
    <!-- Foto Profil -->
    <img
      :src="article.author.profile_photo_path
        ? `/storage/${article.author.profile_photo_path}`
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(article.author.name)}`"
      alt="Foto Profil"
      class="w-16 h-16 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow-md"
    />

    <!-- Info Penulis -->
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2">
        <p class="text-base font-semibold text-gray-800 dark:text-gray-100 truncate">
          {{ article.author.name }}
        </p>
        <span
          class="text-xs font-medium px-2 py-0.5 rounded-sm"
          :class="{
            'bg-green-100 text-green-800 dark:bg-green-800/40 dark:text-green-300': article.author.role === 'admin',
            'bg-green-50 text-green-700 dark:bg-green-700/40 dark:text-green-200': article.author.role === 'operator',
          }"
        >
          {{ article.author.role }}
        </span>
      </div>
      <p class="text-gray-600 dark:text-gray-400 text-sm mt-1 line-clamp-2">
        {{ article.author.bio || 'Penulis Pemula' }}
      </p>
    </div>

    <!-- Button Profil -->
    <a
      :href="`/users/${article.author.id}`"
      class="shrink-0 flex items-center gap-1 text-xs px-3 py-1.5 rounded-full text-white font-medium transition
             bg-gradient-to-r from-purple-600 via-pink-500 to-indigo-500 hover:scale-105 shadow-md"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M5.121 17.804A9.969 9.969 0 0112 15c2.21 0 4.255.72 5.879 1.929M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      Profil
    </a>
  </div>
</div>



            <!-- Tombol Kembali -->
            <div class="pt-6">
              <button
                @click="goBack"
                class="flex items-center gap-2 bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-400
                       text-white px-5 py-2 rounded-md font-medium shadow-md hover:shadow-lg
                       hover:scale-[1.02] active:scale-[0.98] transition-transform duration-150"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
              </button>
            </div>
          </div>
        </div>

<!-- ASIDE (Kanan) -->
<aside class="w-[380px] space-y-6">
  <!-- Card Rekomendasi Artikel -->
  <div
    class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-5 space-y-5
           border border-gray-200 dark:border-gray-700"
  >
    <!-- Judul Section -->
    <h3
      class="text-lg font-bold text-purple-700 dark:text-purple-400
             border-b border-gray-200 dark:border-gray-700 pb-2"
    >
      Rekomendasi Artikel 📰
    </h3>

    <!-- List Artikel -->
    <div class="space-y-4">
      <div
        v-for="rec in recommendations"
        :key="rec.id"
        class="flex gap-4 p-4 border border-gray-200 dark:border-gray-700
               rounded-xl hover:shadow-lg transition cursor-pointer
               bg-white dark:bg-gray-800"
      >
        <!-- Thumbnail -->
        <a
          :href="`/articles/${rec.id}`"
          class="flex-shrink-0 w-28 h-20 rounded-lg overflow-hidden"
        >
          <img
            v-if="rec.cover"
            :src="rec.cover"
            alt="Cover"
            class="w-full h-full object-cover"
          />
          <div
            v-else
            class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center
                   text-gray-400 dark:text-gray-500 text-sm"
          >
            No Img
          </div>
        </a>

        <!-- Info -->
        <div class="flex-1 min-w-0 flex flex-col justify-between">
          <a :href="`/articles/${rec.id}`" class="block">
            <h4
              class="font-semibold text-gray-800 dark:text-gray-100 text-sm
                     line-clamp-2 hover:text-purple-600 dark:hover:text-purple-400 transition"
            >
              {{ rec.title }}
            </h4>
          </a>

          <!-- Views + Category -->
          <div
            class="flex items-center gap-3 mt-2 text-[12px]
                   text-gray-500 dark:text-gray-400"
          >
            <span class="flex items-center gap-1">
              👁️ {{ rec.views ?? 0 }}
            </span>
            <span
              class="bg-gradient-to-r from-sky-400 to-cyan-400
                     text-white text-[11px] font-semibold px-2 py-0.5
                     rounded-full shadow-sm"
            >
              {{ rec.category ?? 'Umum' }}
            </span>
          </div>

          <!-- Author + Date -->
          <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 truncate">
            {{ rec.author.name }} • {{ formatDate(rec.created_at) }}
          </p>
        </div>
      </div>
    </div>
  </div>

<!-- Komentar Section -->
<div
  class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-6
         border border-gray-200 dark:border-gray-700 mt-10"
>
  <!-- Judul -->
  <h3
    class="flex items-center text-lg font-bold text-purple-700 dark:text-purple-400
           border-b border-gray-200 dark:border-gray-700 pb-3"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v6a2 2 0 01-2 2h-3l-4 4z" />
    </svg>
    Komentar 💬
  </h3>

  <!-- Jika belum ada komentar -->
  <div v-if="!comments || comments.length === 0" class="text-center py-6">
    <div class="text-4xl mb-2">💭</div>
    <p class="text-sm text-gray-500 dark:text-gray-400">
      Belum ada yang berkomentar.<br/>
      <span class="text-purple-600 dark:text-purple-400 font-medium">Jadilah yang pertama!</span>
    </p>
  </div>

<!-- Daftar komentar -->
<div
  v-else
  class="mt-4 space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar"
>
  <div
    v-for="comment in comments"
    :key="comment.id"
    class="flex items-start gap-3"
  >
    <!-- Foto profil -->
    <img
      v-if="comment.user.profile_photo_path"
      :src="comment.user.profile_photo_path"
      alt="User"
      class="w-11 h-11 rounded-full object-cover border border-purple-300 shadow"
    />
    <div
      v-else
      class="w-11 h-11 rounded-full bg-purple-500 flex items-center justify-center text-white font-bold shadow"
    >
      {{ comment.user.name.charAt(0).toUpperCase() }}
    </div>

    <!-- Kotak komentar -->
    <div
      class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700
             rounded-xl p-3 shadow-sm hover:shadow-md transition flex flex-col"
    >
      <!-- Nama -->
      <div class="flex items-center justify-between">
        <a
          :href="`/users/${comment.user.id}`"
          class="font-semibold text-sm text-purple-600 dark:text-purple-400 hover:underline"
        >
          {{ comment.user.name }}
        </a>
      </div>

    <!-- Isi komentar -->
<div
  class="mt-2 inline-block max-w-full bg-gray-100 dark:bg-gray-800
         px-4 py-2 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"
>
  <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
    {{ comment.body }}
  </p>
</div>


      <!-- Tanggal -->
      <div class="text-right mt-3">
        <span class="text-xs text-gray-400 italic">
          {{ formatCommentDate(comment.created_at) }}
        </span>
      </div>
    </div>
  </div>
</div>


  <!-- Form komentar -->
  <div class="mt-6">
    <div v-if="props.canComment">
      <form @submit.prevent="submitComment" class="flex flex-col gap-3">
        <textarea
          v-model="form.body"
          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800
                 focus:ring-2 focus:ring-purple-400 focus:border-purple-400
                 transition px-4 py-3 resize-none shadow-sm"
          rows="3"
          placeholder="Tulis komentar kamu..."
        />
        <!-- error pesan -->
        <div v-if="form.errors.body" class="text-sm text-red-500">
          {{ form.errors.body }}
        </div>

        <button
          type="submit"
          class="self-end px-5 py-2 bg-purple-600 text-white rounded-xl
                 font-medium shadow hover:bg-purple-700 active:scale-95
                 transition disabled:opacity-50"
          :disabled="form.processing || !form.body"
        >
          Kirim 🚀
        </button>
      </form>
    </div>


   <div class="max-w-md mx-auto mt-6 text-center">
  <!-- Jika user belum login -->
  <template v-if="!$page.props.auth.user">
    <p class="text-gray-700 dark:text-gray-200 text-base">
      Login terlebih dahulu untuk ikut berkomentar.
    </p>
    <p class="text-gray-700 dark:text-gray-200 text-base mt-2">
      Belum punya akun?
      <a href="/register" class="text-blue-600 hover:text-blue-700 font-semibold underline">
        Daftar Sekarang
      </a>
    </p>
  </template>
</div>

  </div>
</div>

</aside>



      </div>
    </main>
  </PublicLayout>
</template>
