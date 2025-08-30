<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { CheckCircle, X } from 'lucide-vue-next'

dayjs.extend(relativeTime)

const props = defineProps({
  threads: Object,
})

const form = useForm({
  title: '',
  body: '',
})

const showForm = ref(false)
const showToast = ref(false) // ✅ state untuk notif

const submit = () => {
  form.post(route('forum.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      showForm.value = false
      showToast.value = true // ✅ munculin notif

      // ✅ otomatis ilang setelah 3 detik
      setTimeout(() => {
        showToast.value = false
      }, 3000)
    },
  })
}
</script>

<template>
  <PublicLayout>
    <Head title="Forum Diskusi" />

    <!-- Hero Section -->
    <section class="max-w-6xl mx-auto px-6 py-12">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Forum Diskusi
          </h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400 text-base">
            Tempat berbagi ide, berdiskusi, dan bertukar pengalaman bersama komunitas.
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
        </div>
      </div>
    </section>

    <!-- List Threads -->
    <section class="max-w-6xl mx-auto px-6 pb-16">
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="thread in threads.data"
          :key="thread.id"
          class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition flex flex-col overflow-hidden"
        >
          <!-- Header -->
          <div class="flex items-center gap-3 p-4 border-b border-gray-100 dark:border-gray-800">
            <img
              :src="thread.user?.profile_photo_path
                ? '/storage/' + thread.user.profile_photo_path
                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(thread.user?.name || 'User')"
              class="w-11 h-11 rounded-full object-cover border border-gray-300 dark:border-gray-700"
            />
            <div>
              <div class="flex items-center gap-1">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
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
          <div class="flex items-center justify-between p-4 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1">💬 {{ thread.posts_count ?? thread.posts.length }} komentar</span>
            <span class="font-medium text-gray-400">#{{ thread.id }}</span>
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
              'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 border-gray-300 dark:border-gray-700': !link.active,
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
        <!-- Close button -->
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

    <!-- ✅ Toast Notification -->
    <transition name="fade">
      <div
        v-if="showToast"
        class="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg z-50"
      >
        ✅ Thread berhasil dibuat!
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
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out;
}

/* ✅ animasi toast */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
