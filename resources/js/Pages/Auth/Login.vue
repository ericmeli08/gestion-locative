<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline'
import Logo from '@/assets/images/logo.png'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <GuestLayout>

        <Head title="Connexion " />

        <div
            class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute top-0 -left-4 w-72 h-72 bg-primary-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob">
                </div>
                <div
                    class="absolute top-0 -right-4 w-72 h-72 bg-success-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000">
                </div>
                <div
                    class="absolute -bottom-8 left-20 w-72 h-72 bg-warning-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
                </div>

                <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-primary-500 rounded-full animate-float"></div>
                <div
                    class="absolute top-1/3 right-1/4 w-3 h-3 bg-success-500 rounded-full animate-float animation-delay-1000">
                </div>
                <div
                    class="absolute bottom-1/4 right-1/3 w-2 h-2 bg-warning-500 rounded-full animate-float animation-delay-2000">
                </div>
                <div
                    class="absolute bottom-1/3 left-1/3 w-3 h-3 bg-error-500 rounded-full animate-float animation-delay-3000">
                </div>
            </div>

            <div class="max-w-xl w-full space-y-8 relative z-10">
                <div class="text-center animate-slide-down">
                    <div
                        class="mx-auto w-70  rounded-2xl flex items-center justify-center shadow-xl shadow-primary-500/30 animate-pulse-slow">
                        <!-- <LockClosedIcon class="h-8 w-8 text-white" /> -->
                        <img :src="Logo" alt="Logo" class="w-70 h-auto rounded-xl shadow" />
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                        Bienvenue
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Connectez-vous à votre compte
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 space-y-6 backdrop-blur-sm bg-opacity-90 dark:bg-opacity-90 border border-gray-200 dark:border-gray-700 animate-fade-in">
                    <div v-if="status" class="rounded-lg bg-success-50 dark:bg-success-900/20 p-4 animate-slide-down">
                        <p class="text-sm font-medium text-success-800 dark:text-success-300">
                            {{ status }}
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-4">
                            <div class="transform transition-all duration-300 hover:scale-[1.01]">
                                <InputLabel for="email" value="Adresse email"
                                    class="text-gray-700 dark:text-gray-300 font-medium" />
                                <div class="mt-2 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <EnvelopeIcon class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <TextInput id="email" type="email"
                                        class="pl-10 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white transition-all duration-300"
                                        v-model="form.email" required autofocus autocomplete="username"
                                        placeholder="vous@exemple.com" />
                                </div>
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="transform transition-all duration-300 hover:scale-[1.01]">
                                <InputLabel for="password" value="Mot de passe"
                                    class="text-gray-700 dark:text-gray-300 font-medium" />
                                <div class="mt-2 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <LockClosedIcon class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <TextInput id="password" type="password"
                                        class="pl-10 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white transition-all duration-300"
                                        v-model="form.password" required autocomplete="current-password"
                                        placeholder="••••••••" />
                                </div>
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>
                        </div>

                        <div class=" flex items-center justify-between">
                            <label class="flex items-center group cursor-pointer">
                                <Checkbox name="remember" v-model:checked="form.remember"
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 transition-all duration-300" />
                                <span
                                    class="ml-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors duration-300">
                                    Se souvenir de moi
                                </span>
                            </label>

                            <div v-if="canResetPassword" class="text-right">
                                <Link :href="route('password.request')"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-300">
                                Mot de passe oublié ?
                                </Link>
                            </div>
                        </div>

                        <div class="pt-2">
                            <PrimaryButton type="submit"
                                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transform transition-all duration-300 hover:scale-[1.02] hover:shadow-xl"
                                :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                                <span v-if="!form.processing">Se connecter</span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Connexion en cours...
                                </span>
                            </PrimaryButton>
                        </div>

                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Pas encore de compte ?
                                <Link :href="route('register')"
                                    class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-300">
                                Créer un compte
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>

                <div class="text-center text-xs text-gray-500 dark:text-gray-400 animate-fade-in animation-delay-500">
                    <p>© 2024 Votre Entreprise. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </GuestLayout>

</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }

    33% {
        transform: translate(30px, -50px) scale(1.1);
    }

    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }

    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
        opacity: 0.5;
    }

    50% {
        transform: translateY(-20px);
        opacity: 1;
    }
}

@keyframes pulse-slow {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.8;
    }
}

@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-slide-down {
    animation: slide-down 0.5s ease-out;
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animation-delay-1000 {
    animation-delay: 1s;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-3000 {
    animation-delay: 3s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animation-delay-500 {
    animation-delay: 0.5s;
}
</style>
