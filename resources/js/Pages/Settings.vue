<template>
  <AppLayout>
    <Head title="Paramètres" />

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
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700">
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
            <select class="rounded-lg py-2 px-4 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
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
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Recevez des alertes par email
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

      <!-- Paramètres de stock -->
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700">
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
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700">
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
            <select class="rounded-lg py-2 px-4 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
              <option value="EUR">Euro (€)</option>
              <option value="USD">Dollar ($)</option>
              <option value="GBP">Livre (£)</option>
            </select>
          </div>

          <!-- Format de date -->
          <div class="flex items-center justify-between">
            <div>
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                <CalendarIcon class="inline h-4 w-4 mr-2" />
                Format de date
              </label>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Format d'affichage des dates
              </p>
            </div>
            <select class="rounded-lg py-2 px-4 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
              <option value="dd/mm/yyyy">DD/MM/YYYY</option>
              <option value="mm/dd/yyyy">MM/DD/YYYY</option>
              <option value="yyyy-mm-dd">YYYY-MM-DD</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Sauvegarde et export -->
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700">
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
          <div class="flex items-center justify-between">
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

<script setup>
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
  CheckIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'
</script>
