<template>
  <AppLayout>
    <Head title="Dépenses" />

    <!-- Page header -->
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          <CurrencyEuroIcon class="inline h-8 w-8 mr-3 text-primary-600" />
          Dépenses
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Gérez toutes vos dépenses par appartement
        </p>
      </div>
      <div class="mt-4 sm:mt-0">
        <Link
          :href="route('depenses.create')"
          class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Nouvelle dépense
        </Link>
      </div>
    </div>

    <!-- Filters -->
    <div
      class="mb-6 bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
    >
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            <MagnifyingGlassIcon class="inline h-4 w-4 mr-1" />
            Rechercher
          </label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Description, type..."
            class="block input-field w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            <TagIcon class="inline h-4 w-4 mr-1" />
            Type
          </label>
          <select
            v-model="filters.type"
            class="block select-field w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
          >
            <option value="">Tous les types</option>
               <option value="maintenance">Maintenance</option>
              <option value="reparation">Réparation</option>
              <option value="amenagement">Aménagement</option>
              <option value="equipement">Équipement</option>
              <option value="nettoyage">Nettoyage</option>
              <option value="autre">Autre</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            <BuildingOffice2Icon class="inline h-4 w-4 mr-1" />
            Appartement
          </label>
          <select
            v-model="filters.apartment"
            class="block select-field w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
          >
            <option value="">Tous les appartements</option>
            <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
              {{ apt.name }}
            </option>
          </select>
        </div>

        <div class="flex items-end">
          <button
            @click="resetFilters"
            class="w-full rounded-lg bg-gray-100 dark:bg-gray-600 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-200 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200"
          >
            <ArrowPathIcon class="inline h-4 w-4 mr-1" />
            Réinitialiser
          </button>
        </div>
      </div>
    </div>

    <!-- Dépenses table -->
    <div
      class="bg-white dark:bg-gray-800 shadow-card rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700"
    >
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Date
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Type & Description
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Appartement
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Montant
              </th>
              <th class="relative px-6 py-4">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="depense in depenses.data"
              :key="depense.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <CalendarIcon class="inline h-4 w-4 mr-2 text-gray-400" />
                {{ formatDate(depense.date) }}
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ depense.type_depense }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                  {{ depense.description }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span
                  v-if="depense.appartement"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-300"
                >
                  {{ depense.appartement.name }}
                </span>
                <span v-else class="text-gray-400">Général</span>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white"
              >
                {{ formatCurrency(depense.montant) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <Link
                    :href="route('depenses.edit', depense.id)"
                    class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                  >
                    <PencilIcon class="h-4 w-4" />
                  </Link>
                  <button
                    @click="deleteDepense(depense)"
                    class="text-error-600 hover:text-error-900 dark:text-error-400 dark:hover:text-error-300 transition-colors"
                  >
                    <TrashIcon class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6"
      >
        <Pagination :links="depenses" />
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
  CurrencyEuroIcon,
  PlusIcon,
  MagnifyingGlassIcon,
  TagIcon,
  BuildingOffice2Icon,
  ArrowPathIcon,
  CalendarIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  depenses: Object,
  apartments: Array,
  filters: Object,
})

const filters = reactive({
  search: props.filters.search || '',
  type: props.filters.type || '',
  apartment: props.filters.apartment || '',
})

// Watch for filter changes and update URL
watch(
  filters,
  (newFilters) => {
    router.get(route('depenses.index'), newFilters, {
      preserveState: true,
      replace: true,
    })
  },
  { debounce: 300 }
)

const resetFilters = () => {
  Object.keys(filters).forEach((key) => {
    filters[key] = ''
  })
}

const deleteDepense = (depense) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) {
    router.delete(route('depenses.destroy', depense.id))
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
  }).format(amount)
}
</script>
