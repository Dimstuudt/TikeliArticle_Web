<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { CheckCircle, X, Star } from 'lucide-vue-next'

dayjs.extend(relativeTime)

const props = defineProps({
  threads: Object,
  leaderboard: Array,
    authUserId: Number,
})

const form = useForm({
  title: '',
  body: '',
})

const showForm = ref(false)
const showToast = ref(false)

const submit = () => {
  form.post(route('forum.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      showForm.value = false
      showToast.value = true
      setTimeout(() => (showToast.value = false), 3000)
    },
  })
}

const deleteThread = (id) => {
  if (!confirm('Apakah kamu yakin ingin menghapus thread ini?')) return
  form.delete(route('forum.destroy', id), {
    preserveScroll: true,
    onSuccess: () => {
      showToast.value = true
      setTimeout(() => (showToast.value = false), 3000)
    },
  })
}
</script>

<template>
  <PublicLayout>
    <Head title="Forum Diskusi" />

    <!-- Hero -->
    <section class="max-w-6xl mx-auto px-6 py-12">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
      >
        <div>
          <h1
            class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight"
          >
            Forum Diskusi
          </h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400 text-base">
            Tempat berbagi ide, berdiskusi, dan bertukar pengalaman bersama
            komunitas.
          </p>
        </div>

        <div>
          <button
            v-if="$page.props.auth.user && $page.props.auth.user.trusted_writer"
            @click="showForm = true"
            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium shadow hover:bg-blue-700 active:scale-95 transition"
          >
            + Buat Thread
          </button>

          <span
            v-else-if="$page.props.auth.user"
            class="text-sm text-gray-500 dark:text-gray-400 italic"
          >
            Kamu belum bisa membuat thread (butuh status trusted).
          </span>

          <Link
            v-else
            :href="route('login')"
            class="px-5 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition"
          >
            Login untuk Diskusi
          </Link>
        </div>
      </div>
    </section>

    <!-- Threads + Free Slot -->
    <section class="max-w-6xl mx-auto px-6 pb-16">
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Thread list kiri -->
        <div class="md:col-span-2">
          <div class="grid gap-6 md:grid-cols-2">
            <div
              v-for="(thread, index) in threads.data"
              :key="thread.id"
              class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all flex flex-col overflow-hidden"
            >
              <!-- Header -->
              <div
                class="flex items-center gap-3 p-4 border-b border-gray-100 dark:border-gray-800"
              >
                <img
                  :src="
                    thread.user?.profile_photo_path
                      ? '/storage/' + thread.user.profile_photo_path
                      : 'https://ui-avatars.com/api/?name=' +
                        encodeURIComponent(thread.user?.name || 'User')
                  "
                  class="w-11 h-11 rounded-full object-cover border border-gray-300 dark:border-gray-700"
                />
                <div>
                  <div class="flex items-center gap-1">
                    <p
                      class="text-sm font-semibold text-gray-800 dark:text-gray-100"
                    >
                      {{ thread.user?.name }}
                    </p>
                    <CheckCircle
                      v-if="thread.user?.trusted_writer"
                      class="w-4 h-4 text-green-500"
                    />
                  </div>
                  <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ dayjs(thread.updated_at).fromNow() }}
                  </p>
                </div>
              </div>

              <!-- Body -->
              <div class="p-5 flex-1 flex flex-col">
                <Link
                  :href="route('forum.show', thread.id)"
                  class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition"
                >
                  {{ thread.title }}
                </Link>
                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                  {{
                    thread.posts.length > 0
                      ? thread.posts[0].body.slice(0, 120) + '...'
                      : 'Belum ada isi'
                  }}
                </p>
              </div>

              <!-- Footer -->
              <div
                class="flex items-center justify-between p-4 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400"
              >
                <span class="flex items-center gap-1">
                  💬
                  {{ thread.posts_count ?? thread.posts.length }} komentar
                </span>

                <div class="flex items-center gap-2">
                  <button
                    v-if="
                      $page.props.auth.user &&
                      $page.props.auth.user.id === thread.user?.id &&
                      $page.props.auth.user.trusted_writer
                    "
                    @click="deleteThread(thread.id)"
                    class="text-white bg-red-500 hover:bg-red-600 transition-all duration-200 px-3 py-1 rounded-md shadow-sm text-sm font-medium"
                  >
                    Hapus
                  </button>
                  <span class="font-medium text-gray-400">
                    #{{
                      threads.total -
                      ((threads.current_page - 1) * threads.per_page + index)
                    }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>



<div class="space-y-4">
<!-- Slot 1: Leaderboard -->
<div
  class="h-[420px] relative rounded-3xl border border-gray-300 dark:border-gray-700 p-5 bg-white dark:bg-gray-900 flex flex-col shadow-2xl"
