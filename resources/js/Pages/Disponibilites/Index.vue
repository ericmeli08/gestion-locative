<template>
    <AppLayout>

        <Head title="Disponibilités" />

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <CalendarIcon class="inline h-8 w-8 mr-3 text-primary-600" />
                Calendrier des Disponibilités
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Gérez la disponibilité de vos appartements
            </p>
        </div>

        <!-- Month selector -->
        <div
            class="mb-6 bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">

            <div class="flex items-center flex-col gap-3 lg:flex-row lg:justify-between">
                <div class="flex items-center space-x-4">
                    <button @click="previousMonth"
                        class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <ChevronLeftIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                    </button>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ formatMonth(currentMonth) }}
                    </h2>
                    <button @click="nextMonth"
                        class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <ChevronRightIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                    </button>
                </div>

                <!--  -->
                <div>
                    <!-- <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $t('reservations.filters.apartment') }}
          </label> -->
                    <select v-model="apartment"
                        class="block select-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">{{ $t('common.all') }}</option>
                        <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                            {{ apt.name }}
                        </option>
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-1">
                        <div class="w-4 h-4 bg-success-500 rounded"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Disponible</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-4 h-4 bg-primary-500 rounded"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Occupé</span>
                    </div>
                    <!-- <div class="flex items-center space-x-1">
            <div class="w-4 h-4 bg-warning-500 rounded"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Maintenance</span>
          </div> -->
                </div>
            </div>
        </div>

        <!-- Calendar grid -->
        <div class="space-y-8">
            <div v-for="apartment in filterApartment" :key="apartment.id"
                class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <BuildingOffice2Icon class="w-5 h-5 mr-2 text-primary-600" />
                        {{ apartment.name }}
                    </h3>
                </div>

                <!-- Calendar -->
                <div class="p-6">
                    <div class="grid grid-cols-7 gap-1 mb-4">
                        <div v-for="day in ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']" :key="day"
                            class="p-2 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ day }}
                        </div>
                    </div>

                    <div class="grid grid-cols-7 gap-1">
                        <div v-for="date in calendarDays" :key="date.date" class="aspect-square">
                            <div v-if="date.inMonth" class="w-full h-full">

                                <button v-if="!isDateAvailable(date.date, apartment.id)"
                                    @click="goToReservation(apartment.id, date.date)" :class="[
                                        'w-full h-full rounded-lg border-2 transition-all duration-200 hover:scale-105 flex items-center justify-center text-sm font-medium',
                                        getAvailableClasses(true),
                                    ]">
                                    {{ date.day }}
                                </button>
                                <button v-else @click="toggleAvailability(apartment.id, date.date)" :class="[
                                    'w-full h-full rounded-lg border-2 transition-all duration-200 hover:scale-105 flex items-center justify-center text-sm font-medium',
                                    getAvailableClasses(false),
                                ]">
                                    {{ date.day }}
                                </button>
                            </div>
                            <div v-else
                                class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600 text-sm">
                                {{ date.day }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status change modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/25 bg-opacity-25" @click="closeModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Informations sur la réservation
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Client
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ modalData.client }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Email
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ modalData.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Téléphone
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ modalData.telephone }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Plateforme
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white capitalize">{{ modalData.plateforme }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Date d'entrée
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ new
                                    Date(modalData.date_entree).toLocaleDateString('fr-FR') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Date de sortie
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ new
                                    Date(modalData.date_sortie).toLocaleDateString('fr-FR') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nombre de nuits
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ modalData.nombre_nuits }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Prix par nuit
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ modalData.prix_nuit }}€</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Revenus totaux
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">{{
                                    modalData.revenus_totaux }}€</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Statut paiement
                                </label>
                                <span :class="[
                                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                    modalData.statut_paiement === 'paid'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300'
                                        : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'
                                ]">
                                    {{ modalData.statut_paiement === 'paid' ? 'Payé' : 'Non payé' }}
                                </span>
                            </div>
                        </div>
                        <div v-if="modalData.notes" class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <p
                                class="text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 p-2 rounded border">
                                {{ modalData.notes }}
                            </p>
                        </div>
                    </div>


                    <div class="flex justify-end space-x-3 mt-6">
                        <button @click="closeModal" class="btn btn-secondary">Annuler</button>
                        <button @click="saveAvailability" class="btn btn-primary">More</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">

