<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { onMounted, ref } from 'vue'

const props = defineProps({
  article: Object
})

const coverPreview = ref(props.article.cover_url ?? null)

const form = useForm({
  title: '',
  summary: '',
  content: '',
  status: '',
  cover: null,
  category: '',
})

onMounted(() => {
  form.title = props.article.title ?? ''
  form.summary = props.article.summary ?? ''
  form.content = props.article.content ?? ''
  form.status = props.article.status ?? ''
  form.category = props.article.category ?? 'ulasan'
})

const onCoverChange = (e) => {
  const file = e.target.files[0]
  form.cover = file
  coverPreview.value = file ? URL.createObjectURL(file) : null
}

const submit = (status) => {
  form.status = status

  form
    .transform(data => ({
      ...data,
      cover: form.cover,
    }))
    .post(`/operator/articles/${props.article.id}/edit-save`, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        router.visit('/operator/articles/mine')
      },
    })
}

const sendForApproval = () => submit('pending')
const saveAsDraft = () => submit('draft')
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Artikel" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 leading-tight">Edit Artikel</h2>
    </template>

    <div class="py-8">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
          <form class="space-y-6">
            <!-- Judul -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
              <input
                v-model="form.title"
                type="text"
                class="w-full border border-gray-300 rounded-md px-4 py-2"
              />
              <div v-if="form.errors.title" class="text-sm text-red-600 mt-1">{{ form.errors.title }}</div>
            </div>

            <!-- Kategori -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <select
                v-model="form.category"
                class="w-full border border-gray-300 rounded-md px-4 py-2"
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
              <div v-if="form.errors.category" class="text-sm text-red-600 mt-1">{{ form.errors.category }}</div>
            </div>

            <!-- Cover -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Cover (Opsional)</label>
              <input
                type="file"
                accept="image/*"
                @change="onCoverChange"
                class="w-full border border-gray-300 rounded-md px-4 py-2"
              />
              <div v-if="form.errors.cover" class="text-sm text-red-600 mt-1">{{ form.errors.cover }}</div>

              <div v-if="coverPreview" class="mt-3">
                <p class="text-xs text-gray-500 mb-1">Preview Cover:</p>
                <img :src="coverPreview" alt="Cover Preview" class="w-32 h-20 object-cover rounded border" />
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
                  toolbar="essential"
                />
              </div>
              <div v-if="form.errors.content" class="text-sm text-red-600 mt-1">{{ form.errors.content }}</div>
            </div>

            <!-- Tombol -->
            <div class="flex justify-between">
              <button
                type="button"
                @click="saveAsDraft"
                class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-md text-sm font-medium"
              >
                Simpan Draft
              </button>

              <button
                type="button"
                @click="sendForApproval"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm font-medium"
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
