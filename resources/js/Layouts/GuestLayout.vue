<script setup lang="ts">
// import ApplicationLogo from '@/Components/ApplicationLogo.vue'
// import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
    <!-- <div>
      <Link href="/">
        <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
      </Link>
    </div> -->

    <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-2xl sm:rounded-lg">
      <slot />
    </div>
  </div>
</template>

<!--<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Theme Toggle (coin supérieur droit)
        <div class="absolute top-4 right-4 z-50">
            <ThemeToggle />
        </div>

        <slot />
    </div>
</template>

<script setup>
import ThemeToggle from '@/Components/ThemeToggle.vue'
</script> -->
