<template>
  <AppLayout>
    <Head title="Paramètres" />

    <!-- Toast Notification -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 transform translate-x-full"
        enter-to-class="opacity-100 transform translate-x-0"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="opacity-100 transform translate-x-0"
        leave-to-class="opacity-0 transform translate-x-full"
      >
        <div
          v-if="showToast"
          class="fixed top-4 right-4 z-50 max-w-sm"
        >
          <div
            :class="[
              'rounded-lg shadow-lg border p-4 min-w-[320px]',
              toastType === 'success'
                ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300'
                : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300'
            ]"
          >
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <CheckIcon
                  v-if="toastType === 'success'"
                  class="h-5 w-5 text-green-600 dark:text-green-400"
                />
                <ExclamationTriangleIcon
                  v-else
                  class="h-5 w-5 text-red-600 dark:text-red-400"
                />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-sm font-medium">
                  {{ toastType === 'success' ? 'Succès' : 'Erreur' }}
                </p>
                <p class="text-sm mt-1">
                  {{ toastMessage }}
                </p>
              </div>
              <div class="flex-shrink-0 ml-4">
                <button
                  @click="hideToast"
                  class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
                >
                  <span class="sr-only">Fermer</span>
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <!-- Progress bar -->
            <div
              v-if="showToast"
              class="absolute bottom-0 left-0 h-1 bg-current opacity-20 rounded-b-lg"
              :style="{ width: progressWidth + '%' }"
            ></div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        <Cog6ToothIcon class="inline h-8 w-8 mr-3 text-primary-600" />
        Paramètres
      </h1>
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Gérez vos préférences et paramètres de l'application
      </p>
    </div>

    <div class="space-y-8">
      <!-- Préférences générales -->
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700"
      >
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            <UserCircleIcon class="inline h-5 w-5 mr-2" />
            Préférences générales
          </h2>
        </div>
        <div class="p-6 space-y-6">
          <!-- Langue -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <LanguageIcon class="inline h-4 w-4 mr-2" />
                Langue de l'interface
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Choisissez votre langue préférée
              </p>
            </div>
            <select
              class="rounded-lg py-2 px-4 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
              <option value="fr">Français</option>
              <option value="en">English</option>
            </select>
          </div>

          <!-- Thème -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <SunIcon class="inline h-4 w-4 mr-2" />
                Thème d'affichage
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Choisissez entre le mode clair et sombre
              </p>
            </div>
            <ThemeToggle />
          </div>

          <!-- Notifications -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <BellIcon class="inline h-4 w-4 mr-2" />
                Notifications par email
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">Recevez des alertes par email</p>
            </div>
            <input
              type="checkbox"
              checked
              class="h-4 w-4 text-primary-600 input-field focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
            />
          </div>
        </div>
      </div>

      <!-- Paramètres de stock -->
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700"
      >
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            <CubeIcon class="inline h-5 w-5 mr-2" />
            Gestion des stocks
          </h2>
        </div>
        <div class="p-6 space-y-6">
          <!-- Seuil d'alerte global -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <ExclamationTriangleIcon class="inline h-4 w-4 mr-2" />
                Seuil d'alerte par défaut
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Seuil appliqué aux nouveaux stocks
              </p>
            </div>
            <input
              type="number"
              value="5"
              min="1"
              class="w-20 rounded-lg border-gray-300 input-field dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
            />
          </div>

          <!-- Alertes automatiques -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <BellAlertIcon class="inline h-4 w-4 mr-2" />
                Alertes automatiques
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Notifications automatiques pour les stocks faibles
              </p>
            </div>
            <input
              type="checkbox"
              checked
              class="h-4 w-4 text-primary-600 input-field focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
            />
          </div>
        </div>
      </div>

      <!-- Paramètres financiers -->
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700"
      >
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            <CurrencyEuroIcon class="inline h-5 w-5 mr-2" />
            Paramètres financiers
          </h2>
        </div>
        <div class="p-6 space-y-6">
          <!-- Devise -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <CurrencyEuroIcon class="inline h-4 w-4 mr-2" />
                Devise par défaut
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Devise utilisée pour tous les montants
              </p>
            </div>
            <select
              class="rounded-lg py-2 px-4 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
              <option value="EUR">Euro (€)</option>
              <option value="USD">Dollar ($)</option>
              <option value="GBP">Livre (£)</option>
              <option value="XOF">Franc CFA (XOF)</option>
            </select>
          </div>

          <!-- Format de date -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <CalendarIcon class="inline h-4 w-4 mr-2" />
                Format de date
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">Format d'affichage des dates</p>
            </div>
            <select
              class="rounded-lg py-2 px-4 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
              <option value="dd/mm/yyyy">DD/MM/YYYY</option>
              <option value="mm/dd/yyyy">MM/DD/YYYY</option>
              <option value="yyyy-mm-dd">YYYY-MM-DD</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Sauvegarde et export -->
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700"
      >
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            <DocumentArrowDownIcon class="inline h-5 w-5 mr-2" />
            Sauvegarde et export
          </h2>
        </div>
        <div class="p-6 space-y-6">
          <!-- Export des données -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <DocumentArrowDownIcon class="inline h-4 w-4 mr-2" />
                Exporter les données
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Télécharger toutes vos données au format CSV
              </p>
            </div>
            <button class="btn btn-secondary flex items-center">
              <DocumentArrowDownIcon class="w-4 h-4 mr-2" />
              Exporter
            </button>
          </div>

          <!-- Sauvegarde automatique -->
          <!-- <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <CloudArrowUpIcon class="inline h-4 w-4 mr-2" />
                Sauvegarde automatique
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Sauvegarde quotidienne de vos données
              </p>
            </div>
            <input
              type="checkbox"
              checked
              class="h-4 w-4 text-primary-600 input-field focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
            />
          </div> -->

          <!-- Sauvegarde de la base de données - Section améliorée -->
          <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  <CloudArrowUpIcon class="inline h-4 w-4 mr-2" />
                  Sauvegarde de la base de données
                </label>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Synchroniser la base de données avec le serveur distant
                </p>
              </div>
              <button
                @click="pushSqlite"
                :disabled="loading"
                :class="[
                  'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm transition-all duration-200',
                  loading
                    ? 'bg-gray-400 text-white cursor-not-allowed'
                    : 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98]',
                  'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800'
                ]"
              >
                <Transition
                  enter-active-class="transition-all duration-200"
                  enter-from-class="opacity-0 scale-75"
                  enter-to-class="opacity-100 scale-100"
                  leave-active-class="transition-all duration-200"
                  leave-from-class="opacity-100 scale-100"
                  leave-to-class="opacity-0 scale-75"
                  mode="out-in"
                >
                  <div v-if="loading" key="loading" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sauvegarde en cours...
                  </div>
                  <div v-else key="idle" class="flex items-center">
                    <CloudArrowUpIcon class="w-4 h-4 mr-2" />
                    Sauvegarder la BDD
                  </div>
                </Transition>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-4">
        <button class="btn btn-secondary flex items-center">
          <ArrowPathIcon class="w-4 h-4 mr-2" />
          Réinitialiser
        </button>
        <button class="btn btn-primary flex items-center">
          <CheckIcon class="w-4 h-4 mr-2" />
          Enregistrer les modifications
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import {
  Cog6ToothIcon,
  UserCircleIcon,
  LanguageIcon,
  SunIcon,
  BellIcon,
  CubeIcon,
  ExclamationTriangleIcon,
  BellAlertIcon,
  CurrencyEuroIcon,
  CalendarIcon,
  DocumentArrowDownIcon,
  CloudArrowUpIcon,
  ArrowPathIcon,
  CheckIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import { router } from '@inertiajs/vue3'
import { ref, onUnmounted } from 'vue'

const loading = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref<'success' | 'error'>('success')
const progressWidth = ref(100)
let toastTimer: number | null = null
let progressTimer: number | null = null

function showNotification(message: string, type: 'success' | 'error') {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  progressWidth.value = 100

  // Clear existing timers
  if (toastTimer) clearTimeout(toastTimer)
  if (progressTimer) clearInterval(progressTimer)

  // Progress bar animation
  let width = 100
  progressTimer = setInterval(() => {
    width -= 100 / 50 // 50 steps for 5 seconds
    progressWidth.value = width
    if (width <= 0) {
      clearInterval(progressTimer!)
    }
  }, 100) as unknown as number

  // Auto hide after 5 seconds
  toastTimer = setTimeout(() => {
    hideToast()
  }, 5000) as unknown as number
}

function hideToast() {
  showToast.value = false
  if (toastTimer) clearTimeout(toastTimer)
  if (progressTimer) clearInterval(progressTimer)
}

function pushSqlite() {
  loading.value = true

  router.post(route('settings.push-sqlite'), {}, {
    onSuccess: (page) => {
      const message = page.props.flash?.message || 'Base de données sauvegardée avec succès!'
      showNotification(message, 'success')
    },
    onError: (errors: any) => {
      const message = errors.message || 'Une erreur est survenue lors de la sauvegarde'
      showNotification(message, 'error')
    },
    onFinish: () => {
      loading.value = false
    }
  })
}

// Clean up timers on component unmount
onUnmounted(() => {
  if (toastTimer) clearTimeout(toastTimer)
  if (progressTimer) clearInterval(progressTimer)
})
</script>


