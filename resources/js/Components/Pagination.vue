<template>
  <nav
    class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6"
  >
    <div class="flex flex-1 justify-between sm:hidden">
      <Link
        v-if="links.prev_page_url"
        :href="links.prev_page_url"
        class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
      >
        Précédent
      </Link>
      <Link
        v-if="links.next_page_url"
        :href="links.next_page_url"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
      >
        Suivant
      </Link>
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Affichage de
          <span class="font-medium">{{ links.from || 0 }}</span>
          à
          <span class="font-medium">{{ links.to || 0 }}</span>
          sur
          <span class="font-medium">{{ links.total || 0 }}</span>
          résultats
        </p>
      </div>

      <div v-if="links.last_page > 1">
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <!-- Previous -->
          <Link
            v-if="links.prev_page_url"
            :href="links.prev_page_url"
            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0"
          >
            <ChevronLeftIcon class="h-5 w-5" />
          </Link>

          <!-- Page numbers -->
          <template v-for="(link, index) in paginationLinks" :key="index">
            <Link
              v-if="link.url"
              :href="link.url"
              :class="[
                link.active
                  ? 'relative z-10 inline-flex items-center bg-primary-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600'
                  : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0',
              ]"
              v-html="link.label"
            />
            <span
              v-else
              class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-600"
              v-html="link.label"
            />
          </template>

          <!-- Next -->
          <Link
            v-if="links.next_page_url"
            :href="links.next_page_url"
            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0"
          >
            <ChevronRightIcon class="h-5 w-5" />
          </Link>
        </nav>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  links: {
    type: Object,
    required: true
  }
})

const paginationLinks = computed(() => {
  if (!props.links.links) return []
  
  // Filtrer les liens "Previous" et "Next" pour ne garder que les numéros de page
  return props.links.links.filter((link, index) => {
    return index !== 0 && index !== props.links.links.length - 1
  })
})
</script>