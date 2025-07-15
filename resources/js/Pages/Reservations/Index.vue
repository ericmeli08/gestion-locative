<template>
    <AppLayout>
        <!-- Page header -->
        <div class="mb-8 sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $t('reservations.title') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('reservations.subtitle') }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <Link
                    :href="route('reservations.create')"
                    class="inline-flex items-center justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-ring"
                >
                    <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                    {{ $t('reservations.add_new') }}
                </Link>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('reservations.filters.search') }}
                    </label>
                    <input
                        v-model="filters.search"
                        type="text"
                        :placeholder="$t('reservations.filters.search_placeholder')"
                        class="block input-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('reservations.filters.status') }}
                    </label>
                    <select
                        v-model="filters.status"
                        class="block select-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white  shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="">{{ $t('common.all') }}</option>
                        <option value="paid">{{ $t('reservations.status.paid') }}</option>
                        <option value="unpaid">{{ $t('reservations.status.unpaid') }}</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('reservations.filters.apartment') }}
                    </label>
                    <select
                        v-model="filters.apartment"
                        class="block select-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="">{{ $t('common.all') }}</option>
                        <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                            {{ apt.name }}
                        </option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button
                        @click="resetFilters"
                        class="w-full rounded-md bg-gray-100 dark:bg-gray-600 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-200 dark:hover:bg-gray-500 focus-ring"
                    >
                        {{ $t('common.reset_filters') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Reservations table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <ReservationTable 
                :reservations="reservations.data"
                :show-actions="true"
                @edit="editReservation"
                @delete="deleteReservation"
            />
            
            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <Pagination :links="reservations.links" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { PlusIcon } from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import ReservationTable from '@/Components/ReservationTable.vue'
import Pagination from '@/Components/Pagination.vue'
import { useI18n } from '@/Composables/useI18n'

const props = defineProps({
    reservations: Object,
    apartments: Array,
    filters: Object,
})

const { $t } = useI18n()

const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    apartment: props.filters.apartment || '',
})

// Watch for filter changes and update URL
watch(filters, (newFilters) => {
    router.get(route('reservations.index'), newFilters, {
        preserveState: true,
        replace: true,
    })
}, { debounce: 300 })

const resetFilters = () => {
    Object.keys(filters).forEach(key => {
        filters[key] = ''
    })
}

const editReservation = (reservation) => {
    router.visit(route('reservations.edit', reservation.id))
}

const deleteReservation = (reservation) => {
    if (confirm($t('reservations.confirm_delete'))) {
        router.delete(route('reservations.destroy', reservation.id))
    }
}
</script>