<template>
    <button
        @click="toggleTheme"
        class="rounded-lg p-3 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105"
        :title="isDark ? 'Mode clair' : 'Mode sombre'"
    >
        <SunIcon v-if="isDark" class="h-5 w-5 text-yellow-500" />
        <MoonIcon v-else class="h-5 w-5 text-gray-600" />
    </button>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'

const isDark = ref(false)

const toggleTheme = () => {
    isDark.value = !isDark.value
    const theme = isDark.value ? 'dark' : 'light'
    
    // Update DOM
    if (isDark.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
    
    // Save preference
    localStorage.setItem('theme', theme)
}

onMounted(() => {
    // Check system preference and saved preference
    const savedTheme = localStorage.getItem('theme')
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    
    isDark.value = savedTheme ? savedTheme === 'dark' : systemDark
    
    if (isDark.value) {
        document.documentElement.classList.add('dark')
    }
})
</script>