<template>
    <div class="flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 sm:pl-6">
                                Client
                            </th>
                            <th scope="col"
                                class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">
                                Appartement
                            </th>
                            <th scope="col"
                                class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">
                                Dates
                            </th>
                            <th scope="col"
                                class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">
                                Plateforme
                            </th>
                            <th scope="col"
                                class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">
                                Montant
                            </th>
                            <th scope="col"
                                class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">
                                Statut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <tr v-for="reservation in displayedReservations" :key="reservation.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td
                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">
                                <div class="flex flex-col">
                                    <span>{{ reservation.client }}</span>
                                    <span v-if="reservation.email" class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ reservation.email }}
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ reservation.appartement?.name || 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div class="flex flex-col">
                                    <span>{{ formatDate(reservation.date_entree) }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(reservation.date_sortie) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        ({{ reservation.nombre_nuits }} nuit{{
                                            reservation.nombre_nuits > 1 ? 's' : ''
                                        }})
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <span :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                    platformClasses(reservation.plateforme),
                                ]">
                                    {{ reservation.plateforme }}
                                </span>
                            </td>
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(reservation.revenus_totaux) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <span :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                    statusClasses(reservation.statut_paiement),
                                ]">
                                    {{ statusLabel(reservation.statut_paiement) }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="displayedReservations.length === 0">
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucune réservation
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Reservation {
    id: number
    client: string
    email?: string
    appartement?: { name?: string }
    date_entree?: string
    date_sortie?: string
    nombre_nuits?: number
    plateforme?: string
    revenus_totaux?: number
    statut_paiement?: string
}

const props = defineProps<{
    reservations: Reservation[],
    showPagination?: boolean,
    maxItems?: number | null,
}>()

console.log( "Reservations: ", props.reservations)

const displayedReservations = computed(() => {
    if (props.maxItems) {
        return props.reservations.slice(0, props.maxItems)
    }
    return props.reservations
})

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

const formatCurrency = (amount) => {
    if (amount == null || isNaN(amount)) return '0 FCFA'
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount)
}

const platformClasses = (platform) => {
    const classes = {
        airbnb: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        booking:
            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        direct:
            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    }
    return classes[platform] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
}

const statusClasses = (status) => {
    const classes = {
        paid: 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400',
        unpaid:
            'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400',
        partial:
            'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
    }
    return classes[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
}

const statusLabel = (status) => {
    const labels = {
        paid: 'Payé',
        unpaid: 'Non payé',
        partial: 'Partiel',
    }
    return labels[status] || status
}
</script>

