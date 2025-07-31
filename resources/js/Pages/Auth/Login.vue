<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const showPassword = ref(false)
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// Akses error dari server (untuk Google login juga)
const page = usePage()

//gugel
const loginWithGoogle = () => {
  window.location.href = '/auth/google';
};

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Login" />

    <!-- ✅ Notifikasi error umum (misal akun nonaktif) -->
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

      <div class="relative">
        <TextInput
          :type="showPassword ? 'text' : 'password'"
          id="password"
          v-model="form.password"
          class="mt-1 block w-full pr-10"
          required
          autocomplete="current-password"
        />

        <button
          type="button"
          @click="togglePassword"
          class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700 text-sm"
          tabindex="-1"
        >
          {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
        </button>
      </div>
      <InputError class="mt-2" :message="form.errors.password" />

      <div class="flex items-center justify-between">
        <label class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember" />
          <span class="ms-2 text-sm text-gray-600">Remember me</span>
        </label>

        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-sm text-blue-600 hover:underline"
        >
          Forgot your password?
        </Link>
      </div>

      <div>
        <PrimaryButton
          class="w-full justify-center"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Log in
        </PrimaryButton>

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
            Login with Google
          </button>
        </div>
      </div>

      <div class="text-center text-sm mt-4">
        Belum punya akun?
        <Link href="/register" class="text-blue-600 hover:underline">Daftar di sini</Link>
      </div>
    </form>
  </GuestLayout>
</template>
