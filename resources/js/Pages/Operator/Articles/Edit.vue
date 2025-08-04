<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const props = defineProps({
  article: Object
})

const form = useForm({
  title: props.article.title,
  content: props.article.content,
  status: props.article.status,
})

const submit = () => {
  form.put(`/operator/articles/${props.article.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      router.visit('/operator/articles/mine') // redirect setelah update
    },
  })
}

const sendForApproval = () => {
  form.status = 'pending'
  submit()
}

const saveAsDraft = () => {
  form.status = 'draft'
  submit()
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Artikel" />

    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">Edit Artikel</h2>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Judul -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
              <input
                v-model="form.title"
                type="text"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none"
              />
              <div v-if="form.errors.title" class="text-sm text-red-600 mt-1">
                {{ form.errors.title }}
              </div>
            </div>

            <!-- Konten -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
              <div class="border border-gray-300 rounded-md overflow-y-auto max-h-64">
                <QuillEditor
                  v-model:content="form.content"
                  contentType="html"
                  theme="snow"
                  class="min-h-[200px] max-h-64"
                />
              </div>
              <div v-if="form.errors.content" class="text-sm text-red-600 mt-1">
                {{ form.errors.content }}
              </div>
            </div>

            <!-- Tombol -->
            <div class="flex justify-between gap-4">
              <button
                type="button"
                @click="saveAsDraft"
                class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-md text-sm font-medium shadow-sm transition"
              >
                Simpan Draft
              </button>

              <button
                type="button"
                @click="sendForApproval"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm font-medium shadow-sm transition"
              >
                Kirim Artikel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
