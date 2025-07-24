<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
            <p class="mt-1 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <!-- Current Password -->
            <div>
                <InputLabel for="current_password" value="Current Password" />
                <div class="relative">
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        :type="showCurrent ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        @click="showCurrent = !showCurrent"
                        class="absolute inset-y-0 right-2 text-sm text-gray-500 focus:outline-none"
                    >
                        {{ showCurrent ? 'Sembunyikan' : 'Lihat' }}
                    </button>
                </div>
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <!-- New Password -->
            <div>
                <InputLabel for="password" value="New Password" />
                <div class="relative">
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        :type="showNew ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        @click="showNew = !showNew"
                        class="absolute inset-y-0 right-2 text-sm text-gray-500 focus:outline-none"
                    >
                        {{ showNew ? 'Sembunyikan' : 'Lihat' }}
                    </button>
                </div>
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="relative">
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-2 text-sm text-gray-500 focus:outline-none"
                    >
                        {{ showConfirm ? 'Sembunyikan' : 'Lihat' }}
                    </button>
                </div>
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
