<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const user = usePage().props.auth.user

// Form data publik
const profileForm = useForm({
  name: user.name,
  bio: user.bio || '',
})

// Form upload foto profil
const photoForm = useForm({
  photo: null,
})

// Form upload background
const bgForm = useForm({
  background: null,
})

// Handle submit dengan auto-refresh user data
const submitProfile = () => {
  profileForm.patch(route('my.profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['auth'] })
    }
  })
}

const submitPhoto = () => {
  photoForm.post(route('my.profile.photo'), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['auth'] })
    }
  })
}

const submitBackground = () => {
  bgForm.post(route('my.profile.background'), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['auth'] })
    }
  })
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

            <!-- Background -->
            <div
              class="h-48 w-full mb-6 bg-blue-100 bg-cover bg-center rounded-lg border border-blue-200"
              :style="user.background_photo_path ? `background-image: url('/storage/${user.background_photo_path}')` : ''"
            >
              <form @submit.prevent="submitBackground" class="mt-2 ml-2" enctype="multipart/form-data">
                <input
                  type="file"
                  @change="e => bgForm.background = e.target.files[0]"
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

            <!-- Foto Profil -->
            <div class="flex items-center space-x-4 mb-4">
              <img
                :src="user.profile_photo_url"
                alt="Foto Profil"
                class="w-20 h-20 rounded-full object-cover border-2 border-blue-400"
              />
              <form @submit.prevent="submitPhoto" enctype="multipart/form-data">
                <label class="block text-sm font-medium text-gray-700">Ganti Foto Profil</label>
                <input
                  type="file"
                  class="mt-1 text-sm"
                  @change="e => photoForm.photo = e.target.files[0]"
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
