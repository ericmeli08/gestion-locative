<template>
  <AppLayout>
    <Head title="Rapport Mensuel" />

    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        <ChartBarIcon class="inline h-8 w-8 mr-3 text-primary-600" />
        Rapport Mensuel
      </h1>
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Analyse financière détaillée par mois
      </p>
    </div>

    <!-- Period selector -->
    <div class="mb-6 bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Période
            </label>
            <select v-model="selectedPeriod" @change="updatePeriod" class="input-field">
              <option value="6">6 derniers mois</option>
              <option value="12">12 derniers mois</option>
              <option value="24">24 derniers mois</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Appartement
            </label>
            <select v-model="selectedApartment" @change="updatePeriod" class="input-field">
              <option value="">Tous les appartements</option>
              <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
                {{ apt.name }}
              </option>
            </select>
          </div>
        </div>
        <button @click="exportData" class="btn btn-secondary">
          <DocumentArrowDownIcon class="w-4 h-4 mr-2" />
          Exporter
        </button>
      </div>
    </div>

    <!-- Summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
          <div class="p-3 rounded-xl bg-gradient-to-br from-success-500 to-success-600 text-white shadow-lg">
            <CurrencyEuroIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Revenus Totaux</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(summary.totalRevenue) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
          <div class="p-3 rounded-xl bg-gradient-to-br from-error-500 to-error-600 text-white shadow-lg">
            <CurrencyEuroIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dépenses Totales</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(summary.totalExpenses) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
          <div class="p-3 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg">
            <ArrowTrendingUpIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bénéfice Net</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(summary.netProfit) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
          <div class="p-3 rounded-xl bg-gradient-to-br from-warning-500 to-warning-600 text-white shadow-lg">
            <ChartBarIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Marge</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ summary.margin }}%</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Revenue vs Expenses Chart -->
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Revenus vs Dépenses
        </h3>
        <div class="h-80">
          <canvas ref="revenueChart"></canvas>
        </div>
      </div>

      <!-- Profit Trend Chart -->
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Évolution du Bénéfice
        </h3>
        <div class="h-80">
          <canvas ref="profitChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Detailed table -->
    <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
          Détail par Mois
        </h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Mois
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Revenus
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Dépenses
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Charges
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Bénéfice
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Marge
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="month in monthlyData" :key="month.month" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ formatMonth(month.month) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatCurrency(month.revenue) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatCurrency(month.expenses) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatCurrency(month.charges) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" :class="month.profit >= 0 ? 'text-success-600' : 'text-error-600'">
                {{ formatCurrency(month.profit) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ month.margin }}%
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  ChartBarIcon,
  CurrencyEuroIcon,
  ArrowTrendingUpIcon,
  DocumentArrowDownIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  monthlyData: Array,
  apartments: Array,
  selectedPeriod: String,
  selectedApartment: String,
})

const selectedPeriod = ref(props.selectedPeriod || '12')
const selectedApartment = ref(props.selectedApartment || '')
const revenueChart = ref(null)
const profitChart = ref(null)

const summary = computed(() => {
  const totalRevenue = props.monthlyData?.reduce((sum, month) => sum + month.revenue, 0)
  const totalExpenses = props.monthlyData?.reduce((sum, month) => sum + month.expenses + month.charges, 0)
  const netProfit = totalRevenue - totalExpenses
  const margin = totalRevenue > 0 ? ((netProfit / totalRevenue) * 100).toFixed(1) : 0

  return {
    totalRevenue,
    totalExpenses,
    netProfit,
    margin
  }
})

const updatePeriod = () => {
  router.get(route('reports.monthly'), {
    period: selectedPeriod.value,
    apartment: selectedApartment.value
  })
}

const exportData = () => {
  // Logic to export data as CSV/Excel
  const csvContent = "data:text/csv;charset=utf-8,"
    + "Mois,Revenus,Dépenses,Charges,Bénéfice,Marge\n"
    + props.monthlyData?.map(month =>
        `${month.month},${month.revenue},${month.expenses},${month.charges},${month.profit},${month.margin}%`
      ).join("\n")

  const encodedUri = encodeURI(csvContent)
  const link = document.createElement("a")
  link.setAttribute("href", encodedUri)
  link.setAttribute("download", "rapport-mensuel.csv")
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const formatMonth = (month) => {
  return new Date(month + '-01').toLocaleDateString('fr-FR', {
    month: 'long',
    year: 'numeric'
  })
}

const initCharts = async () => {
  await nextTick()

  // Import Chart.js dynamically
  const { Chart, registerables } = await import('chart.js')
  Chart.register(...registerables)

  // Revenue vs Expenses Chart
  if (revenueChart.value) {
    new Chart(revenueChart.value, {
      type: 'line',
      data: {
        labels: props.monthlyData?.map(month => formatMonth(month.month)),
        datasets: [
          {
            label: 'Revenus',
            data: props.monthlyData?.map(month => month.revenue),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Dépenses',
            data: props.monthlyData?.map(month => month.expenses + month.charges),
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return formatCurrency(value)
              }
            }
          }
        }
      }
    })
  }

  // Profit Chart
  if (profitChart.value) {
    new Chart(profitChart.value, {
      type: 'bar',
      data: {
        labels: props.monthlyData?.map(month => formatMonth(month.month)),
        datasets: [
          {
            label: 'Bénéfice',
            data: props.monthlyData?.map(month => month.profit),
            backgroundColor: props.monthlyData?.map(month =>
              month.profit >= 0 ? 'rgba(59, 130, 246, 0.8)' : 'rgba(239, 68, 68, 0.8)'
            ),
            borderColor: props.monthlyData?.map(month =>
              month.profit >= 0 ? 'rgb(59, 130, 246)' : 'rgb(239, 68, 68)'
            ),
            borderWidth: 1
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          }
        },
        scales: {
          y: {
            ticks: {
              callback: function(value) {
                return formatCurrency(value)
              }
            }
          }
        }
      }
    })
  }
}

onMounted(() => {
  initCharts()
})
</script>
