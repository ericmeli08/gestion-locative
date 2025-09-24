<template>
  <AppLayout>
    <Head title="Charges Mensuelles" />

    <!-- Page header -->
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          <CreditCardIcon class="inline h-8 w-8 mr-3 text-primary-600" />
          Charges Mensuelles
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Gérez vos charges fixes et récurrentes
        </p>
      </div>
      <div class="mt-4 sm:mt-0">
        <Link
          :href="route('charges.create')"
          class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Nouvelle charge
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
            placeholder="Service, notes..."
            class="block w-full input-field rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
          />
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

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            <ArrowPathIcon class="inline h-4 w-4 mr-1" />
            Type
          </label>
          <select
            v-model="filters.recurrent"
            class="block w-full select-field rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
          >
            <option value="">Tous les types</option>
            <option value="true">Récurrentes</option>
            <option value="false">Ponctuelles</option>
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

    <!-- Charges table -->
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
                Service
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Appartement
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Date de paiement
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Montant
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Type
              </th>
              <th class="relative px-6 py-4">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="charge in charges.data"
              :key="charge.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
            >
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ charge.service }}
                </div>
                <div
                  v-if="charge.notes"
                  class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"
                >
                  {{ charge.notes }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span
                  v-if="charge.appartement"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-300"
                >
                  {{ charge.appartement.name }}
                </span>
                <span v-else class="text-gray-400">Général</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <CalendarIcon class="inline h-4 w-4 mr-2 text-gray-400" />
                {{ formatDate(charge.date_paiement) }}
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white"
              >
                {{ formatCurrency(charge.montant) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  v-if="charge.recurrent"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900/50 dark:text-success-300"
                >
                  <ArrowPathIcon class="h-3 w-3 mr-1" />
                  Récurrente
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                >
                  Ponctuelle
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <Link
                    :href="route('charges.edit', charge.id)"
                    class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                  >
                    <PencilIcon class="h-4 w-4" />
                  </Link>
                  <button
                    @click="deleteCharge(charge)"
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
        <Pagination :links="charges" />
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
  CreditCardIcon,
  PlusIcon,
  MagnifyingGlassIcon,
  BuildingOffice2Icon,
  ArrowPathIcon,
  CalendarIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  charges: Object,
  apartments: Array,
  filters: Object,
})

const filters = reactive({
  search: props.filters.search || '',
  apartment: props.filters.apartment || '',
  recurrent: props.filters.recurrent || '',
})

// Watch for filter changes and update URL
watch(
  filters,
  (newFilters) => {
    router.get(route('charges.index'), newFilters, {
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

const deleteCharge = (charge) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette charge ?')) {
    router.delete(route('charges.destroy', charge.id))
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
