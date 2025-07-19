<template>
  <span :class="badgeClasses">
    {{ statusText }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from '@/Composables/useI18n'

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
})

const { $t } = useI18n()

const statusText = computed(() => {
  const statusMap = {
    paid: $t('reservations.status.paid'),
    unpaid: $t('reservations.status.unpaid'),
    partial: $t('reservations.status.partial'),
    available: $t('availability.status.available'),
    occupied: $t('availability.status.occupied'),
    maintenance: $t('availability.status.maintenance'),
  }
  return statusMap[props.status] || props.status
})

const badgeClasses = computed(() => {
  const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'

  const statusClasses = {
    paid: 'bg-success-100 text-success-800 dark:bg-success-900/50 dark:text-success-400',
    unpaid: 'bg-error-100 text-error-800 dark:bg-error-900/50 dark:text-error-400',
    partial: 'bg-warning-100 text-warning-800 dark:bg-warning-900/50 dark:text-warning-400',
    available: 'bg-success-100 text-success-800 dark:bg-success-900/50 dark:text-success-400',
    occupied: 'bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-400',
    maintenance: 'bg-warning-100 text-warning-800 dark:bg-warning-900/50 dark:text-warning-400',
  }

  return `${baseClasses} ${statusClasses[props.status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'}`
})
</script>
