<template>
  <AppLayout>
    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ $t('dashboard.title') }}
      </h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ $t('dashboard.subtitle') }}
      </p>
    </div>

    <!-- Metrics cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <MetricCard
        :title="$t('dashboard.metrics.revenue')"
        :value="formatCurrency(metrics.revenue)"
        :trend="metrics.revenueTrend"
        trend-type="positive"
        icon="CurrencyEuroIcon"
        color="primary"
      />

      <MetricCard
        :title="$t('dashboard.metrics.expenses')"
        :value="formatCurrency(metrics.expenses)"
        :trend="metrics.expensesTrend"
        trend-type="negative"
        icon="ArrowTrendingDownIcon"
        color="error"
      />

      <MetricCard
        :title="$t('dashboard.metrics.profit')"
        :value="formatCurrency(metrics.profit)"
        :trend="metrics.profitTrend"
        trend-type="positive"
        icon="ChartBarIcon"
        color="success"
      />

      <MetricCard
        :title="$t('dashboard.metrics.occupancy')"
        :value="`${metrics.occupancyRate}%`"
        :trend="metrics.occupancyTrend"
        trend-type="positive"
        icon="HomeIcon"
        color="warning"
      />
    </div>

    <!-- Charts and tables section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent reservations -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ $t('dashboard.recent_reservations') }}
          </h3>
        </div>
        <ReservationTable
          :reservations="recentReservations"
          :show-pagination="false"
          :max-items="5"
        />
      </div>

      <!-- Stock alerts -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ $t('dashboard.stock_alerts') }}
          </h3>
        </div>
        <div class="p-6">
          <StockAlert
            v-for="stock in lowStocks"
            :key="stock.id"
            :stock="stock"
            class="mb-4 last:mb-0"
          />
          <div v-if="lowStocks.length === 0" class="text-center py-8">
            <CheckCircleIcon class="mx-auto h-12 w-12 text-success-500" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
              {{ $t('dashboard.no_stock_alerts') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import MetricCard from '@/Components/MetricCard.vue'
import ReservationTable from '@/Components/ReservationTable.vue'
import StockAlert from '@/Components/StockAlert.vue'
import { useI18n } from '@/Composables/useI18n'

const props = defineProps({
  metrics: Object,
  recentReservations: Array,
  lowStocks: Array,
})

const { $t } = useI18n()

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
  }).format(amount)
}
</script>
