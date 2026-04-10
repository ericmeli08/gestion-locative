<template>
    <div class="flow-root">
        <!-- Modal de confirmation de suppression -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="showDeleteModal"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="background: rgba(0,0,0,0.45);"
                    @click.self="closeModal"
                >
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 w-full max-w-md shadow-xl"
                    >
                        <!-- Icône -->
                        <div class="flex justify-center mb-5">
                            <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <TrashIcon class="h-7 w-7 text-red-600 dark:text-red-400" />
                            </div>
                        </div>

                        <!-- Titre -->
                        <h2 class="text-center text-lg font-medium text-gray-900 dark:text-white mb-1">
                            Supprimer la réservation
                        </h2>

                        <!-- Description -->
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                            Vous êtes sur le point de supprimer la réservation de
                        </p>
                        <p class="text-center text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ reservationToDelete?.client }}
                        </p>
                        <p class="text-center text-xs text-gray-400 dark:text-gray-500 mb-6">
                            Cette action est irréversible et supprimera toutes les données associées.
                        </p>

                        <!-- Boutons -->
                        <div class="flex gap-3">
                            <button
                                @click="closeModal"
                                class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                            >
                                Annuler
                            </button>
                            <button
                                @click="confirmDelete"
                                class="flex-1 px-4 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-sm font-medium text-white transition-colors flex items-center justify-center gap-2"
                            >
                                <TrashIcon class="h-4 w-4" />
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-100 sm:pl-6">Client</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">Appartement</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">Dates</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">Plateforme</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">Montant</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-100">Statut</th>
                            <th scope="col" class="relative px-3 py-3.5"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <tr
                            v-for="reservation in displayedReservations"
                            :key="reservation.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                        >
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">
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
                                        ({{ reservation.nombre_nuits }} nuit{{ reservation.nombre_nuits > 1 ? 's' : '' }})
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <span :class="['inline-flex items-center rounded-full px-2 py-1 text-xs font-medium', platformClasses(reservation.plateforme)]">
                                    {{ reservation.plateforme }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(reservation.revenus_totaux) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <span :class="['inline-flex items-center rounded-full px-2 py-1 text-xs font-medium', statusClasses(reservation.statut_paiement)]">
                                    {{ statusLabel(reservation.statut_paiement) }}
                                </span>
                            </td>
                            <!-- Colonne actions -->
                            <td v-if="showActions" class="whitespace-nowrap px-3 py-4 text-sm text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button
                                        @click="$emit('edit', reservation)"
                                        class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 transition-colors p-1 rounded hover:bg-primary-50 dark:hover:bg-primary-900/20"
                                    >
                                        <PencilIcon class="h-4 w-4" />
                                    </button>
                                    <button
                                        @click="openDeleteModal(reservation)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20"
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="displayedReservations.length === 0">
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
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
import { ref, computed } from 'vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

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
    reservations: Reservation[]
    showActions?: boolean
    showPagination?: boolean
    maxItems?: number | null
}>()

const emit = defineEmits(['edit', 'delete'])

const showDeleteModal = ref(false)
const reservationToDelete = ref<Reservation | null>(null)

const openDeleteModal = (reservation: Reservation) => {
    reservationToDelete.value = reservation
    showDeleteModal.value = true
}

const closeModal = () => {
    showDeleteModal.value = false
    reservationToDelete.value = null
}

const confirmDelete = () => {
    if (reservationToDelete.value) {
        emit('delete', reservationToDelete.value)
    }
    closeModal()
}

const displayedReservations = computed(() => {
    if (props.maxItems) {
        return props.reservations.slice(0, props.maxItems)
    }
    return props.reservations
})

const formatDate = (date: string | undefined) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('fr-FR')
}

const formatCurrency = (amount: number | undefined) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
    }).format(amount || 0)
}

const platformClasses = (platform: string | undefined) => {
    switch (platform?.toLowerCase()) {
        case 'airbnb':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
        case 'booking':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
        case 'direct':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
    }
}

const statusClasses = (status: string | undefined) => {
    return status === 'paid'
        ? 'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400'
        : 'bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400'
}

const statusLabel = (status: string | undefined) => {
    const labels: Record<string, string> = {
        paid: 'Payé',
        unpaid: 'En attente',
    }
    return labels[status || ''] || status || 'N/A'
}

</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.2s ease;
}
.modal-enter-from, .modal-leave-to {
    opacity: 0;
}
.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
    transition: transform 0.2s ease;
}
.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
    transform: scale(0.95);
}
</style>
