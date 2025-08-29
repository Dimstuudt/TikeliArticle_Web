<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from "vue"
import { usePage, Link } from "@inertiajs/vue3"
import Dropdown from "@/Components/Dropdown.vue"
import DropdownLink from "@/Components/DropdownLink.vue"
import "@fortawesome/fontawesome-free/css/all.min.css";

// Tambahkan import SearchBar di sini
import SearchBar from "@/Components/SearchBar.vue"

// ===== User =====
const page = usePage()
const user = computed(() => page.props.auth?.user || null)

const getDashboardRoute = () => {
  if (!user.value) return "/"
  return user.value.role === "admin"
    ? route("admin.dashboard")
    : route("operator.dashboard")
}

// ===== Dark Mode =====
const isDark = ref(localStorage.getItem("theme") === "dark")

if (isDark.value) document.documentElement.classList.add("dark")

watch(isDark, (val) => {
  if (val) {
    document.documentElement.classList.add("dark")
    localStorage.setItem("theme", "dark")
  } else {
    document.documentElement.classList.remove("dark")
    localStorage.setItem("theme", "light")
  }
})

// ===== Scroll To Top Button =====
const showScrollTop = ref(false)

const handleScroll = () => {
  showScrollTop.value = window.scrollY > 200
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: "smooth" })
}

onMounted(() => {
  window.addEventListener("scroll", handleScroll)
})

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll)
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col transition-colors duration-300">

   <!-- Navbar -->
<header class="bg-blue-600 dark:bg-gray-800 text-white shadow px-6 py-3 flex justify-between items-center">
  <!-- Logo -->
  <Link href="/welcome" class="text-2xl font-bold">Tikeli</Link>

  <!-- Tengah: Search + Menu -->
  <div class="flex-1 mx-4 flex items-center justify-center md:justify-start space-x-6">
    <!-- Search -->
    <div class="w-full max-w-md ml-4">
      <SearchBar />
    </div>

    <!-- Menu Links -->
    <nav class="hidden md:flex space-x-6 ml-6">
      <Link href="/welcome" class="hover:text-yellow-300">Articles</Link>
        <Link href="/forum" class="hover:text-yellow-300">Forum</Link>
      <Link href="/about" class="hover:text-yellow-300">About</Link>
    </nav>


  </div>

  <!-- Kanan: Dark mode + Auth -->
  <div class="flex items-center space-x-4">

     <!-- Sosmed -->
<div class="hidden md:flex space-x-4">
  <a href="#" class="hover:text-yellow-300">
    <i class="fab fa-facebook-f"></i>
  </a>
  <a href="#" class="hover:text-yellow-300">
    <i class="fab fa-instagram"></i>
  </a>
  <a href="#" class="hover:text-yellow-300">
    <i class="fab fa-twitter"></i>
  </a>
</div>


    <!-- Dark Mode Switch -->
    <button
      @click="isDark = !isDark"
      class="relative h-6 w-14 rounded-full bg-gray-300 dark:bg-gray-600 p-1 flex items-center transition-colors duration-300 focus:outline-none hover:ring-2 hover:ring-blue-400 dark:hover:ring-yellow-400"
    >
      <span
        class="absolute left-1 h-2.5 w-2.5 bg-yellow-400 rounded-full transition-opacity duration-300"
        :class="isDark ? 'opacity-0' : 'opacity-100'"
      ></span>
      <span
        class="absolute right-1 h-2.5 w-2.5 bg-gray-800 rounded-full transition-opacity duration-300"
        :class="isDark ? 'opacity-100' : 'opacity-0'"
      ></span>
      <span
        class="inline-block h-5 w-5 rounded-full shadow-md transform transition-all duration-300 ease-in-out cursor-pointer"
        :class="[isDark ? 'translate-x-8 bg-gray-800' : 'translate-x-0 bg-yellow-400','shadow-lg hover:scale-110 active:scale-95']"
      ></span>
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
            <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
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
          <DropdownLink method="post" :href="route('logout')" as="button">Keluar</DropdownLink>
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
    <main class="flex-grow text-gray-900 dark:text-gray-100 transition-colors duration-300">
      <slot />
    </main>

   <!-- Footer -->
<footer class="bg-gradient-to-r from-blue-700 to-blue-800 dark:from-gray-800 dark:to-gray-900 text-white mt-8">
  <div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left">

    <!-- Brand -->
    <div>
      <h2 class="text-2xl font-bold">Tikeli</h2>
      <p class="mt-2 text-sm text-gray-200">
        Platform artikel yang menghadirkan informasi, opini, dan hiburan berkualitas.
      </p>
    </div>

    <!-- Quick Links -->
    <div>
      <h3 class="font-semibold mb-3">Navigasi</h3>
      <ul class="space-y-2 text-sm">
        <li><Link href="/welcome" class="hover:text-yellow-300 transition">Home</Link></li>
        <li><Link href="/about" class="hover:text-yellow-300 transition">Tentang</Link></li>
        <li><Link href="/contact" class="hover:text-yellow-300 transition">Kontak</Link></li>
        <li><Link href="/privacy" class="hover:text-yellow-300 transition">Kebijakan Privasi</Link></li>
        <li><Link href="/terms" class="hover:text-yellow-300 transition">Syarat dan Ketentuan</Link></li>
      </ul>
    </div>

    <!-- Social Media -->
    <div class="hidden md:flex space-x-4">
  <a href="https://facebook.com" target="_blank" class="hover:text-yellow-300">
    <i class="fab fa-facebook-f"></i>
  </a>
  <a href="https://instagram.com" target="_blank" class="hover:text-yellow-300">
    <i class="fab fa-instagram"></i>
  </a>
  <a href="https://twitter.com" target="_blank" class="hover:text-yellow-300">
    <i class="fab fa-twitter"></i>
  </a>
</div>

  </div>

  <div class="border-t border-gray-600 text-center py-3 text-xs text-gray-300">
    &copy; 2025 Tikeli. All rights reserved.
  </div>
</footer>


    <!-- Floating Scroll to Top Button -->
    <transition name="fade">
      <button
        v-if="showScrollTop"
        @click="scrollToTop"
        class="fixed bottom-6 right-6 flex items-center justify-center w-12 h-12 rounded-full
               bg-blue-600 text-white shadow-lg hover:bg-blue-700 dark:bg-gray-700 dark:hover:bg-gray-600
               transition-all duration-300 focus:outline-none hover:scale-110 active:scale-95"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
      </button>
    </transition>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
