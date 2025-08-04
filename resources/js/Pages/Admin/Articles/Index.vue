<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  articles: Array,
})

const approve = (id) => {
  router.put(`/admin/articles/${id}/approve`, {}, {
    preserveScroll: true,
  })
}

const reject = (id) => {
  router.put(`/admin/articles/${id}/reject`, {}, {
    preserveScroll: true,
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Manajemen Artikel" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 leading-tight">Manajemen Artikel</h2>
    </template>

    <div class="py-8">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div v-if="articles.length === 0" class="text-gray-500 text-center py-8">
            Belum ada artikel.
          </div>

          <div v-else class="space-y-6">
            <div
              v-for="article in articles"
              :key="article.id"
              class="border border-gray-200 p-5 rounded-lg shadow-sm hover:shadow-md transition"
            >
              <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold text-gray-900">{{ article.title }}</h2>
                <span
                  class="text-xs font-medium px-2.5 py-0.5 rounded"
                  :class="{
                    'bg-yellow-100 text-yellow-800': article.status === 'pending',
                    'bg-green-100 text-green-800': article.status === 'approved',
                    'bg-red-100 text-red-800': article.status === 'rejected',
                  }"
                >
                  {{ article.status }}
                </span>
              </div>

              <p class="text-sm text-gray-600 mb-1">
                Oleh: <span class="font-medium">{{ article.user.name }}</span>
              </p>

              <div
                class="text-gray-700 max-h-48 overflow-auto mb-4"
                v-html="article.content"
              ></div>

              <div v-if="article.status === 'pending'" class="flex gap-2">
                <button
                  @click="approve(article.id)"
                  class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 text-sm rounded"
                >
                  Approve
                </button>
                <button
                  @click="reject(article.id)"
                  class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 text-sm rounded"
                >
                  Reject
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
