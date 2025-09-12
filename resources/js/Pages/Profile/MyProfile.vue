<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// ✅ bikin reactive user lokal dari props
const page = usePage()
const user = reactive({
  ...page.props.auth.user,
  bio: page.props.auth.user.bio ?? '', // fallback
  background_photo_path: page.props.auth.user.background_photo_path ?? '',
})

// ✅ preview awal
const previewPhoto = ref(user.profile_photo_url || '/images/default-avatar.png')
const previewBg = ref(
  user.background_photo_path
    ? `/storage/${user.background_photo_path}`
    : '/images/default-bg.jpg'
)

// Form data publik
const profileForm = useForm({
  name: user.name || '',
  bio: user.bio || '',
})

// Form upload foto profil
const photoForm = useForm({ photo: null })

// Form upload background
const bgForm = useForm({ background: null })

// Handle submit profil
const submitProfile = () => {
  profileForm.patch(route('my.profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      // ✅ langsung update reactive user lokal
      user.name = profileForm.name
      user.bio = profileForm.bio
    },
  })
}

// Handle submit foto profil
const submitPhoto = () => {
  photoForm.post(route('my.profile.photo'), {
    preserveScroll: true,
    onSuccess: () => {
      if (photoForm.photo) {
        previewPhoto.value = URL.createObjectURL(photoForm.photo)
      }
    },
  })
}

// Handle submit background
const submitBackground = () => {
  bgForm.post(route('my.profile.background'), {
    preserveScroll: true,
    onSuccess: () => {
      if (bgForm.background) {
        previewBg.value = URL.createObjectURL(bgForm.background)
      }
    },
  })
}

// Preview saat pilih file
const onPhotoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    photoForm.photo = file
    previewPhoto.value = URL.createObjectURL(file)
  }
}

const onBackgroundChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    bgForm.background = file
    previewBg.value = URL.createObjectURL(file)
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profil Saya</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <div class="max-w-4xl mx-auto">

            <!-- Background dengan preview -->
            <div
              class="h-48 w-full mb-6 bg-blue-100 bg-cover bg-center rounded-lg border border-blue-200"
              :style="previewBg ? `background-image: url('${previewBg}')` : ''"
            >
              <form @submit.prevent="submitBackground" class="mt-2 ml-2" enctype="multipart/form-data">
                <input
                  type="file"
                  @change="onBackgroundChange"
                  class="bg-white bg-opacity-70 p-1 rounded text-sm"
                />
                <button
                  type="submit"
                  class="mt-1 text-xs text-white bg-blue-600 px-2 py-1 rounded hover:bg-blue-700"
                  :disabled="bgForm.processing"
                >
                  Ganti Background
                </button>
              </form>
            </div>

            <!-- Foto Profil dengan preview -->
            <div class="flex items-center space-x-4 mb-4">
              <img
                :src="previewPhoto"
                alt="Foto Profil"
                class="w-20 h-20 rounded-full object-cover border-2 border-blue-400"
              />
              <form @submit.prevent="submitPhoto" enctype="multipart/form-data">
                <label class="block text-sm font-medium text-gray-700">Ganti Foto Profil</label>
                <input
                  type="file"
                  class="mt-1 text-sm"
                  @change="onPhotoChange"
                />
                <button
                  type="submit"
                  class="mt-1 text-xs text-white bg-blue-600 px-2 py-1 rounded hover:bg-blue-700"
                  :disabled="photoForm.processing"
                >
                  Upload
                </button>
              </form>
            </div>

            <!-- Form Nama & Bio -->
            <form @submit.prevent="submitProfile" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input
                  v-model="profileForm.name"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea
                  v-model="profileForm.bio"
                  rows="4"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                ></textarea>
              </div>

              <div>
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700"
                  :disabled="profileForm.processing"
                >
                  Simpan Perubahan
                </button>
              </div>

              <div v-if="profileForm.recentlySuccessful" class="text-green-600 text-sm mt-2">
                Perubahan berhasil disimpan.
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
