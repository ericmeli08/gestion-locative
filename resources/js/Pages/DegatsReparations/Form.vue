<template>
  <AppLayout>
    <Head :title="degat?.id ? 'Modifier le dégât' : 'Nouveau dégât'" />

    <!-- Page header -->
    <div class="mb-8">
      <div class="flex items-center space-x-4">
        <Link :href="route('degats.index')" class="btn btn-secondary">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <WrenchScrewdriverIcon class="inline h-8 w-8 mr-3 text-primary-600" />
            {{ degat?.id ? 'Modifier le dégât' : 'Nouveau dégât' }}
          </h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{
              degat?.id
                ? 'Mettre à jour les informations du dégât'
                : 'Signaler un nouveau dégât ou réparation'
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
              Date du signalement
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

          <!-- Appartement -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <BuildingOffice2Icon class="inline h-4 w-4 mr-2" />
              Appartement
            </label>
            <select
              v-model="form.appartement_id"
              required
              class="block select-field w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.appartement_id }"
            >
              <option value="">Sélectionner un appartement</option>
              <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                {{ apt.name }}
              </option>
            </select>
            <p v-if="form.errors.appartement_id" class="mt-2 text-sm text-red-600">
              {{ form.errors.appartement_id }}
            </p>
          </div>

          <!-- Type de dégât -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <ExclamationTriangleIcon class="inline h-4 w-4 mr-2" />
              Type de dégât
            </label>
            <select
              v-model="form.type_degat"
              required
              class="block select-field w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.type_degat }"
            >
              <option value="">Sélectionner un type</option>
              <option value="plomberie">Plomberie</option>
              <option value="electricite">Électricité</option>
              <option value="chauffage">Chauffage</option>
              <option value="serrurerie">Serrurerie</option>
              <option value="peinture">Peinture</option>
              <option value="carrelage">Carrelage</option>
              <option value="electromenager">Électroménager</option>
              <option value="autre">Autre</option>
            </select>
            <p v-if="form.errors.type_degat" class="mt-2 text-sm text-red-600">
              {{ form.errors.type_degat }}
            </p>
          </div>

          <!-- Statut -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <ClipboardDocumentListIcon class="inline h-4 w-4 mr-2" />
              Statut
            </label>
            <select
              v-model="form.statut"
              required
              class="block select-field w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.statut }"
            >
              <option value="signale">Signalé</option>
              <option value="en_cours">En cours</option>
              <option value="termine">Terminé</option>
            </select>
            <p v-if="form.errors.statut" class="mt-2 text-sm text-red-600">
              {{ form.errors.statut }}
            </p>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            <DocumentTextIcon class="inline h-4 w-4 mr-2" />
            Description du problème
          </label>
          <textarea
            v-model="form.description"
            rows="4"
            required
            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.description }"
            placeholder="Décrivez le problème en détail..."
          ></textarea>
          <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Solution (si statut en cours ou terminé) -->
        <div v-if="form.statut !== 'signale'">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            <WrenchScrewdriverIcon class="inline h-4 w-4 mr-2" />
            Solution apportée
          </label>
          <textarea
            v-model="form.solution"
            rows="3"
            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.solution }"
            placeholder="Décrivez la solution mise en place..."
          ></textarea>
          <p v-if="form.errors.solution" class="mt-2 text-sm text-red-600">
            {{ form.errors.solution }}
          </p>
        </div>

        <!-- Informations de réparation (si statut terminé) -->
        <div v-if="form.statut === 'termine'" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <!-- Date de réparation -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <CalendarIcon class="inline h-4 w-4 mr-2" />
              Date de réparation
            </label>
            <input
              v-model="form.date_reparation"
              type="date"
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.date_reparation }"
            />
            <p v-if="form.errors.date_reparation" class="mt-2 text-sm text-red-600">
              {{ form.errors.date_reparation }}
            </p>
          </div>

          <!-- Réparateur -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <UserIcon class="inline h-4 w-4 mr-2" />
              Réparateur
            </label>
            <input
              v-model="form.reparateur"
              type="text"
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.reparateur }"
              placeholder="Nom du réparateur"
            />
            <p v-if="form.errors.reparateur" class="mt-2 text-sm text-red-600">
              {{ form.errors.reparateur }}
            </p>
          </div>

          <!-- Coût -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              <CurrencyEuroIcon class="inline h-4 w-4 mr-2" />
              Coût (€)
            </label>
            <input
              v-model.number="form.cout"
              type="number"
              step="0.01"
              min="0"
              class="block w-full input-field rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500': form.errors.cout }"
              placeholder="0.00"
            />
            <p v-if="form.errors.cout" class="mt-2 text-sm text-red-600">{{ form.errors.cout }}</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200 dark:border-gray-700">
          <Link :href="route('degats.index')" class="btn btn-secondary">
            <XMarkIcon class="w-4 h-4 mr-2" />
            Annuler
          </Link>
          <button type="submit" :disabled="form.processing" class="btn btn-primary">
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

<script setup>
import { useForm } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  WrenchScrewdriverIcon,
  CalendarIcon,
  BuildingOffice2Icon,
  ExclamationTriangleIcon,
  ClipboardDocumentListIcon,
  DocumentTextIcon,
  UserIcon,
  CurrencyEuroIcon,
  XMarkIcon,
  CheckIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  degat: Object,
  apartments: Array,
})

const form = useForm({
  date: props.degat?.date || new Date().toISOString().split('T')[0],
  appartement_id: props.degat?.appartement_id || '',
  type_degat: props.degat?.type_degat || '',
  description: props.degat?.description || '',
  solution: props.degat?.solution || '',
  cout: props.degat?.cout || 0,
  statut: props.degat?.statut || 'signale',
  date_reparation: props.degat?.date_reparation || '',
  reparateur: props.degat?.reparateur || '',
})

const submit = () => {
  const url = props.degat?.id ? route('degats.update', props.degat.id) : route('degats.store')

  const method = props.degat?.id ? 'put' : 'post'

  form[method](url)
}
</script>
