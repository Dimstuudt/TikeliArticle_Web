<script setup>
import { ref } from "vue"
import { Head, useForm } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import PrimaryButton from "@/Components/PrimaryButton.vue"
import DangerButton from "@/Components/DangerButton.vue"
import Modal from "@/Components/Modal.vue"
import Swal from "sweetalert2"

const props = defineProps({
  roles: Array,
  permissions: Array,
})

// STATE
const showRoleModal = ref(false)
const showPermissionModal = ref(false)
const editMode = ref(false)
const selectedRole = ref(null)

// FORMS
const roleForm = useForm({ name: "", permissions: [] })
const permissionForm = useForm({ name: "" })

// ===== MODAL HANDLING =====
const openRoleModal = (role = null) => {
  showRoleModal.value = true
  editMode.value = !!role
  selectedRole.value = role

  if (role) {
    roleForm.name = role.name
    roleForm.permissions = role.permissions.map(p => p.name)
  } else roleForm.reset()
}

const closeRoleModal = () => {
  roleForm.reset()
  showRoleModal.value = false
}

const openPermissionModal = () => {
  showPermissionModal.value = true
  permissionForm.reset()
}

const closePermissionModal = () => {
  permissionForm.reset()
  showPermissionModal.value = false
}

// ===== ROLE ACTIONS =====
const saveRole = () => {
  const routeName = editMode.value ? "admin.roles.update" : "admin.roles.store"
  const method = editMode.value ? "put" : "post"
  const id = editMode.value ? selectedRole.value.id : undefined

  roleForm[method](route(routeName, id), {
    preserveScroll: true,
    onSuccess: () => {
      closeRoleModal()
      Swal.fire({
        icon: 'success',
        title: editMode.value ? 'Role berhasil diperbarui' : 'Role berhasil ditambahkan',
        timer: 1500,
        showConfirmButton: false
      })
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Gagal simpan role',
        text: 'Coba lagi nanti'
      })
    }
  })
}

// 🔹 SweetAlert delete role
const deleteRole = (id) => {
  Swal.fire({
    title: 'Yakin hapus role ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      roleForm.delete(route("admin.roles.destroy", id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Role berhasil dihapus',
            timer: 1500,
            showConfirmButton: false
          })
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal hapus role',
            text: 'Coba lagi nanti'
          })
        }
      })
    }
  })
}

// ===== PERMISSION ACTIONS =====
const savePermission = () => {
  permissionForm.post(route("admin.permissions.store"), {
    preserveScroll: true,
    onSuccess: () => {
      closePermissionModal()
      Swal.fire({
        icon: 'success',
        title: 'Permission berhasil ditambahkan',
        timer: 1500,
        showConfirmButton: false
      })
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Gagal simpan permission',
        text: 'Coba lagi nanti'
      })
    }
  })
}

// 🔹 SweetAlert delete permission
const deletePermission = (id) => {
  Swal.fire({
    title: 'Yakin hapus permission ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      permissionForm.delete(route("admin.permissions.destroy", id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Permission berhasil dihapus',
            timer: 1500,
            showConfirmButton: false
          })
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal hapus permission',
            text: 'Coba lagi nanti'
          })
        }
      })
    }
  })
}
</script>


<template>
  <Head title="Role & Permission Management" />

  <AuthenticatedLayout>
   <template #header>
  <!-- Wrapper full width -->
  <div class="w-full flex justify-between items-center bg-blue-100 border border-blue-200 rounded-lg px-4 py-3 shadow-sm">

    <!-- Judul -->
    <div class="flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg"
           class="h-6 w-6 text-blue-600"
           fill="none"
           viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.534 6.121 1.475M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <h2 class="text-lg sm:text-xl font-semibold text-blue-800 truncate">Manajemen Role & Permission</h2>
    </div>

    <!-- Tombol ke Artikel -->
    <Link
      :href="route('guest.welcome')"
      class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded-lg shadow transition"
    >
      📖 Lihat Artikel
    </Link>
  </div>
</template>


    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

      <!-- ROLE TABLE -->
      <section class="bg-white shadow-xl sm:rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-700">Daftar Role</h3>
          <PrimaryButton @click="openRoleModal()">Tambah Role</PrimaryButton>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2">Nama Role</th>
                <th class="px-4 py-2">Permissions</th>
                <th class="px-4 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="role in props.roles" :key="role.id" class="border-t">
                <td class="px-4 py-2 font-medium">{{ role.name }}</td>
                <td class="px-4 py-2">
                  <span
                    v-for="p in role.permissions"
                    :key="p.id"
                    class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1 mb-1"
                  >
                    {{ p.name }}
                  </span>
                </td>
                <td class="px-4 py-2 flex gap-2">
                  <PrimaryButton size="sm" @click="openRoleModal(role)">Edit</PrimaryButton>
                  <DangerButton size="sm" @click="deleteRole(role.id)">Hapus</DangerButton>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- PERMISSION TABLE -->
      <section class="bg-white shadow-xl sm:rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-700">Daftar Permission</h3>
          <PrimaryButton @click="openPermissionModal()">Tambah Permission</PrimaryButton>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2">Nama Permission</th>
                <th class="px-4 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="permission in props.permissions" :key="permission.id" class="border-t">
                <td class="px-4 py-2 font-medium">{{ permission.name }}</td>
                <td class="px-4 py-2">
                  <DangerButton size="sm" @click="deletePermission(permission.id)">Hapus</DangerButton>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- MODAL ROLE -->
    <Modal :show="showRoleModal" @close="closeRoleModal">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editMode ? "Edit Role" : "Tambah Role" }}</h2>
        <form @submit.prevent="saveRole" class="space-y-4">
          <div>
            <label class="block text-sm font-medium">Nama Role</label>
            <input
              v-model="roleForm.name"
              type="text"
              class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Masukkan nama role"
            />
          </div>

          <!-- PERMISSIONS CHECKBOX -->
          <div>
            <label class="block text-sm font-medium mb-2">Permissions</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-60 overflow-y-auto p-2 border rounded-md">
              <label
                v-for="p in props.permissions"
                :key="p.id"
                class="flex items-center space-x-2 bg-gray-50 rounded px-2 py-1 hover:bg-gray-100 cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="p.name"
                  v-model="roleForm.permissions"
                  class="form-checkbox h-4 w-4 text-blue-600"
                />
                <span class="text-gray-700 text-sm">{{ p.name }}</span>
              </label>
            </div>
          </div>

          <div class="flex justify-end gap-2 mt-6">
            <DangerButton type="button" @click="closeRoleModal">Batal</DangerButton>
            <PrimaryButton type="submit" :disabled="roleForm.processing">
              {{ editMode ? "Simpan" : "Tambah" }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- MODAL PERMISSION -->
    <Modal :show="showPermissionModal" @close="closePermissionModal">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">Tambah Permission</h2>
        <form @submit.prevent="savePermission" class="space-y-4">
          <div>
            <label class="block text-sm font-medium">Nama Permission</label>
            <input
              v-model="permissionForm.name"
              type="text"
              class="w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Masukkan nama permission"
            />
          </div>

          <div class="flex justify-end gap-2 mt-6">
            <DangerButton type="button" @click="closePermissionModal">Batal</DangerButton>
            <PrimaryButton type="submit" :disabled="permissionForm.processing">Tambah</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
