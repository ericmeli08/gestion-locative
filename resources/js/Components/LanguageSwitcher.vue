<template>
  <Menu as="div" class="relative">
    <MenuButton
      class="flex items-center rounded-md p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:text-gray-300 dark:hover:text-gray-200"
    >
      <LanguageIcon class="h-5 w-5" />
      <span class="ml-1 text-sm font-medium">{{ currentLanguage.toUpperCase() }}</span>
    </MenuButton>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <MenuItems
        class="absolute right-0 z-10 mt-2 w-32 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
      >
        <div class="py-1">
          <MenuItem v-for="lang in availableLanguages" :key="lang.code" v-slot="{ active }">
            <button
              @click="changeLanguage(lang.code)"
              :class="[
                active ? 'bg-gray-100 dark:bg-gray-700' : '',
                currentLanguage === lang.code
                  ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400'
                  : 'text-gray-700 dark:text-gray-300',
                'group flex w-full items-center px-4 py-2 text-sm',
              ]"
            >
              <span class="mr-3 text-lg">{{ lang.flag }}</span>
              {{ lang.name }}
            </button>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { LanguageIcon } from '@heroicons/vue/24/outline'

const currentLanguage = ref('fr') // This would come from props or store

const availableLanguages = [
  { code: 'fr', name: 'Français', flag: '🇫🇷' },
  { code: 'en', name: 'English', flag: '🇬🇧' },
]

const changeLanguage = (langCode) => {
  if (langCode !== currentLanguage.value) {
    currentLanguage.value = langCode

    // Update language via Laravel route
    router.post(
      route('language.switch'),
      {
        language: langCode,
      },
      {
        preserveState: true,
        preserveScroll: true,
      }
    )
  }
}
</script>
