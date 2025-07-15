<template>
    <header class="bg-white dark:bg-gray-800 shadow">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
                <!-- Mobile menu button -->
                <button
                    @click="$emit('toggle-sidebar')"
                    class="lg:hidden -ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
                >
                    <Bars3Icon class="h-6 w-6" />
                </button>

                <!-- Breadcrumb -->
                <nav class="hidden lg:flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <div class="flex">
                                <Link :href="route('dashboard')" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    {{ $t('navigation.dashboard') }}
                                </Link>
                            </div>
                        </li>
                        <li v-if="currentPage">
                            <div class="flex items-center">
                                <ChevronRightIcon class="h-5 w-5 flex-shrink-0 text-gray-400" />
                                <span class="ml-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ currentPage }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Right section -->
                <div class="flex items-center space-x-4">
                    <!-- Theme toggle -->
                    <ThemeToggle />
                    
                    <!-- Language switcher -->
                    <LanguageSwitcher />
                    
                    <!-- Notifications -->
                    <button
                        @click="showNotifications = !showNotifications"
                        class="relative rounded-full bg-gray-100 dark:bg-gray-700 p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        <BellIcon class="h-6 w-6" />
                        <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 block h-4 w-4 rounded-full bg-red-500 text-xs font-medium text-white text-center leading-4">
                            {{ unreadCount > 99 ? '99+' : unreadCount }}
                        </span>
                    </button>

                    <!-- Profile dropdown -->
                    <Menu as="div" class="relative">
                        <MenuButton class="flex max-w-xs items-center rounded-full bg-gray-100 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <img
                                class="h-8 w-8 rounded-full"
                                :src="$page.props.auth.user.avatar || '/images/default-avatar.png'"
                                :alt="$page.props.auth.user.name"
                            />
                        </MenuButton>
                        <transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <MenuItem v-slot="{ active }">
                                    <Link
                                        :href="route('profile.edit')"
                                        :class="[active ? 'bg-gray-100 dark:bg-gray-700' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300']"
                                    >
                                        {{ $t('navigation.profile') }}
                                    </Link>
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <Link
                                        :href="route('settings')"
                                        :class="[active ? 'bg-gray-100 dark:bg-gray-700' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300']"
                                    >
                                        {{ $t('navigation.settings') }}
                                    </Link>
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        :class="[active ? 'bg-gray-100 dark:bg-gray-700' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300']"
                                    >
                                        {{ $t('navigation.logout') }}
                                    </Link>
                                </MenuItem>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import {
    Bars3Icon,
    BellIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import { useI18n } from '@/Composables/useI18n'

const { $t } = useI18n()
const page = usePage()

const showNotifications = ref(false)
const unreadCount = ref(3) // This would come from props or store

const currentPage = computed(() => {
    // Extract current page name from route
    const routeName = page.component.value
    // if (routeName.includes('Reservations')) return $t('navigation.reservations')
    // if (routeName.includes('Depenses')) return $t('navigation.expenses')
    // if (routeName.includes('Stocks')) return $t('navigation.stocks')
    return $t('navigation.reservations')
    // return null
})

defineEmits(['toggle-sidebar'])
</script>