<template>
    <AppLayout>
      <Head :title="charge?.id ? 'Modifier la charge' : 'Nouvelle charge'" />

      <!-- Page header -->
      <div class="mb-8">
        <div class="flex items-center space-x-4">
          <Link :href="route('charges.index')" class="btn btn-secondary">
            <ArrowLeftIcon class="w-4 h-4" />
          </Link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
              <CreditCardIcon class="inline h-8 w-8 mr-3 text-primary-600" />
              {{ charge?.id ? 'Modifier la charge' : 'Nouvelle charge' }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
              {{
                charge?.id
                  ? 'Mettre à jour les informations de la charge'
                  : 'Ajouter une nouvelle charge mensuelle'
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
            <!-- Service -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                <CogIcon class="inline h-4 w-4 mr-2" />
                Service / Fournisseur
              </label>
              <input
                v-model="form.service"
                type="text"
                required
                class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
                :class="{ 'border-red-500 focus:ring-red-500': form.errors.service }"
                placeholder="Ex: EDF, Gaz, Internet..."
              />
              <p v-if="form.errors.service" class="mt-2 text-sm text-red-600">
                {{ form.errors.service }}
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
                <option value="">Charge générale</option>
                <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                  {{ apt.name }}
                </option>
              </select>
              <p v-if="form.errors.appartement_id" class="mt-2 text-sm text-red-600">
                {{ form.errors.appartement_id }}
              </p>
            </div>

            <!-- Date de paiement -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                <CalendarIcon class="inline h-4 w-4 mr-2" />
                Date de paiement
              </label>
              <input
                v-model="form.date_paiement"
                type="date"
                required
                class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
                :class="{ 'border-red-500 focus:ring-red-500': form.errors.date_paiement }"
              />
              <p v-if="form.errors.date_paiement" class="mt-2 text-sm text-red-600">
                {{ form.errors.date_paiement }}
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

          <!-- Sélection des appartements pour charge générale -->
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
                    Choisissez les appartements qui seront impactés par cette charge générale
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

          <!-- Récurrent -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
            <div class="flex items-center">
              <input
                v-model="form.recurrent"
                type="checkbox"
                :disabled="form.parent_charge_id"
                class="h-4 w-4 input-field text-primary-600 disabled:cursor-not-allowed disabled:text-gray-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
              />
              <label class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                <ArrowPathIcon class="inline h-4 w-4 mr-2" />
                Charge récurrente (mensuelle)
              </label>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
              Cochez cette case si cette charge revient chaque mois
            </p>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <DocumentTextIcon class="inline h-4 w-4 mr-2" />
              Notes (optionnel)
            </label>
            <textarea
              v-model="form.notes"
              rows="3"
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.notes }"
              placeholder="Informations complémentaires..."
            ></textarea>
            <p v-if="form.errors.notes" class="mt-2 text-sm text-red-600">{{ form.errors.notes }}</p>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200 dark:border-gray-700">
            <Link :href="route('charges.index')" class="btn btn-secondary">
              <XMarkIcon class="w-4 h-4 mr-2" />
              Annuler
            </Link>
            <button type="submit" :disabled="form.processing || isSubmitDisabled" class="btn btn-primary">
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
    CreditCardIcon,
    CogIcon,
    BuildingOffice2Icon,
    CalendarIcon,
    CurrencyEuroIcon,
    ArrowPathIcon,
    DocumentTextIcon,
    XMarkIcon,
    CheckIcon,
  } from '@heroicons/vue/24/outline'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import dayjs from 'dayjs'

  const formatDate = (isoString) => isoString ? dayjs(isoString).format('YYYY-MM-DD') : dayjs().format('YYYY-MM-DD')

  const props = defineProps({
    charge: Object,
    apartments: Array,
  })

import { usePage } from '@inertiajs/vue3'

const page = usePage()

// Surveille les changements du message de succès
watch(
    () => page.props.flash?.success,
    (newVal) => {
        if (newVal) {
            // Exemple : afficher avec un toast, une alerte, etc.
            console.log('✅ Message de succès:', newVal)
            alert(newVal)
        }
    },
    { immediate: true }
)

  // État pour la sélection des appartements
  const selectedApartments = ref([])
  const showApartmentSelection = ref(true)

  const form = useForm({
    service: props.charge?.service || '',
    appartement_id: props.charge?.appartement_id || '',
    date_paiement: formatDate(props.charge?.date_paiement || ''),
    montant: props.charge?.montant || 0,
    recurrent: props.charge?.recurrent || true,
    notes: props.charge?.notes || '',
    parent_charge_id: props.charge?.parent_charge_id || null,
    selected_apartments: [],
    is_general_charge: true,
  })

  // Calcul du montant par appartement
  const amountPerApartment = computed(() => {
    if (selectedApartments.value.length === 0 || !form.montant) return 0
    return form.montant / selectedApartments.value.length
  })

  // Vérifier si le bouton de soumission doit être désactivé
  const isSubmitDisabled = computed(() => {
    return showApartmentSelection.value && selectedApartments.value.length === 0
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
      form.is_general_charge = false
    } else {
      form.is_general_charge = true
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
  if (props.charge?.appartement_id === null) {
    showApartmentSelection.value = true
    form.is_general_charge = true
  }

  const submit = () => {
    // Validation côté client
    if (showApartmentSelection.value && selectedApartments.value.length === 0) {
      form.setError('selected_apartments', 'Veuillez sélectionner au moins un appartement pour une charge générale.')
      return
    }

    const url = props.charge?.id ? route('charges.update', props.charge.id) : route('charges.store')
    const method = props.charge?.id ? 'put' : 'post'

    console.log("Submitting form to ", url, " with data: ", form)

    form[method](url, {
      onSuccess: () => {
        // Redirect handled by Laravel
      },
      onError: (err) => {
        console.error('Form submission errors:', err)
      },
    })
  }
  </script>
