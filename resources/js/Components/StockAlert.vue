<template>
    <div class="flex items-center p-4 border rounded-lg" :class="alertClasses">
        <div class="flex-shrink-0">
            <component :is="alertIcon" class="h-5 w-5" />
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium">
                {{ stock.element }}
            </h3>
            <div class="text-sm opacity-90">
                {{ $t('stocks.remaining') }}: {{ stock.reste }} / {{ stock.quantite_mois }}
            </div>
        </div>
        <div class="ml-4">
                <!-- :href="route('stocks.edit', stock.id)" -->
            <Link
                :href="'#'"
                class="text-sm font-medium underline hover:no-underline"
            >
                {{ $t('stocks.restock') }}
            </Link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { ExclamationTriangleIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import { useI18n } from '@/Composables/useI18n'

const props = defineProps({
    stock: {
        type: Object,
        required: true
    }
})

const { $t } = useI18n()

const alertLevel = computed(() => {
    const percentage = (props.stock.reste / props.stock.quantite_mois) * 100
    if (percentage <= 10) return 'critical'
    if (percentage <= 25) return 'warning'
    return 'normal'
})

const alertIcon = computed(() => {
    return alertLevel.value === 'critical' ? XCircleIcon : ExclamationTriangleIcon
})

const alertClasses = computed(() => {
    const classes = {
        critical: 'border-error-200 bg-error-50 text-error-700 dark:border-error-800 dark:bg-error-900/50 dark:text-error-400',
        warning: 'border-warning-200 bg-warning-50 text-warning-700 dark:border-warning-800 dark:bg-warning-900/50 dark:text-warning-400',
        normal: 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300'
    }
    return classes[alertLevel.value]
})
</script>