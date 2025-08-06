<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const showPassword = ref(false)
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const page = usePage()

const loginWithGoogle = () => {
  window.location.href = '/auth/google'
}

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Login" />

    <!-- Notifikasi Error -->
    <div
      v-if="page.props.errors.email"
      class="mb-4 px-4 py-3 text-sm text-red-700 bg-red-100 rounded"
    >
      {{ page.props.errors.email }}
    </div>

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Email -->
      <div>
        <InputLabel for="email" value="Email atau Username" />
        <TextInput
          id="email"
          type="text"
          v-model="form.email"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <!-- Password -->
      <div class="relative">
        <InputLabel for="password" value="Password" />
        <div class="relative">
          <TextInput
            :type="showPassword ? 'text' : 'password'"
            id="password"
            v-model="form.password"
            class="mt-1 block w-full pr-12"
            required
            autocomplete="current-password"
          />
          <button
            type="button"
            @click="togglePassword"
            class="absolute top-1/2 right-3 -translate-y-1/2 text-sm text-gray-500 hover:text-gray-700 focus:outline-none"
            tabindex="-1"
          >
            {{ showPassword ? '🙈' : '👁️' }}
          </button>
        </div>
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <!-- Remember + Forgot password -->
      <div class="flex items-center justify-between">
        <label class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember" />
          <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
        </label>

        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-sm text-blue-600 hover:underline"
        >
          Lupa password?
        </Link>
      </div>

      <!-- Tombol Login -->
      <div>
        <PrimaryButton
          class="w-full justify-center"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Masuk
        </PrimaryButton>
      </div>

      <!-- Login dengan Google -->
      <div class="mt-4">
        <button
          type="button"
          @click="loginWithGoogle"
          class="w-full flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm hover:bg-gray-100 transition"
        >
          <img
            src="https://www.svgrepo.com/show/475656/google-color.svg"
            alt="Google"
            class="w-5 h-5"
          />
          Masuk dengan Google
        </button>
      </div>

      <!-- Pendaftaran -->
      <div class="text-center text-sm mt-4">
        Belum punya akun?
        <Link href="/register" class="text-blue-600 hover:underline">Daftar di sini</Link>
      </div>

      <!-- Link ke Halaman Utama -->
      <div class="mt-6">
        <Link
          :href="route('guest.welcome')"
          class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-200 transition"
        >
          Ke Halaman Utama
        </Link>
      </div>
    </form>
  </GuestLayout>
</template>
