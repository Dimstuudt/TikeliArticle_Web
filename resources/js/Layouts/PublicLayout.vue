<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)

const getDashboardRoute = () => {
  if (!user.value) return '/'
  return user.value.role === 'admin' ? route('admin.dashboard') : route('operator.dashboard')
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Navbar -->
    <header class="bg-blue-600 text-white shadow py-4 px-6 flex justify-between items-center">
      <Link href="/welcome" class="text-2xl font-bold text-white">Tikeli</Link>

      <div class="flex items-center space-x-4">
        <template v-if="user">
          <Dropdown align="right" width="48">
            <template #trigger>
              <button class="flex items-center text-sm text-white hover:underline focus:outline-none">
                <img
                  :src="user.profile_photo_url"
                  class="h-8 w-8 rounded-full object-cover border-2 border-white"
                  alt="User"
                />
                <span class="ml-2 font-medium">{{ user.name }}</span>
                <svg class="ml-2 h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </template>

            <template #content>
              <DropdownLink :href="getDashboardRoute()">Dashboard</DropdownLink>
              <DropdownLink method="post" :href="route('logout')" as="button">Keluar</DropdownLink>
            </template>
          </Dropdown>
        </template>

        <template v-else>
          <Link href="/login" class="text-sm hover:underline text-white font-medium">Login / Register</Link>
        </template>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-blue-700 text-white text-center py-4 text-sm">
      &copy; 2025 Tikeli. All rights reserved.
    </footer>
  </div>
</template>
