<template>
    <li>
        <Link
            :href="href"
            :class="[
                active
                    ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400'
                    : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200'
            ]"
        >
            <component
                :is="icon"
                :class="[
                    active 
                        ? 'text-primary-600 dark:text-primary-400' 
                        : 'text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400',
                    'h-6 w-6 shrink-0 transition-colors duration-200'
                ]"
            />
            <slot />
        </Link>
    </li>
</template>

<script setup>
defineProps({
    href: String,
    icon: Object,
    active: Boolean,
})
</script>