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

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200 dark:border-gray-700">
          <Link :href="route('depenses.index')" class="btn flex btn-secondary">
            <XMarkIcon class="w-4 h-4 mr-2" />
            Annuler
          </Link>
          <button type="submit" :disabled="form.processing" class="btn flex  btn-primary">
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
import { ref } from 'vue'
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

const formatDate = (isoString) => isoString? dayjs(isoString).format('YYYY-MM-DD'): dayjs().format('YYYY-MM-DD')

const props = defineProps({
  depense: Object,
  apartments: Array,
})

const form = useForm({
  date: formatDate(props.depense?.date || ''),
  type_depense: props.depense?.type_depense || '',
  description: props.depense?.description || '',
  appartement_id: props.depense?.appartement_id || '',
  montant: props.depense?.montant || 0,
})

const errors = ref({})
const submit = () => {
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
