<template>
  <AppLayout>
    <Head :title="depense?.id ? 'Modifier la dépense' : 'Nouvelle dépense'" />

    <!-- Page header -->
    <div class="mb-8">
      <div class="flex items-center space-x-4">
        <Link :href="route('depenses.index')" class="btn btn-secondary">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <CurrencyEuroIcon class="inline h-8 w-8 mr-3 text-primary-600" />
            {{ depense?.id ? 'Modifier la dépense' : 'Nouvelle dépense' }}
          </h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{
              depense?.id
                ? 'Mettre à jour les informations de la dépense'
                : 'Ajouter une nouvelle dépense'
            }}
          </p>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div
      class="bg-white dark:bg-gray-800 shadow-card-hover rounded-xl border border-gray-200 dark:border-gray-700"
    >
      <form @submit.prevent="submit" class="p-8 space-y-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
          <!-- Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <CalendarIcon class="inline h-4 w-4 mr-2" />
              Date de la dépense
            </label>
            <input
              v-model="form.date"
              type="date"
              required
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.date }"
            />
            <p v-if="form.errors.date" class="mt-2 text-sm text-red-600">{{ form.errors.date }}</p>
          </div>

          <!-- Type de dépense -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <TagIcon class="inline h-4 w-4 mr-2" />
              Type de dépense
            </label>
            <select
              v-model="form.type_depense"
              required
              class="block w-full select-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.type_depense }"
            >
              <option value="">Sélectionner un type</option>
              <option value="maintenance">Maintenance</option>
              <option value="reparation">Réparation</option>
              <option value="amenagement">Aménagement</option>
              <option value="equipement">Équipement</option>
              <option value="nettoyage">Nettoyage</option>
              <option value="autre">Autre</option>
            </select>
            <p v-if="form.errors.type_depense" class="mt-2 text-sm text-red-600">
              {{ form.errors.type_depense }}
            </p>
          </div>

          <!-- Appartement -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <BuildingOffice2Icon class="inline h-4 w-4 mr-2" />
              Appartement
            </label>
            <select
              v-model="form.appartement_id"
              @change="onAppartementChange"
              class="block select-field w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.appartement_id }"
            >
              <option value="">Dépense générale</option>
              <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                {{ apt.name }}
              </option>
            </select>
            <p v-if="form.errors.appartement_id" class="mt-2 text-sm text-red-600">
              {{ form.errors.appartement_id }}
            </p>
          </div>

          <!-- Montant -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <CurrencyEuroIcon class="inline h-4 w-4 mr-2" />
              Montant (F CFA)
            </label>
            <input
              v-model.number="form.montant"
              type="number"
              step="0.01"
              min="0"
              required
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.montant }"
              placeholder="0.00"
            />
            <p v-if="form.errors.montant" class="mt-2 text-sm text-red-600">
              {{ form.errors.montant }}
            </p>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            <DocumentTextIcon class="inline h-4 w-4 mr-2" />
            Description détaillée
          </label>
          <textarea
            v-model="form.description"
            rows="4"
            required
            class="block w-full rounded-xl input-field border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.description }"
            placeholder="Décrivez la dépense en détail..."
          ></textarea>
          <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Sélection des appartements pour dépense générale -->
        <transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 max-h-0"
          enter-to-class="opacity-100 max-h-96"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 max-h-96"
          leave-to-class="opacity-0 max-h-0"
        >
          <div v-if="showApartmentSelection" class="overflow-hidden">
            <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-gray-700 dark:to-gray-600 rounded-xl p-6 border border-primary-200 dark:border-gray-600">
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  <BuildingOffice2Icon class="inline h-5 w-5 mr-2 text-primary-600" />
                  Sélection des appartements concernés
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                  Choisissez les appartements qui seront impactés par cette dépense générale
                </p>
              </div>

              <!-- Contrôles de sélection -->
              <div class="flex flex-wrap gap-3 mb-6">
                <button
                  type="button"
                  @click="selectAllApartments"
                  class="px-4 py-2 text-sm font-medium text-primary-700 bg-primary-100 dark:bg-primary-900 dark:text-primary-300 rounded-lg hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors duration-200"
                >
                  Tout sélectionner
                </button>
                <button
                  type="button"
                  @click="clearApartmentSelection"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 dark:bg-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors duration-200"
                >
                  Tout désélectionner
                </button>
              </div>

              <!-- Liste des appartements -->
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div
                  v-for="apartment in apartments"
                  :key="apartment.id"
                  class="relative"
                >
                  <label
                    class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200"
                    :class="{
                      'ring-2 ring-primary-500 border-primary-500': selectedApartments.includes(apartment.id)
                    }"
                  >
                    <input
                      type="checkbox"
                      :value="apartment.id"
                      v-model="selectedApartments"
                      class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ apartment.name }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        Appartement {{ apartment.numero || apartment.id }}
                      </div>
                    </div>
                  </label>
                </div>
              </div>
             
              <!-- Calcul et affichage du montant par appartement -->
              <div v-if="selectedApartments.length > 0" class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                      {{ selectedApartments.length }} appartement(s) sélectionné(s)
                    </div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                      Montant par appartement: {{ formatAmount(amountPerApartment) }} F CFA
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                    <div class="text-lg font-bold text-primary-600">
                      {{ formatAmount(form.montant) }} F CFA
                    </div>
                  </div>
                </div>
              </div>

              <!-- Erreur de validation -->
              <p v-if="form.errors.selected_apartments" class="mt-4 text-sm text-red-600">
                {{ form.errors.selected_apartments }}
              </p>
            </div>
          </div>
        </transition>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200 dark:border-gray-700">
          <Link :href="route('depenses.index')" class="btn flex btn-secondary">
            <XMarkIcon class="w-4 h-4 mr-2" />
            Annuler
          </Link>
          <button type="submit" :disabled="form.processing " class="btn flex btn-primary">
            <CheckIcon v-if="!form.processing" class="w-4 h-4 mr-2" />
            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import {
  ArrowLeftIcon,
  CurrencyEuroIcon,
  CalendarIcon,
  TagIcon,
  BuildingOffice2Icon,
  DocumentTextIcon,
  XMarkIcon,
  CheckIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import dayjs from 'dayjs'

const formatDate = (isoString) => isoString ? dayjs(isoString).format('YYYY-MM-DD') : dayjs().format('YYYY-MM-DD')

const props = defineProps({
  depense: Object,
  apartments: Array,
})

// État pour la sélection des appartements
const selectedApartments = ref([])
const showApartmentSelection = ref(true)

const form = useForm({
  date: formatDate(props.depense?.date || ''),
  type_depense: props.depense?.type_depense || '',
  description: props.depense?.description || '',
  appartement_id: props.depense?.appartement_id || '',
  montant: props.depense?.montant || 0,
  selected_apartments: [],
  is_general_expense: true,
})

// Calcul du montant par appartement
const amountPerApartment = computed(() => {
  if (selectedApartments.value.length === 0 || !form.montant) return 0
  return form.montant / selectedApartments.value.length
})


// Formater le montant pour l'affichage
const formatAmount = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(amount || 0)
}

