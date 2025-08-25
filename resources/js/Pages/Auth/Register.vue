<script setup>
import { computed, ref, onMounted } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    'g-recaptcha-response': ''
})

// Validasi realtime
const usernameHasSpace = computed(() => /\s/.test(form.username))
const isPasswordLongEnough = computed(() => form.password.length >= 8)
const isPasswordStrong = computed(() =>
    /[a-z]/.test(form.password) &&
    /[A-Z]/.test(form.password) &&
    /[0-9]/.test(form.password) &&
    /[^A-Za-z0-9]/.test(form.password)
)

const showPassword = ref(false)
const showConfirm = ref(false)

// Submit ke backend
const submit = () => {
    form['g-recaptcha-response'] = grecaptcha.getResponse()
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
            grecaptcha.reset()
        }
    })
}

// Load script reCAPTCHA
onMounted(() => {
    const script = document.createElement('script')
    script.src = 'https://www.google.com/recaptcha/api.js'
    script.async = true
    script.defer = true
    document.head.appendChild(script)
})
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <!-- Name -->
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Username -->
            <div class="mt-4">
                <InputLabel for="username" value="Username" />
                <TextInput id="username" type="text" class="mt-1 block w-full" v-model="form.username" required />
                <InputError class="mt-2" :message="form.errors.username" />
                <p v-if="usernameHasSpace" class="text-sm text-red-500 mt-1">Username tidak boleh mengandung spasi.</p>
            </div>

            <!-- Email -->
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <div class="relative">
                    <TextInput :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full pr-10" id="password"
                        v-model="form.password" required />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-2 text-sm text-gray-500 focus:outline-none">
                        {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
                    </button>
                </div>

                <!-- Realtime Validasi -->
                <p class="text-sm font-semi text-gray-700 mt-2">Password Policy</p>
                <div class="mt-2 rounded-md px-4 py-2 shadow-sm text-sm space-y-1 text-gray-700 transition"
                    :class="{
                        'bg-red-100 border border-red-300': !isPasswordLongEnough,
                        'bg-yellow-100 border border-yellow-300': isPasswordLongEnough && !isPasswordStrong,
                        'bg-blue-100 border border-blue-300': isPasswordLongEnough && isPasswordStrong && form.password !== form.password_confirmation,
                        'bg-green-100 border border-green-300': isPasswordLongEnough && isPasswordStrong && form.password === form.password_confirmation
                    }">
                    <ul>
                        <li v-if="!isPasswordLongEnough" class="text-red-600">Minimal 8 karakter</li>
                        <li v-else-if="!isPasswordStrong" class="text-yellow-700">Gunakan huruf besar, kecil, angka, dan simbol</li>
                        <li v-else-if="form.password !== form.password_confirmation" class="text-green-700">
                            Konfirmasi password belum sama
                        </li>
                        <li v-else class="text-green-700 font-medium">✓ Password valid dan cocok!</li>
                    </ul>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Konfirmasi Password -->
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="relative">
                    <TextInput :type="showConfirm ? 'text' : 'password'" id="password_confirmation"
                        class="mt-1 block w-full pr-10" v-model="form.password_confirmation" required />
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-2 text-sm text-gray-500 focus:outline-none">
                        {{ showConfirm ? 'Sembunyikan' : 'Lihat' }}
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Google reCAPTCHA -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Verifikasi Keamanan</label>
                <div
                    class="w-full rounded-md border border-gray-300 bg-white px-4 py-3 shadow-sm flex items-center justify-center">
                    <div class="g-recaptcha" :data-sitekey="siteKey"></div>
                </div>
                <InputError class="mt-2" :message="form.errors['g-recaptcha-response']" />
            </div>

            <!-- Submit -->
            <div class="mt-4 flex items-center justify-end">
                <Link :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Already registered?
                </Link>
                <PrimaryButton class="ms-4"
                    :class="{ 'opacity-25': form.processing || usernameHasSpace || !isPasswordLongEnough || !isPasswordStrong }"
                    :disabled="form.processing || usernameHasSpace || !isPasswordLongEnough || !isPasswordStrong">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
