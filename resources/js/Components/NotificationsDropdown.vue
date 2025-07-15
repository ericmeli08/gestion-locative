<template>
    <div v-if="showNotifications" class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-25" @click="close"></div>
        <div class="absolute top-0 right-0 h-full w-96 bg-white dark:bg-gray-800 shadow-xl">
            <div class="flex h-full flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $t('notifications.title') }}
                    </h2>
                    <button
                        @click="close"
                        class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        <XMarkIcon class="h-6 w-6" />
                    </button>
                </div>

                <!-- Notifications list -->
                <div class="flex-1 overflow-y-auto">
                    <div v-if="notifications.length === 0" class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <BellIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $t('notifications.no_notifications') }}
                            </h3>
                        </div>
                    </div>
                    
                    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                            :class="{ 'bg-blue-50 dark:bg-blue-900/50': !notification.read_at }"
                        >
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div :class="[
                                        'inline-flex items-center justify-center h-8 w-8 rounded-full',
                                        getNotificationTypeClasses(notification.type)
                                    ]">
                                        <component :is="getNotificationIcon(notification.type)" class="h-4 w-4" />
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ notification.data.title }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ notification.data.message }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        {{ formatTimeAgo(notification.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                    <button
                        @click="markAllAsRead"
                        class="w-full rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-ring"
                    >
                        {{ $t('notifications.mark_all_read') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import {
    XMarkIcon,
    BellIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline'
import { useI18n } from '@/Composables/useI18n'

const { $t } = useI18n()
const showNotifications = ref(false)

// Mock notifications - these would come from props
const notifications = ref([
    {
        id: 1,
        type: 'stock_alert',
        data: {
            title: 'Stock faible',
            message: 'Le stock de serviettes est en dessous du seuil critique'
        },
        read_at: null,
        created_at: new Date().toISOString()
    },
    {
        id: 2,
        type: 'payment_received',
        data: {
            title: 'Paiement reçu',
            message: 'Paiement de 150€ reçu pour la réservation #123'
        },
        read_at: new Date().toISOString(),
        created_at: new Date(Date.now() - 86400000).toISOString()
    }
])

const close = () => {
    showNotifications.value = false
}

const getNotificationIcon = (type) => {
    const icons = {
        stock_alert: ExclamationTriangleIcon,
        payment_received: CheckCircleIcon,
        info: InformationCircleIcon,
    }
    return icons[type] || InformationCircleIcon
}

const getNotificationTypeClasses = (type) => {
    const classes = {
        stock_alert: 'bg-warning-100 text-warning-600 dark:bg-warning-900/50 dark:text-warning-400',
        payment_received: 'bg-success-100 text-success-600 dark:bg-success-900/50 dark:text-success-400',
        info: 'bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400',
    }
    return classes[type] || classes.info
}

const formatTimeAgo = (date) => {
    const now = new Date()
    const notificationDate = new Date(date)
    const diffInHours = Math.floor((now - notificationDate) / (1000 * 60 * 60))
    
    if (diffInHours < 1) return $t('time.just_now')
    if (diffInHours < 24) return $t('time.hours_ago', { count: diffInHours })
    
    const diffInDays = Math.floor(diffInHours / 24)
    return $t('time.days_ago', { count: diffInDays })
}

const markAllAsRead = () => {
    router.post(route('notifications.mark-all-read'), {}, {
        preserveState: true,
        onSuccess: () => {
            notifications.value.forEach(notification => {
                notification.read_at = new Date().toISOString()
            })
        }
    })
}
</script>