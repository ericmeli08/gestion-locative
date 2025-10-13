<template>
  <div class="flex grow flex-col gap-y-5 overflow-y-auto px-6">
    <!-- Logo -->
    <div class="flex  p-2 flex-col h-18 shrink-0 items-start">
        <img class="h-10 w-auto" :src="Logo" alt="Gestion Locative" />
        <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white"> Gestion Locative </span>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
      <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
          <ul role="list" class="-mx-2 space-y-1">
            <SidebarItem
              :href="route('dashboard')"
              :icon="HomeIcon"
              :active="$page.component === 'Dashboard'"
            >
              {{ $t('navigation.dashboard') }}
            </SidebarItem>

            <!-- :href="'#'" -->
            <SidebarItem
              :href="route('reservations.index')"
              :icon="CalendarDaysIcon"
              :active="$page.component.startsWith('Reservations')"
            >
              {{ $t('navigation.reservations') }}
            </SidebarItem>

            <!-- :href="'#'" -->
            <SidebarItem
              :href="route('depenses.index')"
              :icon="BanknotesIcon"
              :active="$page.component.startsWith('Depenses')"
            >
              {{ $t('navigation.expenses') }}
            </SidebarItem>

            <!-- :href="'#'" -->
            <SidebarItem
              :href="route('charges.index')"
              :icon="CreditCardIcon"
              :active="$page.component.startsWith('ChargesMensuelles')"
            >
              {{ $t('navigation.monthly_charges') }}
            </SidebarItem>

            <!-- :href="'#'" -->
            <SidebarItem
              :href="route('stocks.index')"
              :icon="CubeIcon"
              :active="$page.component.startsWith('Stocks')"
            >
              {{ $t('navigation.stocks') }}
            </SidebarItem>

            <SidebarItem
              :href="route('appartements.index')"
              :icon="BuildingOffice2Icon"
              :active="$page.component.startsWith('Appartement')"
            >
              {{ $t('navigation.appartements') }}
            </SidebarItem>

            <!-- :href="'#'" -->
            <SidebarItem
              :href="route('degats.index')"
              :icon="WrenchScrewdriverIcon"
              :active="$page.component.startsWith('DegatsReparations')"
            >
              {{ $t('navigation.repairs') }}
            </SidebarItem>

            <!-- :href="'#'" -->
            <SidebarItem
              :href="route('disponibilites.index')"
              :icon="CalendarIcon"
              :active="$page.component.startsWith('Disponibilites')"
            >
              {{ $t('navigation.availability') }}
            </SidebarItem>
          </ul>
        </li>

        <!-- Reports section -->
        <li>
          <div class="text-xs font-semibold leading-6 text-gray-400 dark:text-gray-500">
            {{ $t('navigation.reports') }}
          </div>
          <ul role="list" class="-mx-2 mt-2 space-y-1">
            <SidebarItem
              :href="route('reports.monthly')"
              :icon="ChartBarIcon"
              :active="$page.component.startsWith('Reports/Monthly')"
            >
              {{ $t('navigation.monthly_summary') }}
            </SidebarItem>

            <SidebarItem
              :href="route('reports.occupancy')"
              :icon="PresentationChartLineIcon"
              :active="$page.component.startsWith('Reports/Occupancy')"
            >
              {{ $t('navigation.occupancy_rate') }}
            </SidebarItem>
          </ul>
        </li>

        <!-- Settings -->
        <li class="mt-auto">
          <SidebarItem
            :href="route('settings')"
            :icon="Cog6ToothIcon"
            :active="$page.component === 'Settings'"
          >
            {{ $t('navigation.settings') }}
          </SidebarItem>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import {
  HomeIcon,
  CalendarDaysIcon,
  BanknotesIcon,
  CreditCardIcon,
  CubeIcon,
  WrenchScrewdriverIcon,
  BuildingOffice2Icon,
  CalendarIcon,
  ChartBarIcon,
  PresentationChartLineIcon,
  Cog6ToothIcon,
} from '@heroicons/vue/24/outline'
import SidebarItem from '@/Components/SidebarItem.vue'
import { useI18n } from '@/Composables/useI18n'
import Logo from '@/assets/images/logo.png'

const { $t } = useI18n()
const $page = usePage()
</script>
