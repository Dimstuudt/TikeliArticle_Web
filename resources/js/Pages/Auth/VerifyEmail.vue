<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => {
    return props.status === 'verification-link-sent';
});
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600">
            Thank you for registering! Please verify your email address by clicking the link we just sent you.
            If you didn’t receive the email, we will send you another.
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 text-sm font-medium text-green-600"
        >
            A new verification link has been sent to your email address.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="ml-4 text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none"
                >
                    Log Out
                </Link>
            </div>

<br>

<div class="mt-6 text-center text-sm text-gray-600">
    Sudah verifikasi?
    <Link
        :href="route('login')"
        class="text-blue-600 underline hover:text-blue-800">
        Tekan di sini untuk login kembali
    </Link>
</div>


        </form>
    </GuestLayout>
</template>
