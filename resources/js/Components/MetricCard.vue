<template>
    <div
        class="bg-white dark:bg-gray-800 shadow-card hover:shadow-card-hover dark:shadow-card-dark dark:hover:shadow-card-hover-dark rounded-xl transition-all duration-300 hover:scale-105 border border-gray-200 dark:border-gray-700 animate-fade-in">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div :class="[
                        'inline-flex items-center justify-center p-3 rounded-xl shadow-lg',
                        colorClasses,
                    ]">
                        <component :is="iconComponent" class="h-7 w-7" />
                    </div>
                </div>
                <div class="ml-5 flex-1 min-w-0">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            {{ title }}
                        </dt>
                        <dd class="flex items-baseline mt-1">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white truncate" :title="value">
                                {{ value }}
                            </div>
                            <div v-if="trend" :class="[
                                'ml-2 flex items-center text-sm font-semibold flex-shrink-0',
                                trendClasses,
                            ]">
                                <component :is="trendIcon" class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
                                <span class="sr-only">
                                    {{ trendType === 'positive' ? 'Increased' : 'Decreased' }} by
                                </span>
                                <span class="ml-1">{{ trend }}</span>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import {
    CurrencyEuroIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ChartBarIcon,
    HomeIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    title: String,
    value: String,
    trend: String,
    trendType: {
        type: String,
        default: 'positive',
    },
    icon: String,
    color: {
        type: String,
        default: 'primary',
    },
})

const iconComponent = computed(() => {
    const icons = {
        CurrencyEuroIcon,
        ArrowTrendingDownIcon,
        ChartBarIcon,
        HomeIcon,
    }
    return icons[props.icon] || ChartBarIcon
})

const colorClasses = computed(() => {
    const colors = {
        primary:
            'bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-primary-500/25',
        success:
            'bg-gradient-to-br from-success-500 to-success-600 text-white shadow-success-500/25',
        warning:
            'bg-gradient-to-br from-warning-500 to-warning-600 text-white shadow-warning-500/25',
        error:
            'bg-gradient-to-br from-error-500 to-error-600 text-white shadow-error-500/25',
    }
    return colors[props.color] || colors.primary
})

const trendIcon = computed(() => {
    if (!props.trend) return null

    const isPositive = props.trend.startsWith('+')
    const isNegative = props.trend.startsWith('-')

    if (props.color === 'error') {
        return isNegative ? ArrowTrendingUpIcon : ArrowTrendingDownIcon
    }

    return isPositive ? ArrowTrendingUpIcon : ArrowTrendingDownIcon
})

const trendClasses = computed(() => {
    if (!props.trend) return ''

    const isPositive = props.trend.startsWith('+')
    const isNegative = props.trend.startsWith('-')

    if (props.color === 'error') {
        return isNegative
            ? 'text-success-600 dark:text-success-400'
            : 'text-error-600 dark:text-error-400'
    }

    return isPositive
        ? 'text-success-600 dark:text-success-400'
        : 'text-error-600 dark:text-error-400'
})
</script>

