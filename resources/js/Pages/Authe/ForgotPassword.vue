<template>
    <GuestLayout>
        <Head title="Mot de passe oublié" />

        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Logo et titre -->
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <KeyIcon class="h-8 w-8 text-white" />
                    </div>
                    <h2 class="mt-6 text-3xl font-bold text-gray-900 dark:text-white">
                        Mot de passe oublié
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Entrez votre email pour recevoir un lien de réinitialisation
                    </p>
                </div>

                <!-- Message de succès -->
                <div v-if="status" class="bg-success-50 dark:bg-success-900/50 border border-success-200 dark:border-success-800 rounded-lg p-4">
                    <div class="flex">
                        <CheckCircleIcon class="h-5 w-5 text-success-400" />
                        <div class="ml-3">
                            <p class="text-sm text-success-800 dark:text-success-200">
                                {{ status }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire -->
                <div class="bg-white dark:bg-gray-800 shadow-card-hover rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Adresse email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <EnvelopeIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200"
                                    placeholder="votre@email.com"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
                                />
                            </div>
                            <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Bouton d'envoi -->
                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl"
                            >
                                <span v-if="!form.processing" class="flex items-center">
                                    <PaperAirplaneIcon class="h-5 w-5 mr-2" />
                                    Envoyer le lien
                                </span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Envoi...
                                </span>
                            </button>
                        </div>

                        <!-- Lien retour -->
                        <div class="text-center">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                            >
                                <ArrowLeftIcon class="h-4 w-4 mr-1" />
                                Retour à la connexion
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    KeyIcon,
    EnvelopeIcon,
    PaperAirplaneIcon,
    ArrowLeftIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline'
import GuestLayout from '@/Layouts/GuestLayout.vue'

defineProps({
    status: String,
})

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.email'))
}
</script>