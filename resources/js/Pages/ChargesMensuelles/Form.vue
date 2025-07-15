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
            {{ charge?.id ? 'Mettre à jour les informations de la charge' : 'Ajouter une nouvelle charge mensuelle' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow-card-hover rounded-xl border border-gray-200 dark:border-gray-700">
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
            <p v-if="form.errors.service" class="mt-2 text-sm text-red-600">{{ form.errors.service }}</p>
          </div>

          <!-- Appartement -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <BuildingOffice2Icon class="inline h-4 w-4 mr-2" />
              Appartement
            </label>
            <select
              v-model="form.appartement_id"
              class="block select-field w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.appartement_id }"
            >
              <option value="">Charge générale</option>
              <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                {{ apt.name }}
              </option>
            </select>
            <p v-if="form.errors.appartement_id" class="mt-2 text-sm text-red-600">{{ form.errors.appartement_id }}</p>
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
            <p v-if="form.errors.date_paiement" class="mt-2 text-sm text-red-600">{{ form.errors.date_paiement }}</p>
          </div>

          <!-- Montant -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <CurrencyEuroIcon class="inline h-4 w-4 mr-2" />
              Montant (€)
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
            <p v-if="form.errors.montant" class="mt-2 text-sm text-red-600">{{ form.errors.montant }}</p>
          </div>
        </div>

        <!-- Récurrent -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
          <div class="flex items-center">
            <input
              v-model="form.recurrent"
              type="checkbox"
              class="h-4 w-4 input-field text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
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
            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.notes }"
            placeholder="Informations complémentaires..."
          ></textarea>
          <p v-if="form.errors.notes" class="mt-2 text-sm text-red-600">{{ form.errors.notes }}</p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200 dark:border-gray-700">
          <Link
            :href="route('charges.index')"
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
import { useForm } from '@inertiajs/vue3'
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
  CheckIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  charge: Object,
  apartments: Array,
})

const form = useForm({
  service: props.charge?.service || '',
  appartement_id: props.charge?.appartement_id || '',
  date_paiement: props.charge?.date_paiement || new Date().toISOString().split('T')[0],
  montant: props.charge?.montant || 0,
  recurrent: props.charge?.recurrent || true,
  notes: props.charge?.notes || '',
})

const submit = () => {
  const url = props.charge?.id
    ? route('charges.update', props.charge.id)
    : route('charges.store')

  const method = props.charge?.id ? 'put' : 'post'

  form[method](url)
}
</script>
