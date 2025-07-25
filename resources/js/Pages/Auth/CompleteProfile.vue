<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const form = useForm({
    username: '',
    password: '',
    password_confirmation: '',
})

const usernameError = ref('')
const showPassword = ref(false)
const isPasswordLongEnough = ref(false)
const isPasswordStrong = ref(false)

// Validasi username
watch(() => form.username, (val) => {
    usernameError.value = val.includes(' ') ? 'Username tidak boleh mengandung spasi' : ''
})

// Validasi password
watch(() => form.password, (val) => {
    isPasswordLongEnough.value = val.length >= 8
    isPasswordStrong.value =
        /[A-Z]/.test(val) &&
        /[a-z]/.test(val) &&
        /[0-9]/.test(val) &&
        /[\W_]/.test(val)
})

// Submit
const submit = () => {
    if (
        usernameError.value ||
        !isPasswordLongEnough.value ||
        !isPasswordStrong.value ||
        form.password !== form.password_confirmation
    ) return

    form.post('/complete-profile', {
        preserveScroll: true,
        onSuccess: () => router.visit('/dashboard')
    })
}
</script>

<template>
    <Head title="Lengkapi Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lengkapi Profil
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Alert -->
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg shadow-sm flex items-start gap-2">
                                <span class="text-sm font-bold pt-0.5">ⓘ</span>
                                <p class="text-sm font-medium leading-relaxed">
                                    Akun Anda belum memiliki <span class="font-semibold">username</span> dan <span class="font-semibold">password</span>. Silakan lengkapi terlebih dahulu.
                                </p>
                            </div>

                            <!-- Username -->
                            <div>
                                <InputLabel for="username" value="Username" />
                                <TextInput
                                    id="username"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.username"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="usernameError || form.errors.username" />
                            </div>

                            <!-- Password -->
                            <div>
                                <InputLabel for="password" value="Password" />
                                <div class="relative">
                                    <TextInput
                                        id="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="mt-1 block w-full pr-20"
                                        v-model="form.password"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        class="absolute right-3 top-2.5 text-sm text-gray-600 hover:text-gray-800"
                                        @click="showPassword = !showPassword"
                                    >
                                        {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div>
                                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    autocomplete="new-password"
                                />
                            </div>

                            <!-- Validasi Dinamis -->
                            <div
                                class="mt-2 rounded-md px-4 py-2 shadow-sm text-sm space-y-1 text-gray-700 transition"
                                :class="{
                                    'bg-slate-200 border border-slate-400': form.password === '',
                                    'bg-red-100 border border-red-300': form.password !== '' && !isPasswordLongEnough,
                                    'bg-yellow-100 border border-yellow-300': isPasswordLongEnough && !isPasswordStrong,
                                    'bg-blue-100 border border-blue-300': isPasswordLongEnough && isPasswordStrong && form.password !== form.password_confirmation,
                                    'bg-green-100 border border-green-300': isPasswordLongEnough && isPasswordStrong && form.password === form.password_confirmation
                                }"
                            >
                                <ul>
                                    <li v-if="form.password === ''" class="text-gray-500">
                                        Masukkan password baru
                                    </li>
                                    <li v-else-if="!isPasswordLongEnough" class="text-red-600">
                                        Minimal 8 karakter
                                    </li>
                                    <li v-else-if="!isPasswordStrong" class="text-yellow-700">
                                        Gunakan huruf besar, kecil, angka, dan simbol
                                    </li>
                                    <li v-else-if="form.password !== form.password_confirmation" class="text-blue-700">
                                        Konfirmasi password belum sama
                                    </li>
                                    <li v-else class="text-green-700 font-medium">
                                        ✓ Password valid dan cocok!
                                    </li>
                                </ul>
                            </div>

                            <!-- Error dari server -->
                            <InputError class="mt-2" :message="form.errors.password" />

                            <!-- Submit -->
                            <PrimaryButton
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan & Masuk' }}
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
