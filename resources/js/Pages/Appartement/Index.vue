<template>
    <AppLayout>
      <Head title="Appartements" />

      <!-- Toggle Status Modal -->
      <ToggleStatusModal
        :show="modal.show"
        :appartement="modal.appartement"
        @confirm="confirmToggle"
        @cancel="modal.show = false"
      />

      <!-- Page header -->
      <div class="mb-8 sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <BuildingOffice2Icon class="inline h-8 w-8 mr-3 text-primary-600" />
            Appartements
          </h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Gérez tous vos appartements et leurs informations
          </p>
        </div>
        <div class="mt-4 sm:mt-0">
          <Link
            :href="route('appartements.create')"
            class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Nouvel appartement
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
              placeholder="Nom, adresse, description..."
              class="block input-field w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <CheckCircleIcon class="inline h-4 w-4 mr-1" />
              Statut
            </label>
            <select
              v-model="filters.status"
              class="block select-field w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            >
              <option value="">Tous les statuts</option>
              <option value="active">Actif</option>
              <option value="inactive">Inactif</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <UsersIcon class="inline h-4 w-4 mr-1" />
              Capacité min.
            </label>
            <input
              v-model.number="filters.capacity"
              type="number"
              min="1"
              placeholder="Ex: 2"
              class="block input-field w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            />
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

      <!-- Appartements table -->
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700"
      >
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Appartement
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Adresse
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Capacité
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Surface
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Statut
                </th>
                <th class="relative px-6 py-4">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="appartement in appartements.data"
                :key="appartement.id"
                :class="[
                  'transition-all duration-200',
                  appartement.active
                    ? 'hover:bg-gray-50 dark:hover:bg-gray-700/50'
                    : 'bg-gray-50/70 dark:bg-gray-900/30 opacity-75 hover:opacity-90'
                ]"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <!-- Active indicator dot -->
                    <span
                      :class="[
                        'inline-block flex-shrink-0 w-2 h-2 rounded-full transition-all duration-300',
                        appartement.active
                          ? 'bg-emerald-400 shadow-sm shadow-emerald-300 dark:shadow-emerald-700 animate-pulse'
                          : 'bg-gray-300 dark:bg-gray-600'
                      ]"
                    />
                    <div>
                      <div
                        :class="[
                          'text-sm font-medium transition-colors',
                          appartement.active
                            ? 'text-gray-900 dark:text-white'
                            : 'text-gray-400 dark:text-gray-500 line-through decoration-1'
                        ]"
                      >
                        {{ appartement.name }}
                      </div>
                      <div
                        v-if="appartement.description"
                        class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"
                      >
                        {{ appartement.description }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <MapPinIcon class="inline h-4 w-4 mr-2 text-gray-400" />
                  {{ appartement.address }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <UsersIcon class="inline h-4 w-4 mr-2 text-gray-400" />
                  {{ appartement.capacity }} {{ appartement.capacity > 1 ? 'personnes' : 'personne' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <span v-if="appartement.surface">{{ appartement.surface }} m²</span>
                  <span v-else class="text-gray-400">Non renseignée</span>
                </td>

                <!-- Status cell with toggle -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <ToggleSwitch
                      :model-value="appartement.active"
                      @click="openModal(appartement)"
                    />
                    <span
                      :class="[
                        'text-xs font-semibold transition-colors duration-200',
                        appartement.active
                          ? 'text-emerald-600 dark:text-emerald-400'
                          : 'text-gray-400 dark:text-gray-500'
                      ]"
                    >
                      {{ appartement.active ? 'Actif' : 'Inactif' }}
                    </span>
                  </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <Link
                      :href="route('appartements.edit', appartement.id)"
                      class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 transition-colors p-1 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20"
                    >
                      <PencilIcon class="h-4 w-4" />
                    </Link>
                    <button
                      @click="deleteAppartement(appartement)"
                      class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
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
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
          <Pagination :links="appartements" />
        </div>
      </div>
    </AppLayout>
  </template>

  <script setup lang="ts">
  import { reactive, watch } from 'vue'
  import { Head, Link, router } from '@inertiajs/vue3'
  import {
    BuildingOffice2Icon,
    PlusIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    UsersIcon,
    ArrowPathIcon,
    MapPinIcon,
    PencilIcon,
    TrashIcon,
  } from '@heroicons/vue/24/outline'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import Pagination from '@/Components/Pagination.vue'
  import ToggleSwitch from '@/Components/ToggleSwitch.vue'
  import ToggleStatusModal from '@/Components/ToggleStatusModal.vue'

  const props = defineProps({
    appartements: Object,
    filters: Object,
  })

  const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    capacity: props.filters.capacity || '',
  })

  const modal = reactive({
    show: false,
    appartement: null as any,
  })

  const openModal = (appartement: any) => {
    modal.appartement = appartement
    modal.show = true
  }

  const confirmToggle = () => {
    router.patch(route('appartements.toggle-status', modal.appartement.id), {}, {
      onSuccess: () => {
        modal.show = false
        modal.appartement = null
      },
    })
  }

  watch(
    filters,
    (newFilters) => {
      router.get(route('appartements.index'), newFilters, {
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

  const deleteAppartement = (appartement: any) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet appartement ?')) {
      router.delete(route('appartements.destroy', appartement.id))
    }
  }
  </script>
