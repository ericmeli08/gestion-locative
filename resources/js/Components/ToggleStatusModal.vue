<template>
    <!-- Backdrop -->
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="$emit('cancel')"
      >
        <!-- Blur backdrop -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" />

        <!-- Modal -->
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
          appear
        >
          <div
            v-if="show"
            class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden"
          >
            <!-- Top accent bar -->
            <div
              :class="[
                'h-1.5 w-full',
                appartement?.active
                  ? 'bg-gradient-to-r from-orange-400 via-red-400 to-rose-500'
                  : 'bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-500'
              ]"
            />

            <div class="p-6">
              <!-- Header -->
              <div class="flex items-start gap-4 mb-5">
                <!-- Icon -->
                <div
                  :class="[
                    'flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl',
                    appartement?.active
                      ? 'bg-orange-100 dark:bg-orange-900/30'
                      : 'bg-emerald-100 dark:bg-emerald-900/30'
                  ]"
                >
                  <!-- Deactivate icon -->
                  <svg
                    v-if="appartement?.active"
                    class="w-6 h-6 text-orange-500"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                  </svg>
                  <!-- Activate icon -->
                  <svg
                    v-else
                    class="w-6 h-6 text-emerald-500"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>

                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ appartement?.active ? 'Désactiver' : 'Réactiver' }} l'appartement
                  </h3>
                  <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ appartement?.name }}</span>
                  </p>
                </div>
              </div>

              <!-- Risks section - DEACTIVATE -->
              <div v-if="appartement?.active" class="space-y-3 mb-6">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                  <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  Conséquences de la désactivation
                </p>

                <div class="rounded-xl border border-orange-200 dark:border-orange-800/50 bg-orange-50 dark:bg-orange-900/20 divide-y divide-orange-200 dark:divide-orange-800/50">
                  <div class="flex gap-3 p-3.5">
                    <div class="flex-shrink-0 mt-0.5">
                      <div class="w-5 h-5 rounded-full bg-orange-200 dark:bg-orange-800 flex items-center justify-center">
                        <svg class="w-3 h-3 text-orange-600 dark:text-orange-300" fill="currentColor" viewBox="0 0 12 12">
                          <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"/>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-orange-800 dark:text-orange-200">Réservations bloquées</p>
                      <p class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">
                        Aucune nouvelle réservation ne pourra être créée pour cet appartement.
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 p-3.5">
                    <div class="flex-shrink-0 mt-0.5">
                      <div class="w-5 h-5 rounded-full bg-orange-200 dark:bg-orange-800 flex items-center justify-center">
                        <svg class="w-3 h-3 text-orange-600 dark:text-orange-300" fill="currentColor" viewBox="0 0 12 12">
                          <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"/>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-orange-800 dark:text-orange-200">Charges récurrentes suspendues</p>
                      <p class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">
                        Toutes les charges récurrentes liées à cet appartement seront passées en <strong>non-récurrentes</strong>. Les générations futures seront ignorées.
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 p-3.5">
                    <div class="flex-shrink-0 mt-0.5">
                      <div class="w-5 h-5 rounded-full bg-orange-200 dark:bg-orange-800 flex items-center justify-center">
                        <svg class="w-3 h-3 text-orange-600 dark:text-orange-300" fill="currentColor" viewBox="0 0 12 12">
                          <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"/>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-orange-800 dark:text-orange-200">Retiré du calendrier</p>
                      <p class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">
                        L'appartement n'apparaîtra plus dans la vue des disponibilités.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Info section - REACTIVATE -->
              <div v-else class="space-y-3 mb-6">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                  </svg>
                  Ce qui sera restauré
                </p>

                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800/50 bg-emerald-50 dark:bg-emerald-900/20 divide-y divide-emerald-200 dark:divide-emerald-800/50">
                  <div class="flex gap-3 p-3.5">
                    <div class="flex-shrink-0 mt-0.5">
                      <div class="w-5 h-5 rounded-full bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-300" fill="currentColor" viewBox="0 0 12 12">
                          <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"/>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">Réservations réactivées</p>
                      <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">
                        L'appartement sera à nouveau disponible pour de nouvelles réservations.
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 p-3.5">
                    <div class="flex-shrink-0 mt-0.5">
                      <div class="w-5 h-5 rounded-full bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-300" fill="currentColor" viewBox="0 0 12 12">
                          <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"/>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">Charges récurrentes restaurées</p>
                      <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">
                        Les charges qui étaient récurrentes avant la désactivation redeviendront récurrentes. <strong>Les charges non-générées pendant la période d'inactivité seront ignorées.</strong>
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 p-3.5">
                    <div class="flex-shrink-0 mt-0.5">
                      <div class="w-5 h-5 rounded-full bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-300" fill="currentColor" viewBox="0 0 12 12">
                          <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"/>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">Retour dans le calendrier</p>
                      <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">
                        L'appartement réapparaîtra dans la vue des disponibilités.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex gap-3">
                <button
                  type="button"
                  @click="$emit('cancel')"
                  class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400"
                >
                  Annuler
                </button>
                <button
                  type="button"
                  @click="$emit('confirm')"
                  :class="[
                    'flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-all focus:outline-none focus:ring-2 focus:ring-offset-2',
                    appartement?.active
                      ? 'bg-orange-500 hover:bg-orange-600 focus:ring-orange-500 shadow-orange-200 dark:shadow-orange-900'
                      : 'bg-emerald-500 hover:bg-emerald-600 focus:ring-emerald-500 shadow-emerald-200 dark:shadow-emerald-900',
                    'shadow-lg hover:shadow-xl hover:scale-[1.02]'
                  ]"
                >
                  {{ appartement?.active ? 'Désactiver' : 'Réactiver' }}
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </template>

  <script setup lang="ts">
  defineProps<{
    show: boolean
    appartement: { name: string; active: boolean } | null
  }>()
  defineEmits(['confirm', 'cancel'])
  </script>
