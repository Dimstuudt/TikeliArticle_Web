<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { ref } from 'vue'

const form = useForm({
  title: '',
  summary: '',
  content: '',
  status: 'pending',
  cover: null,
  category: 'ulasan',
})

const coverPreview = ref(null)

const onCoverChange = (e) => {
  const file = e.target.files[0]
  form.cover = file
  coverPreview.value = file ? URL.createObjectURL(file) : null
}

const submit = () => {
  form.post('/operator/articles', {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      router.visit('/operator/articles/mine')
    }
  })
}

const sendArticle = () => {
  form.status = 'pending'
  submit()
}

const saveDraft = () => {
  form.status = 'draft'
  submit()
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Buat Artikel" />

    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">Buat Artikel</h2>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
          <form @submit.prevent="sendArticle" class="space-y-6">

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

            <!-- Kategori -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <select
                v-model="form.category"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none"
              >
                <option value="ulasan">Ulasan</option>
                <option value="teknologi">Teknologi</option>
                <option value="hiburan">Hiburan</option>
                <option value="hoby">Hoby</option>
                <option value="opini">Opini</option>
                <option value="fiksi">Fiksi</option>
                <option value="tutorial">Tutorial</option>
                <option value="biografi">Biografi</option>
                <option value="comparison">Comparison</option>
              </select>
              <div v-if="form.errors.category" class="text-sm text-red-600 mt-1">
                {{ form.errors.category }}
              </div>
            </div>

            <!-- Cover -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Cover</label>
              <input
                type="file"
                @change="onCoverChange"
                accept="image/*"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none"
              />
              <div v-if="form.errors.cover" class="text-sm text-red-600 mt-1">
                {{ form.errors.cover }}
              </div>

              <!-- Preview Cover -->
              <div v-if="coverPreview" class="mt-3">
                <p class="text-xs text-gray-500 mb-1">Preview Cover:</p>
                <img :src="coverPreview" alt="Preview" class="w-32 h-20 object-cover rounded border" />
              </div>
            </div>

            <!-- Ringkasan -->
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan</label>
  <div class="border border-gray-300 rounded-md">
    <QuillEditor
      v-model:content="form.summary"
      contentType="html"
      theme="snow"
      class="min-h-[150px]"
    />
  </div>
  <div v-if="form.errors.summary" class="text-sm text-red-600 mt-1">
    {{ form.errors.summary }}
  </div>
</div>

            <!-- Konten -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
              <div class="border border-gray-300 rounded-md">
                <QuillEditor
                  v-model:content="form.content"
                  contentType="html"
                  theme="snow"
                  class="min-h-[200px]"
                />
              </div>
              <div v-if="form.errors.content" class="text-sm text-red-600 mt-1">
                {{ form.errors.content }}
              </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-between gap-4">
              <button
                type="button"
                @click="saveDraft"
                class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-md text-sm font-medium shadow-sm transition"
              >
                Simpan Draft
              </button>

              <button
                type="submit"
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
