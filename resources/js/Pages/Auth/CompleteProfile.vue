<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

// Form state
const form = useForm({
    username: '',
    password: '',
})

// State tambahan
const usernameError = ref('')
const passwordErrors = ref([])
const showPassword = ref(false) // 👁️ toggle password visibility

// Validasi realtime: Username
watch(() => form.username, (val) => {
    usernameError.value = val.includes(' ') ? 'Username tidak boleh mengandung spasi' : ''
})

// Validasi realtime: Password
watch(() => form.password, (val) => {
    const errors = []

    if (val.length < 8) {
        errors.push('Minimal 8 karakter')
    }
    if (!/[A-Z]/.test(val)) {
        errors.push('Harus ada huruf besar')
    }
    if (!/[a-z]/.test(val)) {
        errors.push('Harus ada huruf kecil')
    }
    if (!/[0-9]/.test(val)) {
        errors.push('Harus ada angka')
    }
    if (!/[\W_]/.test(val)) {
        errors.push('Harus ada simbol')
    }

    passwordErrors.value = errors
})

// Submit form
const submit = () => {
    if (usernameError.value || passwordErrors.value.length > 0) return

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

                            <!-- Informasi alert -->
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

                                    <!-- Toggle show password -->
                                    <button
                                        type="button"
                                        class="absolute right-3 top-2.5 text-sm text-gray-600 hover:text-gray-800 focus:outline-none"
                                        @click="showPassword = !showPassword"
                                    >
                                        {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
                                    </button>
                                </div>

                                <!-- Info bantuan -->
                                <p class="text-sm text-gray-500 mt-1">
                                    Password minimal 8 karakter, dan mengandung huruf besar, huruf kecil, angka, serta simbol.
                                </p>

                                <!-- Error realtime -->
                                <div v-if="passwordErrors.length" class="mt-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-4 py-2">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li v-for="(error, index) in passwordErrors" :key="index">{{ error }}</li>
                                    </ul>
                                </div>

                                <!-- Error dari server -->
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <!-- Tombol Submit -->
                            <PrimaryButton :disabled="form.processing">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan & Masuk' }}
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
