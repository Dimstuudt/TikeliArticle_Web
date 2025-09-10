<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import DropdownLink from '@/Components/DropdownLink.vue'
import {
  LayoutDashboard,
  Users,
  ShieldCheck,
  FileText,
  CheckCircle,
  PenSquare,
  BookOpen,
  BarChart2,
  User,
  LogOut,
  Menu
} from 'lucide-vue-next'

const sidebarOpen = ref(false)
const user = usePage().props.auth.user
const permissions = user?.permissions || []

// ===== Permission Checks =====
const canManageUsers = computed(() => permissions.includes('manage users'))
const canManageArticles = computed(() => permissions.includes('manage articles'))
const canViewReports = computed(() => permissions.includes('view reports'))
const canWriteArticles = computed(() => permissions.includes('write articles'))
const canManagePermissions = computed(() => permissions.includes('manage permissions'))

// Role badge
const roleLabel = computed(() => {
  const role = user?.roles?.[0] || 'User'
  switch (role) {
    case 'super-admin': return { text: 'Super Admin', color: 'bg-purple-500' }
    case 'admin': return { text: 'Admin', color: 'bg-green-500' }
    case 'operator': return { text: 'Writer', color: 'bg-yellow-500' }
    default: return { text: 'User', color: 'bg-gray-400' }
  }
})
</script>

<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside
      :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-64',
        'fixed inset-y-0 left-0 z-30 w-64 transform bg-white border-r border-gray-200 shadow-lg overflow-y-auto transition-transform duration-200 ease-in-out sm:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center justify-center h-16 border-b border-gray-200">
        <Link :href="route('dashboard')" class="text-2xl font-extrabold text-blue-600">
          Tikeli
        </Link>
      </div>

      <!-- User Profile -->
      <div class="flex items-center space-x-3 p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
        <img
          :src="user.profile_photo_url"
          class="h-12 w-12 rounded-full object-cover border-2 border-blue-500"
          alt="User"
        />
        <div>
          <p class="text-sm font-semibold text-gray-800">{{ user.name }}</p>
          <p class="text-xs text-gray-500 truncate max-w-[150px]">{{ user.email }}</p>
          <span class="mt-1 inline-block text-white text-xs font-semibold px-2 py-0.5 rounded" :class="roleLabel.color">
            {{ roleLabel.text }}
          </span>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-6">
        <!-- Dashboard -->
        <div>
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">General</p>
          <Link
            :href="route('dashboard')"
            :class="[
              route().current('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
              'flex items-center px-3 py-2 rounded-lg font-medium transition'
            ]"
          >
            <LayoutDashboard class="w-5 h-5 mr-2" />
            Dashboard
          </Link>

              <Link
            :href="route('guest.welcome')"
            :class="[
              route().current('guest.welcome') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
              'flex items-center px-3 py-2 rounded-lg font-medium transition'
            ]"
          >
            <LayoutDashboard class="w-5 h-5 mr-2" />
            Halaman Utama
          </Link>


        </div>

        <!-- Manajemen Akses -->
        <div v-if="canManageUsers || canManagePermissions">
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Manajemen Akses</p>
          <div class="space-y-1">
            <Link
              v-if="canManageUsers"
              :href="route('admin.users')"
              :class="[
                route().current('admin.users') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <Users class="w-5 h-5 mr-2" />
              Users
            </Link>

            <Link
              v-if="canManagePermissions"
              :href="route('admin.roles-permissions')"
              :class="[
                route().current('admin.roles-permissions') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <ShieldCheck class="w-5 h-5 mr-2" />
              Roles & Permissions
            </Link>
          </div>
        </div>

        <!-- Artikel -->
        <div v-if="canManageArticles || canWriteArticles">
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Artikel</p>
          <div class="space-y-1">
            <Link
              v-if="canManageArticles"
              :href="route('admin.articles.index')"
              :class="[
                route().current('admin.articles.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <FileText class="w-5 h-5 mr-2" />
              Manajemen Artikel
            </Link>

            <Link
              v-if="canManageArticles"
              :href="route('admin.approved-articles.index')"
              :class="[
                route().current('admin.approved-articles.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <CheckCircle class="w-5 h-5 mr-2" />
              Approved
            </Link>

            <Link
              v-if="canWriteArticles"
              :href="route('operator.articles.create')"
              :class="[
                route().current('operator.articles.create') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <PenSquare class="w-5 h-5 mr-2" />
              Tulis Artikel
            </Link>

            <Link
              v-if="canWriteArticles"
              :href="route('operator.articles.mine')"
              :class="[
                route().current('operator.articles.mine') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <BookOpen class="w-5 h-5 mr-2" />
              Artikel Saya
            </Link>
          </div>
        </div>

        <!-- Reports -->
        <div v-if="canViewReports">
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Reports</p>
          <Link
            :href="route('admin.reports.index')"
            :class="[
              route().current('admin.reports.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
              'flex items-center px-3 py-2 rounded-lg font-medium transition'
            ]"
          >
            <BarChart2 class="w-5 h-5 mr-2" />
            Reports
          </Link>
        </div>

        <!-- Akun -->
        <div>
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Akun</p>
          <div class="space-y-1">
            <Link
              :href="route('profile.edit')"
              :class="[
                route().current('profile.edit') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <User class="w-5 h-5 mr-2" />
              Data Saya
            </Link>

            <Link
              :href="route('my.profile')"
              :class="[
                route().current('my.profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700',
                'flex items-center px-3 py-2 rounded-lg font-medium transition'
              ]"
            >
              <User class="w-5 h-5 mr-2" />
              Profil Saya
            </Link>

            <DropdownLink method="post" :href="route('logout')" as="button" class="w-full text-left">
              <span class="flex items-center">
                <LogOut class="w-5 h-5 mr-2" />
                Keluar
              </span>
            </DropdownLink>
          </div>
        </div>
      </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-h-screen sm:ml-64 transition-all duration-200">
      <!-- Topbar -->
      <header class="bg-white shadow-sm h-16 flex items-center justify-between px-4">
        <button
          class="sm:hidden p-2 text-gray-500 hover:text-gray-700"
          @click="sidebarOpen = !sidebarOpen"
        >
          <Menu class="w-6 h-6" />
        </button>
        <slot name="header" />
      </header>

      <!-- Content -->
      <main class="flex-1 p-6 bg-gray-50">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="bg-blue-600 text-white py-6 mt-auto shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
          <p class="text-sm">&copy; {{ new Date().getFullYear() }} Tikeli. All rights reserved.</p>
          <nav class="flex gap-4 text-sm">
            <a href="/about" class="hover:underline">Tentang</a>
            <a href="/terms" class="hover:underline">Kebijakan</a>
            <a href="/contact" class="hover:underline">Kontak</a>
            <a href="/privacy" class="hover:underline">Privasi</a>
          </nav>
        </div>
      </footer>
    </div>
  </div>
</template>
