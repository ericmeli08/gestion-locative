<template>
  <AppLayout>
    <Head :title="appartement?.id ? 'Modifier l\'appartement' : 'Nouvel appartement'" />

    <!-- Page header -->
    <div class="mb-8">
      <div class="flex items-center space-x-4">
        <Link :href="route('appartements.index')" class="btn btn-secondary">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <BuildingOffice2Icon class="inline h-8 w-8 mr-3 text-primary-600" />
            {{ appartement?.id ? 'Modifier l\'appartement' : 'Nouvel appartement' }}
          </h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{
              appartement?.id
                ? 'Mettre à jour les informations de l\'appartement'
                : 'Ajouter un nouvel appartement'
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
          <!-- Nom -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <BuildingOffice2Icon class="inline h-4 w-4 mr-2" />
              Nom de l'appartement *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
              placeholder="Ex: Appartement T2 Centre-ville"
            />
            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Adresse -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <MapPinIcon class="inline h-4 w-4 mr-2" />
              Adresse *
            </label>
            <input
              v-model="form.address"
              type="text"
              required
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.address }"
              placeholder="Ex: 123 Rue de la Paix, 75001 Paris"
            />
            <p v-if="form.errors.address" class="mt-2 text-sm text-red-600">{{ form.errors.address }}</p>
          </div>

          <!-- Capacité -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <UsersIcon class="inline h-4 w-4 mr-2" />
              Capacité (nombre de personnes) *
            </label>
            <input
              v-model.number="form.capacity"
              type="number"
              min="1"
              max="50"
              required
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.capacity }"
              placeholder="Ex: 4"
            />
            <p v-if="form.errors.capacity" class="mt-2 text-sm text-red-600">{{ form.errors.capacity }}</p>
          </div>

          <!-- Surface -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <Square3Stack3DIcon class="inline h-4 w-4 mr-2" />
              Surface (m²)
            </label>
            <input
              v-model.number="form.surface"
              type="number"
              step="0.01"
              min="0"
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.surface }"
              placeholder="Ex: 65.50"
            />
            <p v-if="form.errors.surface" class="mt-2 text-sm text-red-600">{{ form.errors.surface }}</p>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            <DocumentTextIcon class="inline h-4 w-4 mr-2" />
            Description
          </label>
          <textarea
            v-model="form.description"
            rows="4"
            class="block w-full rounded-xl input-field border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.description }"
            placeholder="Décrivez l'appartement, ses caractéristiques, équipements..."
          ></textarea>
          <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Statut actif -->
        <div class="flex items-center">
          <input
            v-model="form.active"
            type="checkbox"
            id="active"
            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
          />
          <label for="active" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
            <CheckCircleIcon class="inline h-4 w-4 mr-2" />
            Appartement actif
          </label>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200 dark:border-gray-700">
          <Link :href="route('appartements.index')" class="btn flex btn-secondary">
            <XMarkIcon class="w-4 h-4 mr-2" />
            Annuler
          </Link>
          <button type="submit" :disabled="form.processing" class="btn flex btn-primary">
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
  BuildingOffice2Icon,
  MapPinIcon,
  UsersIcon,
  Square3Stack3DIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  XMarkIcon,
  CheckIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  appartement: Object,
})

const form = useForm({
  name: props.appartement?.name || '',
  description: props.appartement?.description || '',
  address: props.appartement?.address || '',
  capacity: props.appartement?.capacity || 1,
  surface: props.appartement?.surface || '',
  active: props.appartement?.active !== undefined ? props.appartement.active : true,
})

const errors = ref({})

const submit = () => {
  const url = props.appartement?.id
    ? route('appartements.update', props.appartement.id)
    : route('appartements.store')

  const method = props.appartement?.id ? 'put' : 'post'

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
