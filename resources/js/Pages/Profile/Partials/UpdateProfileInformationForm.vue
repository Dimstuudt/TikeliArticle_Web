<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue' // Tambahkan ini

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

// Form utama untuk update nama dan email
const form = useForm({
    name: user.name,
    email: user.email,
    username: user.username, // ✅ tambahkan ini
});

// Form foto
const photo = ref(null);
const photoPreview = ref(null); // ✅ tambahan

// Saat user pilih file
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    photo.value = file;

    // ✅ preview sebelum upload
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Saat form upload disubmit
function submitPhoto() {
    if (!photo.value) return;

    const formData = new FormData();
    formData.append('photo', photo.value);

    router.post(route('profile.photo.update'), formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            photo.value = null;
            photoPreview.value = null;
            router.reload({ only: ['auth'] }); // ✅ reload user biar foto baru langsung muncul
        },
    });
}


const usernameError = ref('')

// Validasi: username tidak boleh pakai spasi
watch(() => form.username, (val) => {
    usernameError.value = val.includes(' ') ? 'Username tidak boleh mengandung spasi' : ''
})


</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <!-- ✅ Form Foto Profil -->
        <form @submit.prevent="submitPhoto" class="mt-6">
            <InputLabel for="photo" value="Profile Photo" />

            <div class="flex items-center mt-2">
                <!-- Tampilkan foto -->
                <img
                    :src="photoPreview ? photoPreview : user.profile_photo_url"
                    alt="Profile Photo"
                    class="h-16 w-16 rounded-full object-cover mr-4"
                />

                <input
                    id="photo"
                    type="file"
                    @change="handleFileUpload"
                    accept="image/*"
                    class="block"
                />
            </div>

            <PrimaryButton class="mt-2">Upload Photo</PrimaryButton>
        </form>

        <!-- ✅ Form Nama & Email -->
        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

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


            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