// Gérer le changement d'appartement
const onAppartementChange = () => {
  showApartmentSelection.value = form.appartement_id === ''
  if (!showApartmentSelection.value) {
    selectedApartments.value = []
    form.is_general_expense = false
  } else {
    form.is_general_expense = true
  }
}

// Sélectionner tous les appartements
const selectAllApartments = () => {
  selectedApartments.value = props.apartments.map(apt => apt.id)
}

// Désélectionner tous les appartements
const clearApartmentSelection = () => {
  selectedApartments.value = []
}

// Surveiller les changements de selectedApartments
watch(selectedApartments, (newVal) => {
  form.selected_apartments = newVal
}, { deep: true })

// Initialiser l'état si c'est une modification
if (props.depense?.appartement_id === null) {
  showApartmentSelection.value = true
  form.is_general_expense = true
}

const errors = ref({})
const submit = () => {
  // Validation côté client
  if (showApartmentSelection.value && selectedApartments.value.length === 0) {
    form.setError('selected_apartments', 'Veuillez sélectionner au moins un appartement pour une dépense générale.')
    return
  }

  const url = props.depense?.id
    ? route('depenses.update', props.depense.id)
    : route('depenses.store')

  const method = props.depense?.id ? 'put' : 'post'

  form[method](url, {
    onSuccess: () => {
      // Redirect handled by Laravel
    },
    onError: (err) => {
      console.error('Form submission errors:', err)
      errors.value = err
    },
  })
}
</script>