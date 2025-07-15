<template>
  <AppLayout>
    <Head :title="stock?.id ? 'Modifier le stock' : 'Nouveau stock'" />

    <!-- Page header -->
    <div class="mb-8">
      <div class="flex items-center space-x-4">
        <Link :href="route('stocks.index')" class="btn btn-secondary">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <CubeIcon class="inline h-8 w-8 mr-3 text-primary-600" />
            {{ stock?.id ? 'Modifier le stock' : 'Nouveau stock' }}
          </h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ stock?.id ? 'Mettre à jour les informations du stock' : 'Ajouter un nouvel élément en stock' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow-card-hover rounded-xl border border-gray-200 dark:border-gray-700">
      <form @submit.prevent="submit" class="p-8 space-y-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
          <!-- Élément -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <CubeIcon class="inline h-4 w-4 mr-2" />
              Nom de l'élément
            </label>
            <input
              v-model="form.element"
              type="text"
              required
              class="block input-field w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.element }"
              placeholder="Ex: Papier toilette, Serviettes..."
            />
            <p v-if="form.errors.element" class="mt-2 text-sm text-red-600">{{ form.errors.element }}</p>
          </div>

          <!-- Appartement -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <BuildingOffice2Icon class="inline h-4 w-4 mr-2" />
              Appartement
            </label>
            <select
              v-model="form.appartement_id"
              class="block w-full select-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.appartement_id }"
            >
              <option value="">Stock général</option>
              <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                {{ apt.name }}
              </option>
            </select>
            <p v-if="form.errors.appartement_id" class="mt-2 text-sm text-red-600">{{ form.errors.appartement_id }}</p>
          </div>

          <!-- Quantité mensuelle -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <PlusIcon class="inline h-4 w-4 mr-2" />
              Quantité mensuelle
            </label>
            <input
              v-model.number="form.quantite_mois"
              type="number"
              min="0"
              required
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.quantite_mois }"
              placeholder="0"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Quantité prévue pour le mois</p>
            <p v-if="form.errors.quantite_mois" class="mt-2 text-sm text-red-600">{{ form.errors.quantite_mois }}</p>
          </div>

          <!-- Quantité utilisée -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <MinusIcon class="inline h-4 w-4 mr-2" />
              Quantité utilisée
            </label>
            <input
              v-model.number="form.utilise"
              type="number"
              min="0"
              :max="form.quantite_mois"
              required
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.utilise }"
              placeholder="0"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Quantité déjà consommée</p>
            <p v-if="form.errors.utilise" class="mt-2 text-sm text-red-600">{{ form.errors.utilise }}</p>
          </div>

          <!-- Seuil d'alerte -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <ExclamationTriangleIcon class="inline h-4 w-4 mr-2" />
              Seuil d'alerte
            </label>
            <input
              v-model.number="form.seuil"
              type="number"
              min="0"
              required
              class="block w-full rounded-xl input-field border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.seuil }"
              placeholder="0"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alerte quand le stock restant atteint ce seuil</p>
            <p v-if="form.errors.seuil" class="mt-2 text-sm text-red-600">{{ form.errors.seuil }}</p>
          </div>

          <!-- Calculs automatiques -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              <CalculatorIcon class="inline h-5 w-5 mr-2" />
              Calculs automatiques
            </h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Stock restant:</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                  {{ stockRestant }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Statut:</span>
                <span v-if="needsRestock" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-900/50 dark:text-warning-300">
                  <ExclamationTriangleIcon class="h-3 w-3 mr-1" />
                  À racheter
                </span>
                <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900/50 dark:text-success-300">
                  <CheckCircleIcon class="h-3 w-3 mr-1" />
                  Stock OK
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200 dark:border-gray-700">
          <Link
            :href="route('stocks.index')"
            class="btn btn-secondary"
          >
            <XMarkIcon class="w-4 h-4 mr-2" />
            Annuler
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="btn btn-primary"
          >
            <CheckIcon v-if="!form.processing" class="w-4 h-4 mr-2" />
            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  CubeIcon,
  BuildingOffice2Icon,
  PlusIcon,
  MinusIcon,
  ExclamationTriangleIcon,
  CalculatorIcon,
  CheckCircleIcon,
  XMarkIcon,
  CheckIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  stock: Object,
  apartments: Array,
})

const form = useForm({
  element: props.stock?.element || '',
  appartement_id: props.stock?.appartement_id || '',
  quantite_mois: props.stock?.quantite_mois || 0,
  utilise: props.stock?.utilise || 0,
  seuil: props.stock?.seuil || 5,
})

const stockRestant = computed(() => {
  return Math.max(0, form.quantite_mois - form.utilise)
})

const needsRestock = computed(() => {
  return stockRestant.value <= form.seuil
})

const submit = () => {
  const url = props.stock?.id
    ? route('stocks.update', props.stock.id)
    : route('stocks.store')

  const method = props.stock?.id ? 'put' : 'post'

  form[method](url)
}
</script>
