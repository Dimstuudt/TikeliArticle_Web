<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { CheckCheck, MessageSquare, CheckCircle2, LogIn } from 'lucide-vue-next'
import { ref } from 'vue'
import axios from 'axios'

dayjs.extend(relativeTime)

const props = defineProps({
  thread: Object,
})

const firstPost = props.thread.posts[0] || null
const replies = ref(props.thread.posts.slice(1))

const form = ref({
  body: '',
  processing: false,
})

const successMessage = ref('')

const submitReply = async () => {
  if (!form.value.body.trim()) return
  form.value.processing = true
  successMessage.value = ''

  try {
    const { data: newPost } = await axios.post(
      route('forum.reply', props.thread.id),
      { body: form.value.body }
    )
    replies.value.push(newPost)
    form.value.body = ''
    successMessage.value = 'Komentar berhasil ditambahkan'
    setTimeout(() => (successMessage.value = ''), 3000)
  } catch (error) {
    console.error(error)
  } finally {
    form.value.processing = false
  }
}


</script>

<template>
  <PublicLayout>
    <Head :title="thread.title" />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Kiri: Thread + Komentar -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Thread Card -->
        <div
          class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 transition hover:shadow-md"
        >
          <div class="flex items-center gap-3 mb-4">
            <img
              :src="thread.user?.profile_photo_path
                ? '/storage/' + thread.user.profile_photo_path
                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(thread.user?.name || 'User')"
              alt="avatar"
              class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-500/30"
            />
            <div>
              <p class="flex items-center gap-1 text-base font-semibold text-gray-800 dark:text-gray-100">
                {{ thread.user?.name }}
                <CheckCheck v-if="thread.user?.trusted_writer" class="w-4 h-4 text-green-500" />
              </p>
              <p class="text-xs text-gray-400">
                {{ dayjs(thread.created_at).fromNow() }}
              </p>
            </div>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
            📌 {{ thread.title }}
          </h1>
          <p v-if="firstPost" class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
            {{ firstPost.body }}
          </p>
        </div>

        <!-- Balasan -->
        <div class="space-y-4">
          <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-800 dark:text-gray-100">
            <MessageSquare class="w-5 h-5 text-blue-600" />
            Balasan ({{ replies.length }})
          </h2>

          <transition-group name="fade-up" tag="div" class="space-y-4">
            <div
              v-for="post in replies"
              :key="post.id"
              class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-5 hover:shadow-md transition"
            >
              <div class="flex items-start gap-3">
                <img
                  :src="post.user?.profile_photo_path
                    ? '/storage/' + post.user.profile_photo_path
                    : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(post.user?.name || 'User')"
                  alt="avatar"
                  class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-500/30"
                />
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <p class="flex items-center gap-1 text-sm font-semibold text-gray-800 dark:text-gray-100">
                      {{ post.user?.name }}
                      <CheckCheck v-if="post.user?.trusted_writer" class="w-4 h-4 text-green-500" />
                    </p>
                    <p class="text-xs text-gray-400">
                      {{ dayjs(post.created_at).fromNow() }}
                    </p>
                  </div>
                  <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                    {{ post.body }}
                  </p>
                </div>
              </div>
            </div>
          </transition-group>

          <p v-if="!replies.length" class="text-gray-500 text-sm">Belum ada balasan.</p>
        </div>
      </div>

      <!-- Kanan: Form / Login -->
      <aside class="space-y-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 sticky top-20">
          <h3 class="text-base font-semibold mb-4">Tulis Balasan</h3>

          <!-- Jika login -->
          <template v-if="$page.props.auth.user">
            <!-- Notifikasi Toast -->
            <transition name="slide-down">
              <div
                v-if="successMessage"
                class="fixed top-4 right-4 z-50 px-4 py-2 bg-green-600 text-white text-sm rounded-lg shadow-lg"
              >
                <CheckCircle2 class="inline w-4 h-4 mr-1" />
                {{ successMessage }}
              </div>
            </transition>

            <form @submit.prevent="submitReply" class="space-y-3">
              <textarea
                v-model="form.body"
                rows="4"
                placeholder="Tulis komentar kamu..."
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl p-3 text-sm text-gray-800 dark:text-gray-200 dark:bg-gray-800 resize-none focus:ring-2 focus:ring-blue-500"
              />

              <button
                type="submit"
                class="w-full flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition disabled:opacity-50"
                :disabled="form.processing || !form.body.trim()"
              >
                <span v-if="!form.processing">🚀 Kirim Balasan</span>
                <span v-else class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
              </button>
            </form>
          </template>

          <!-- Jika belum login -->
          <template v-else>
            <div class="flex flex-col items-center text-center space-y-3">
              <LogIn class="w-8 h-8 text-gray-400" />
              <p class="text-sm text-gray-600 dark:text-gray-300">
                Login untuk menambahkan balasan
              </p>
              <Link
                href="/login"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition"
              >
                Login
              </Link>
            </div>
          </template>
        </div>
      </aside>
    </div>
  </PublicLayout>
</template>

<style>
/* Animasi Balasan */
.fade-up-enter-active, .fade-up-leave-active {
  transition: all 0.3s ease;
}
.fade-up-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.fade-up-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Toast */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-20px);
}
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
