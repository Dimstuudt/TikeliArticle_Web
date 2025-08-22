<script setup>
import { ref, watch, computed } from "vue"
import { usePage, Link } from "@inertiajs/vue3"
import Dropdown from "@/Components/Dropdown.vue"
import DropdownLink from "@/Components/DropdownLink.vue"

const page = usePage()
const user = computed(() => page.props.auth?.user || null)

const getDashboardRoute = () => {
  if (!user.value) return "/"
  return user.value.role === "admin"
    ? route("admin.dashboard")
    : route("operator.dashboard")
}

// dark mode state
const isDark = ref(localStorage.getItem("theme") === "dark")

// apply saat mounted
if (isDark.value) {
  document.documentElement.classList.add("dark")
}

// toggle theme
watch(isDark, (val) => {
  if (val) {
    document.documentElement.classList.add("dark")
    localStorage.setItem("theme", "dark")
  } else {
    document.documentElement.classList.remove("dark")
    localStorage.setItem("theme", "light")
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col transition-colors">
    <!-- Navbar -->
    <header
      class="bg-blue-600 dark:bg-gray-800 text-white shadow px-6 py-3 flex justify-between items-center"
    >
      <!-- Logo -->
      <Link href="/welcome" class="text-2xl font-bold">Tikeli</Link>

      <!-- Right side -->
      <div class="flex items-center space-x-4">
        <!-- Dark Mode Switch -->
        <button
          @click="isDark = !isDark"
          class="relative inline-flex items-center h-6 rounded-full w-11 bg-gray-300 dark:bg-gray-600 transition"
        >
          <span
            class="absolute left-0.5 top-0.5 inline-block h-5 w-5 transform rounded-full bg-white dark:bg-yellow-400 transition"
            :class="isDark ? 'translate-x-5' : ''"
          />
        </button>

        <!-- Auth -->
        <template v-if="user">
          <Dropdown align="right" width="48">
            <template #trigger>
              <button
                class="flex items-center px-3 py-1 rounded-md bg-blue-400 hover:bg-blue-500 dark:bg-gray-700 dark:hover:bg-gray-600 transition text-sm font-medium focus:outline-none"
              >
                <img
                  :src="user.profile_photo_url"
                  class="h-7 w-7 rounded-full object-cover border-2 border-white"
                  alt="User"
                />
                <span class="ml-2">{{ user.name }}</span>
                <svg
                  class="ml-2 h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </template>

            <template #content>
              <DropdownLink :href="getDashboardRoute()">Dashboard</DropdownLink>
              <DropdownLink
                method="post"
                :href="route('logout')"
                as="button"
                >Keluar</DropdownLink
              >
            </template>
          </Dropdown>
        </template>

        <template v-else>
          <Link
            href="/login"
            class="px-4 py-2 rounded-md bg-blue-400 hover:bg-blue-500 dark:bg-gray-700 dark:hover:bg-gray-600 transition text-sm font-semibold"
          >
            Login / Register
          </Link>
        </template>
      </div>
    </header>

    <!-- Main -->
    <main class="flex-grow text-gray-900 dark:text-gray-100 transition-colors">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-blue-700 dark:bg-gray-800 text-white text-center py-4 text-sm">
      &copy; 2025 Tikeli. All rights reserved.
    </footer>
  </div>
</template>
