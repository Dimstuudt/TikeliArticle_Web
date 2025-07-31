<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  users: Array
});

const showModal = ref(false);
const editMode = ref(false);
const selectedUser = ref(null);

const form = useForm({
  name: '',
  email: '',
  username: '',
  password: '',
  password_confirmation: '',
  role: 'operator',
});

const openModal = (user = null) => {
  showModal.value = true;
  editMode.value = !!user;
  selectedUser.value = user;
  if (user) {
    form.name = user.name;
    form.email = user.email;
    form.username = user.username;
    form.role = user.role;
    form.password = '';
    form.password_confirmation = '';
  } else {
    form.reset();
  }
};

const closeModal = () => {
  showModal.value = false;
  form.reset();
};

const saveUser = () => {
  if (editMode.value) {
    form.put(route('admin.users.update', selectedUser.value.id), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('admin.users.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  }
};

const deleteUser = (id) => {
  if (confirm('Yakin hapus user ini?')) {
    form.delete(route('admin.users.destroy', id));
  }
};

const isPasswordLongEnough = computed(() => form.password.length >= 8);
const isPasswordStrong = computed(() => /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])/.test(form.password));
</script>

<template>
  <Head title="Manajemen User" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen User</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-700">Daftar User</h3>
            <PrimaryButton @click="openModal()">Tambah User</PrimaryButton>
          </div>

          <table class="w-full text-sm text-left">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" class="border-t">
                <td class="px-4 py-2">{{ user.name }}</td>
                <td class="px-4 py-2">{{ user.username }}</td>
                <td class="px-4 py-2">{{ user.email }}</td>
                <td class="px-4 py-2">{{ user.role }}</td>
                <td class="px-4 py-2 space-x-2">
                  <PrimaryButton @click="openModal(user)">Edit</PrimaryButton>
                  <DangerButton @click="deleteUser(user.id)">Hapus</DangerButton>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Modal :show="showModal" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">
          {{ editMode ? 'Edit User' : 'Tambah User' }}
        </h2>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input v-model="form.name" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" type="text" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="form.email" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" type="email" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input v-model="form.username" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" type="text" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select v-model="form.role" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
              <option value="admin">Admin</option>
              <option value="operator">Operator</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input v-model="form.password" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" type="password" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
            <input v-model="form.password_confirmation" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" type="password" />
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
              <li v-else-if="form.password !== form.password_confirmation" class="text-green-700">Konfirmasi password belum sama</li>
              <li v-else class="text-green-700 font-medium">✓ Password valid dan cocok!</li>
            </ul>
          </div>

          <div class="mt-6 flex justify-end space-x-2">
            <DangerButton type="button" @click="closeModal">Batal</DangerButton>
            <PrimaryButton type="submit" :disabled="form.processing">
              {{ editMode ? 'Simpan Perubahan' : 'Tambah User' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
