<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

const showingNavigationDropdown = ref(false)
const role = usePage().props.auth.user.role
const user = usePage().props.auth.user
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-blue-200 border-b border-blue-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Left: Logo dan Nav -->
          <div class="flex items-center space-x-10">
            <!-- Logo Tikeli -->
            <Link
              :href="route('dashboard')"
              class="text-2xl font-extrabold text-blue-600 tracking-tight hover:text-blue-700 transition"
            >
              Tikeli
            </Link>

            <!-- Menu -->
            <div class="hidden sm:flex space-x-8">
              <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</NavLink>

              <template v-if="role === 'admin'">
                <NavLink :href="route('admin.users')" :active="route().current('admin.users')">Users</NavLink>
                <NavLink :href="route('admin.articles.index')" :active="route().current('admin.articles.index')">
                  Manajemen Artikel
                </NavLink>
              </template>

              <template v-if="role === 'operator'">
                <NavLink :href="route('operator.articles.create')" :active="route().current('operator.articles.create')">
                  Tulis Artikel
                </NavLink>
                <NavLink :href="route('operator.articles.mine')" :active="route().current('operator.articles.mine')">
                  Artikel Saya
                </NavLink>
              </template>
            </div>
          </div>

          <!-- Right: Profil & Dropdown -->
          <div class="hidden sm:flex sm:items-center">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="flex items-center text-sm font-medium text-gray-700 hover:text-blue-700 focus:outline-none">
                  <img
                    :src="user.profile_photo_url"
                    class="h-9 w-9 rounded-full object-cover border-2 border-blue-500"
                    alt="User"
                  />
                  <span class="ml-2">{{ user.name }}</span>
                  <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </template>

              <template #content>
                <DropdownLink :href="route('profile.edit')">Data Saya</DropdownLink>
                <DropdownLink :href="route('my.profile')">Profil Saya</DropdownLink>
                <DropdownLink method="post" :href="route('logout')" as="button">Keluar</DropdownLink>
              </template>
            </Dropdown>
          </div>

          <!-- Hamburger Menu (Mobile) -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center p-2 rounded-md text-gray-500 hover:text-blue-700 hover:bg-blue-50 focus:outline-none"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  v-if="!showingNavigationDropdown"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  v-else
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-blue-50">
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Dashboard
          </ResponsiveNavLink>

          <template v-if="role === 'admin'">
            <ResponsiveNavLink :href="route('admin.users')" :active="route().current('admin.users')">Users</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('admin.articles.index')" :active="route().current('admin.articles.index')">Manajemen Artikel</ResponsiveNavLink>
          </template>

          <template v-if="role === 'operator'">
            <ResponsiveNavLink :href="route('operator.articles.create')" :active="route().current('operator.articles.create')">Tulis Artikel</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('operator.articles.mine')" :active="route().current('operator.articles.mine')">Artikel Saya</ResponsiveNavLink>
          </template>
        </div>

        <div class="border-t border-blue-200 pt-4 pb-1">
          <div class="px-4">
            <div class="text-base font-medium text-blue-700">{{ user.name }}</div>
            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.edit')">Profil</ResponsiveNavLink>
            <ResponsiveNavLink method="post" :href="route('logout')" as="button">Keluar</ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Header -->
    <header v-if="$slots.header" class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Main Content -->
    <main>
      <slot />
    </main>
  </div>
</template>