import { ref, computed } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import {
    CalendarIcon,
    BuildingOffice2Icon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    apartments: Array,
    disponibilites: Object,
    available: Object,
    currentMonth: String,
    startDate: String,
    endDate: String,
})


console.log('Disponibilités:', props.disponibilites);
console.log('Résultat:', props.available);

import { useI18n } from '@/Composables/useI18n'

const { $t } = useI18n()
const apartment = ref('')
const filterApartment = computed(() => {
    console.log('Filtering by apartment:', apartment.value);
    return apartment.value ? props.apartments.filter(apt => apt.id === apartment.value) : props.apartments
})

const isDateAvailable = (date, id_apartment) => {
    return props.available.find((d) => d.date === date && d.reservation.appartement_id === id_apartment) !== undefined
}

const showModal = ref(false)
const modalData = ref({
    apartmentId: null,
    date: null,
    status: 'available',
    notes: '',
})

// Helper function to format date in local timezone
const formatDateLocal = (date: Date): string => {
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    return `${year}-${month}-${day}`
}

const goToReservation = (apartmentId, date) => {
    console.log('goToReservation - Date clicked:', date);
    router.get(
        route('reservations.create'),
        { date, apartment_id: apartmentId },
        {
            preserveState: true,
            replace: false
        }
    )
}

const calendarDays = computed(() => {
    const start = new Date(props.startDate)
    const firstDay = new Date(start.getFullYear(), start.getMonth(), 1)
    const lastDay = new Date(start.getFullYear(), start.getMonth() + 1, 0)

    // Adjust to start on Monday
    const startCalendar = new Date(firstDay)
    startCalendar.setDate(startCalendar.getDate() - ((firstDay.getDay() + 6) % 7))

    const days = []
    const current = new Date(startCalendar)

    while (current <= lastDay || days.length % 7 !== 0) {
        days.push({
            date: formatDateLocal(current), // Use local timezone formatting
            day: current.getDate(),
            inMonth: current.getMonth() === start.getMonth(),
        })
        current.setDate(current.getDate() + 1)
    }

    return days
})

const getAvailableClasses = (valid: boolean) => {
    const baseClasses = 'border-gray-200 dark:border-gray-600'
    const statusClasses = valid ?
        'bg-success-100 text-success-800 border-success-300 hover:bg-success-200 dark:bg-success-900/50 dark:text-success-300 dark:border-success-700'
        : 'bg-primary-100 text-primary-800 border-primary-300 hover:bg-primary-200 dark:bg-primary-900/50 dark:text-primary-300 dark:border-primary-700'

    return `${baseClasses} ${statusClasses}`
}

const toggleAvailability = (apartmentId, date) => {
    console.log('toggleAvailability - Date clicked:', date);
    modalData.value = props.available.find((d) => d.date === date && d.reservation.appartement_id === apartmentId).reservation
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const saveAvailability = () => {
    router.post(
        route('disponibilites.store'),
        {
            appartement_id: modalData.value.apartmentId,
            date: modalData.value.date,
            statut: modalData.value.status,
            notes: modalData.value.notes,
        },
        {
            preserveState: true,
            onSuccess: () => {
                closeModal()
            },
        }
    )
}

const previousMonth = () => {
    const date = new Date(props.currentMonth + '-01')
    date.setMonth(date.getMonth() - 1)
    const newMonth = date.toISOString().slice(0, 7)
    router.get(route('disponibilites.index'), { month: newMonth })
}

const nextMonth = () => {
    const date = new Date(props.currentMonth + '-01')
    date.setMonth(date.getMonth() + 1)
    const newMonth = date.toISOString().slice(0, 7)
    router.get(route('disponibilites.index'), { month: newMonth })
}

const formatMonth = (month) => {
    const date = new Date(month + '-01')
    return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })
}
</script>
