<template>
    <!-- Mobile sidebar -->
    <div class="lg:hidden">
        <Transition
            enter-active-class="transition-opacity ease-linear duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-linear duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="isOpen" class="fixed inset-0 z-50 flex">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="$emit('close')"></div>
                <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white dark:bg-gray-800">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button
                            @click="$emit('close')"
                            class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        >
                            <XMarkIcon class="h-6 w-6 text-white" />
                        </button>
                    </div>
                    <SidebarContent />
                </div>
            </div>
        </Transition>
    </div>

    <!-- Desktop sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
            <SidebarContent />
        </div>
    </div>
</template>

<script setup>
import { XMarkIcon } from '@heroicons/vue/24/outline'
import SidebarContent from '@/Components/SidebarContent.vue'

defineProps({
    isOpen: Boolean
})

defineEmits(['close'])
</script>