>
  <h2 class="text-xl font-extrabold mb-4 text-gray-800 dark:text-gray-100 tracking-wide">
  User Leaderboard 🏆
  </h2>

  <ul class="space-y-3 flex-1">
    <li
      v-for="(u, i) in props.leaderboard.slice(0,4)"
      :key="u.id"
      :class="[
        'flex items-center gap-4 p-3 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-md',
        u.id === props.authUserId ? 'bg-yellow-50 dark:bg-yellow-900' : 'bg-gradient-to-r from-gray-50 via-gray-100 to-gray-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800'
      ]"
    >
      <!-- Medali top 3 -->
      <div class="w-6 text-lg flex justify-center">
        <span v-if="i === 0">🥇</span>
        <span v-else-if="i === 1">🥈</span>
        <span v-else-if="i === 2">🥉</span>
        <span v-else class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ i + 1 }}</span>
      </div>

      <!-- Profil photo / inisial -->
      <div
        class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-white font-bold overflow-hidden ring-2 ring-yellow-400 dark:ring-yellow-300 shadow-inner"
      >
        <img v-if="u.profile_photo_url" :src="u.profile_photo_url" class="w-full h-full object-cover rounded-full" />
        <span v-else>{{ getInitials(u.name) }}</span>
      </div>

      <!-- Nama + username + role -->
      <div class="flex-1 flex flex-col justify-center gap-1">
        <div class="flex items-center gap-2">
          <span class="font-semibold text-gray-800 dark:text-gray-100 line-clamp-1">
            {{ u.name.split(' ').slice(0,2).join(' ') }}
          </span>
          <CheckCircle
            v-if="u.trusted_writer"
            class="w-4 h-4 text-green-500"
            stroke-width="2"
          />
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
          @{{ u.username.split(' ').slice(0,2).join(' ') }} - {{ u.role }}
        </div>
      </div>

      <!-- Points dengan icon bintang di belakang -->
      <div class="flex flex-col items-end justify-center relative">
        <span class="text-lg font-bold text-yellow-500 dark:text-yellow-400 animate-pulse relative z-10">
          {{ u.total_points }}
        </span>
        <!-- Icon samar di belakang -->
        <Star class="absolute text-yellow-200 dark:text-yellow-700 opacity-20 w-6 h-6 top-1 right-0 z-0"/>
        <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 z-10">points</span>
      </div>
    </li>
  </ul>
</div>



<!-- Slot 2: Rules Leaderboard Informative -->
<div
  class="h-[307px] relative rounded-2xl border border-gray-300 dark:border-gray-700 overflow-hidden p-5 flex flex-col justify-between shadow-xl bg-gradient-to-br from-pink-50 via-pink-100 to-pink-50 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800"
>
  <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3 tracking-wide">
    Leaderboard Points 💎
  </h3>

  <ul class="flex-1 space-y-2 text-sm text-gray-700 dark:text-gray-300">
    <li class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <span class="text-pink-500 animate-pulse">🏆</span>
        Approved Articles
      </div>
      <span class="font-semibold text-gray-800 dark:text-gray-100">+100</span>
    </li>
    <li class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <span class="text-yellow-500 animate-bounce">❤️</span>
        Likes
      </div>
      <span class="font-semibold text-gray-800 dark:text-gray-100">+25</span>
    </li>
    <li class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <span class="text-blue-500 animate-bounce-slow">👁️</span>
        Views
      </div>
      <span class="font-semibold text-gray-800 dark:text-gray-100">+20</span>
    </li>
    <li class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <span class="text-green-500 animate-pulse-slow">⚡</span>
        Hits
      </div>
      <span class="font-semibold text-gray-800 dark:text-gray-100">+1</span>
    </li>
    <li class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <span class="text-purple-500 animate-bounce-slow">💬</span>
        Comments
      </div>
      <span class="font-semibold text-gray-800 dark:text-gray-100">+5</span>
    </li>
  </ul>

  <!-- Decorative elements -->
  <div class="absolute top-0 left-0 w-16 h-16 bg-pink-200 dark:bg-pink-700 rounded-full opacity-20 animate-bounce-slow"></div>
  <div class="absolute bottom-0 right-0 w-24 h-24 bg-pink-300 dark:bg-pink-600 rounded-full opacity-15 animate-pulse-slow"></div>
</div>


</div>



      </div>

      <!-- Pagination -->
      <div class="mt-12 flex justify-center">
        <div class="flex gap-2">
          <Link
            v-for="link in threads.links"
            :key="link.label"
            :href="link.url || ''"
            v-html="link.label"
            class="px-3 py-1.5 rounded-lg border text-sm font-medium transition"
            :class="{
              'bg-blue-600 text-white border-blue-600 shadow': link.active,
              'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 border-gray-300 dark:border-gray-700':
                !link.active,
            }"
          />
        </div>
      </div>
    </section>

    <!-- Modal Create Thread -->
    <div
      v-if="showForm"
      class="fixed inset-0 flex items-center justify-center bg-black/60 z-50 px-4"
    >
      <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-lg relative animate-fadeIn"
      >
        <button
          @click="showForm = false"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
        >
          <X class="w-5 h-5" />
        </button>

        <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">
          Buat Thread Baru
        </h2>

        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <input
              v-model="form.title"
              type="text"
              placeholder="Judul Thread"
              class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-200"
            />
            <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">
              {{ form.errors.title }}
            </div>
          </div>

          <div>
            <textarea
              v-model="form.body"
              rows="5"
              placeholder="Isi Thread..."
              class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-200"
            />
            <div v-if="form.errors.body" class="text-red-500 text-xs mt-1">
              {{ form.errors.body }}
            </div>
          </div>

          <div class="flex justify-end gap-2">
            <button
              type="button"
              @click="showForm = false"
              class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 active:scale-95 transition"
              :disabled="form.processing"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast -->
    <transition name="fade">
      <div
        v-if="showToast"
        class="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg z-50"
      >
        🛠️ Thread berhasil diproses!
      </div>
    </transition>
  </PublicLayout>
</template>

<style>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.15; }
  50% { opacity: 0.3; }
}

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-bounce-slow {
  animation: bounce-slow 6s ease-in-out infinite;
}
</style>
