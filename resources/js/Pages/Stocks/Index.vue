<template>
  <AppLayout>
    <Head title="Gestion des Stocks" />

    <!-- Page header -->
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          <CubeIcon class="inline h-8 w-8 mr-3 text-primary-600" />
          Gestion des Stocks
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Suivez vos stocks avec alertes automatiques
        </p>
      </div>
      <div class="mt-4 sm:mt-0">
        <Link
          :href="route('stocks.create')"
          class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Nouveau stock
        </Link>
      </div>
    </div>

    <!-- Alertes de stock -->
    <div
      v-if="lowStockItems.length > 0"
      class="mb-6 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-xl p-6"
    >
      <div class="flex items-center mb-4">
        <ExclamationTriangleIcon class="h-6 w-6 text-warning-600 dark:text-warning-400 mr-2" />
        <h3 class="text-lg font-medium text-warning-800 dark:text-warning-200">
          Alertes de stock ({{ lowStockItems.length }})
        </h3>
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="item in lowStockItems"
          :key="item.id"
          class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-warning-200 dark:border-warning-700"
        >
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-medium text-gray-900 dark:text-white">{{ item.element }}</h4>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Reste: {{ item.reste }} / {{ item.quantite_mois }}
              </p>
            </div>
            <Link :href="route('stocks.edit', item.id)" class="btn btn-warning btn-sm">
              Réapprovisionner
            </Link>
          </div>
        </div>
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
            placeholder="Nom de l'élément..."
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
            <ExclamationTriangleIcon class="inline h-4 w-4 mr-1" />
            Alerte
          </label>
          <select
            v-model="filters.alert"
            class="block w-full select-field rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
          >
            <option value="">Tous les stocks</option>
            <option value="low">Stock faible uniquement</option>
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

    <!-- Stocks table -->
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
                Élément
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Appartement
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Stock initial
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Utilisé
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Reste
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Statut
              </th>
              <th class="relative px-6 py-4">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="stock in stocks.data"
              :key="stock.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
              :class="{ 'bg-warning-50 dark:bg-warning-900/20': stock.a_racheter }"
            >
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <CubeIcon class="h-5 w-5 text-gray-400 mr-3" />
                  <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ stock.element }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span
                  v-if="stock.appartement"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-300"
                >
                  {{ stock.appartement.name }}
                </span>
                <span v-else class="text-gray-400">Général</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ stock.quantite_mois }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ stock.utilise }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                <span
                  :class="
                    stock.a_racheter
                      ? 'text-warning-600 dark:text-warning-400'
                      : 'text-gray-900 dark:text-white'
                  "
                >
                  {{ stock.reste }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  v-if="stock.a_racheter"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-900/50 dark:text-warning-300"
                >
                  <ExclamationTriangleIcon class="h-3 w-3 mr-1" />
                  À racheter
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900/50 dark:text-success-300"
                >
                  <CheckCircleIcon class="h-3 w-3 mr-1" />
                  OK
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <Link
                    :href="route('stocks.edit', stock.id)"
                    class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                  >
                    <PencilIcon class="h-4 w-4" />
                  </Link>
                  <button
                    @click="deleteStock(stock)"
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
        <Pagination :links="stocks" />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
  CubeIcon,
  PlusIcon,
  MagnifyingGlassIcon,
  BuildingOffice2Icon,
  ExclamationTriangleIcon,
  ArrowPathIcon,
  PencilIcon,
  TrashIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  stocks: Object,
  apartments: Array,
  filters: Object,
})

const filters = reactive({
  search: props.filters.search || '',
  apartment: props.filters.apartment || '',
  alert: props.filters.alert || '',
})

const lowStockItems = computed(() => {
  return props.stocks.data.filter((stock) => stock.a_racheter)
})

// Watch for filter changes and update URL
watch(
  filters,
  (newFilters) => {
    router.get(route('stocks.index'), newFilters, {
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

const deleteStock = (stock) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')) {
    router.delete(route('stocks.destroy', stock.id))
  }
}
</script>
