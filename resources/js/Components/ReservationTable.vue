<template>
  <div class="overflow-hidden">
    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            {{ $t('reservations.table.client') }}
          </th>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            {{ $t('reservations.table.apartment') }}
          </th>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            {{ $t('reservations.table.dates') }}
          </th>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            {{ $t('reservations.table.revenue') }}
          </th>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            {{ $t('reservations.table.status') }}
          </th>
          <th v-if="showActions" class="relative px-6 py-3">
            <span class="sr-only">{{ $t('common.actions') }}</span>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <tr
          v-for="reservation in displayedReservations"
          :key="reservation.id"
          class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
        >
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
              <div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ reservation.client }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ reservation.plateforme }}
                </div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ reservation.appartement?.name }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            <div>
              {{ formatDate(reservation.date_entree) }} - {{ formatDate(reservation.date_sortie) }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              {{ reservation.nombre_nuits }} {{ $t('common.nights') }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ formatCurrency(reservation.revenus_totaux) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <StatusBadge :status="reservation.statut_paiement" />
          </td>
          <td v-if="showActions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex items-center space-x-2">
              <button
                @click="$emit('edit', reservation)"
                class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
              >
                {{ $t('common.edit') }}
              </button>
              <button
                @click="$emit('delete', reservation)"
                class="text-error-600 hover:text-error-900 dark:text-error-400 dark:hover:text-error-300"
              >
                {{ $t('common.delete') }}
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="displayedReservations.length === 0" class="text-center py-12">
      <CalendarDaysIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $t('reservations.no_reservations') }}
      </h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ $t('reservations.no_reservations_description') }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { CalendarDaysIcon } from '@heroicons/vue/24/outline'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/Composables/useI18n'

const props = defineProps({
  reservations: {
    type: Array,
    default: () => [],
  },
  showActions: {
    type: Boolean,
    default: true,
  },
  showPagination: {
    type: Boolean,
    default: true,
  },
  maxItems: {
    type: Number,
    default: null,
  },
})

const { $t } = useI18n()

defineEmits(['edit', 'delete'])

const displayedReservations = computed(() => {
  if (props.maxItems) {
    return props.reservations.slice(0, props.maxItems)
  }
  return props.reservations
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
  }).format(amount)
}
</script>
