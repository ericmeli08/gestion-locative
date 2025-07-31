<template>
    <AppLayout>
        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ reservation?.id ? $t('reservations.edit_title') : $t('reservations.create_title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{
                    reservation?.id ? $t('reservations.edit_subtitle') : $t('reservations.create_subtitle')
                }}
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <form @submit.prevent="submit" class="space-y-6 p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Client -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('reservations.fields.client') }}
                        </label>
                        <input v-model="form.client" type="text" required
                            class="block input-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-500': errors?.client }" />
                        <p v-if="errors?.client" class="mt-1 text-sm text-red-600">{{ errors?.client }}</p>
                    </div>

                    <!-- Plateforme -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('reservations.fields.platform') }}
                        </label>
                        <select v-model="form.plateforme" required
                            class="block select-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-500': errors?.plateforme }">
                            <option value="">{{ $t('common.select_option') }}</option>
                            <option value="airbnb">Airbnb</option>
                            <option value="booking">Booking.com</option>
                            <option value="direct">{{ $t('reservations.platforms.direct') }}</option>
                        </select>
                        <p v-if="errors?.plateforme" class="mt-1 text-sm text-red-600">
                            {{ errors?.plateforme }}
                        </p>
                    </div>

                    <!-- Appartement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('reservations.fields.apartment') }}
                        </label>
                        <select v-model="form.appartement_id" required
                            class="block select-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-500': errors?.appartement_id }">
                            <option value="">{{ $t('common.select_option') }}</option>
                            <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                                {{ apt.name }}
                            </option>
                        </select>
                        <p v-if="errors?.appartement_id" class="mt-1 text-sm text-red-600">
                            {{ errors?.appartement_id }}
                        </p>
                    </div>

                    <!-- Prix par nuit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('reservations.fields.price_per_night') }}
                        </label>
                        <input v-model.number="form.prix_nuit" type="number" step="0.01" required
                            class="block input-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-500': errors?.prix_nuit }" />
                        <p v-if="errors?.prix_nuit" class="mt-1 text-sm text-red-600">
                            {{ errors?.prix_nuit }}
                        </p>
                    </div>

                    <!-- Date d'entrée -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('reservations.fields.check_in') }}
                        </label>
                        <input v-model="form.date_entree" type="date" required
                            class="block input-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-500': errors?.date_entree }" />
                        <p v-if="errors?.date_entree" class="mt-1 text-sm text-red-600">
                            {{ errors?.date_entree }}
                        </p>
                    </div>

                    <!-- Date de sortie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $t('reservations.fields.check_out') }}
                        </label>
                        <input v-model="form.date_sortie" type="date" required
                            class="block input-field w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-500': errors?.date_sortie }" />
                        <p v-if="errors?.date_sortie" class="mt-1 text-sm text-red-600">
                            {{ errors?.date_sortie }}
                        </p>
                    </div>
                </div>

                <!-- Calculs automatiques -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ $t('reservations.calculations') }}
                    </h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('reservations.fields.nights_count') }}
                            </label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ nombreNuits }} {{ $t('common.nights') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('reservations.fields.total_revenue') }}
                            </label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatCurrency(revenusTotal) }}
                            </p>
                        </div>
                        <!--  -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('reservations.fields.payment_status') }}
                            </label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input v-model="isPaid" type="checkbox" value="paid"
                                        class="rounded input-field border-gray-300 text-primary-600 focus:ring-primary-500" />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $t('reservations.status.paid') }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date de paiement -->
                <div v-if="isPaid">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('reservations.fields.payment_date') }}
                    </label>
                    <input v-model="form.date_paiement" type="date"
                        class="block w-full input-field rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        :class="{ 'border-red-500': errors?.date_paiement }" />
                    <p v-if="errors?.date_paiement" class="mt-1 text-sm text-red-600">
                        {{ errors?.date_paiement }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <Link :href="route('reservations.index')"
                        class="rounded-md bg-white dark:bg-gray-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 focus-ring">
                    {{ $t('common.cancel') }}
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-ring disabled:opacity-50">
                        {{ form.processing ? $t('common.saving') : $t('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, watch, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useI18n } from '@/Composables/useI18n'
import dayjs from 'dayjs'

const formatDate = (isoString) => isoString? dayjs(isoString).format('YYYY-MM-DD'): dayjs().format('YYYY-MM-DD')

const props = defineProps({
    reservation: Object,
    apartments: Array,
    date: String,
    apartment_id: String,
})

const { $t } = useI18n()

const form = useForm({
    client: props.reservation?.client || '',
    plateforme: props.reservation?.plateforme || '',
    appartement_id: props.reservation?.appartement_id?? props.apartment_id?? '',
    prix_nuit: props.reservation?.prix_nuit || 0,
    date_entree: props.reservation?.date_entree ? formatDate(props.reservation?.date_entree) : props.date || '',
    date_sortie: props.reservation?.date_sortie ? formatDate(props.reservation?.date_sortie) : '',
    statut_paiement: props.reservation?.statut_paiement || 'unpaid',
    date_paiement: props.reservation?.date_paiement ? formatDate(props.reservation?.date_paiement) : '',
    id_reservation: null,
})
const isPaid = ref(props.reservation?.statut_paiement === 'paid')

const nombreNuits = computed(() => {
    if (!form.date_entree || !form.date_sortie) return 0
    const entree = new Date(form.date_entree)
    const sortie = new Date(form.date_sortie)
    const diffTime = Math.abs(sortie - entree)
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
})

const revenusTotal = computed(() => {
    return nombreNuits.value * form.prix_nuit
})

// Auto-fill payment date when status is changed to paid
watch(
    () => isPaid.value,
    (newStatus) => {
        if (newStatus && !form.date_paiement) {
            form.date_paiement = new Date().toISOString().split('T')[0]
        }
    }
)
const errors = ref({})
const submit = () => {
    form.statut_paiement = isPaid.value ? 'paid' : 'unpaid'
    const url = props.reservation?.id
        ? route('reservations.update', props.reservation.id)
        : route('reservations.store')
    form.id_reservation = props.reservation?.id ? props.reservation?.id : null
    const method = props.reservation?.id ? 'put' : 'post'

    form[method](url,{
        onSuccess: () => {
            // Redirect handled by Laravel
        },
        onError: (err) => {
            console.error('Form submission errors:', err)
            errors.value = err
        },
    })
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
    }).format(amount)
}
</script>
