<template>
  <AppLayout>
    <Head :title="isDaily ? `Rapport Journalier - ${monthName}` : 'Rapport Mensuel'" />

    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        <ChartBarIcon class="inline h-8 w-8 mr-3 text-primary-600" />
        {{ isDaily ? `Rapport Journalier - ${monthName}` : 'Rapport Mensuel' }}
      </h1>
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        {{ isDaily ? 'Analyse financière détaillée par jour' : 'Analyse financière détaillée par mois' }}
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
              <option value="1">1 dernier mois</option>
              <option value="3">3 derniers mois</option>
              <option value="6">6 derniers mois</option>
              <option value="12">12 derniers mois</option>
              <option value="24">24 derniers mois</option>
            </select>
          </div>

          <!-- Sélecteur de mois spécifique quand période = 1 -->
          <div v-if="selectedPeriod === '1' && !isDaily">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Mois spécifique
            </label>
            <select v-model="selectedMonth" @change="updatePeriod" class="input-field">
              <option value="">Mois actuel</option>
              <option v-for="month in availableMonths" :key="month.value" :value="month.value">
                {{ month.label_fr }}
              </option>
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
        <div class="flex space-x-2">
          <!-- Bouton pour voir les détails journaliers -->
          <button v-if="selectedPeriod === '1' && !isDaily"
                  @click="showDailyDetails"
                  class="btn btn-primary">
            <CalendarDaysIcon class="w-4 h-4 mr-2" />
            Voir par jour
          </button>

          <!-- Bouton pour revenir à la vue mensuelle -->
          <button v-if="isDaily"
                  @click="backToMonthly"
                  class="btn btn-secondary">
            <ArrowLeftIcon class="w-4 h-4 mr-2" />
            Retour mensuel
          </button>

          <button @click="exportData" class="btn btn-secondary">
            <DocumentArrowDownIcon class="w-4 h-4 mr-2" />
            Exporter
          </button>
        </div>
      </div>
    </div>

    <!-- Summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <!-- Revenus Totaux -->
  <div class="bg-white dark:bg-gray-800 shadow-card hover:shadow-card-hover rounded-xl p-6 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:scale-105">
    <div class="flex items-start justify-between">
      <div class="flex-1 min-w-0">
        <div class="flex items-center mb-3">
          <div class="p-3 rounded-xl bg-gradient-to-br from-success-500 to-success-600 text-white shadow-lg">
            <CurrencyEuroIcon class="h-6 w-6" />
          </div>
          <div class="ml-3 flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
              Revenus Totaux
            </p>
          </div>
        </div>
        
        <div class="space-y-2">
          <p class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white break-words leading-tight">
            {{ formatCurrencyCompact(summary.totalRevenue) }}
          </p>
          
          <div class="flex items-center">
            <ArrowTrendingUpIcon v-if="revenuesTrend > 0" class="h-4 w-4 text-success-500 mr-1 flex-shrink-0" />
            <ArrowTrendingDownIcon v-else-if="revenuesTrend < 0" class="h-4 w-4 text-error-500 mr-1 flex-shrink-0" />
            <span 
              :class="revenuesTrend > 0 ? 'text-success-600' : revenuesTrend < 0 ? 'text-error-600' : 'text-gray-500'"
              class="text-xs font-medium"
            >
              {{ Math.abs(revenuesTrend).toFixed(1) }}%
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">vs mois dernier</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Dépenses Totales -->
  <div class="bg-white dark:bg-gray-800 shadow-card hover:shadow-card-hover rounded-xl p-6 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:scale-105">
    <div class="flex items-start justify-between">
      <div class="flex-1 min-w-0">
        <div class="flex items-center mb-3">
          <div class="p-3 rounded-xl bg-gradient-to-br from-error-500 to-error-600 text-white shadow-lg">
            <CurrencyEuroIcon class="h-6 w-6" />
          </div>
          <div class="ml-3 flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
              Dépenses Totales
            </p>
          </div>
        </div>
        
        <div class="space-y-2">
          <p class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white break-words leading-tight">
            {{ formatCurrencyCompact(summary.totalExpenses) }}
          </p>
          
          <div class="flex items-center">
            <span class="text-xs text-gray-500 dark:text-gray-400">
              {{ getExpensePercentage(summary.totalExpenses, summary.totalRevenue) }}% des revenus
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bénéfice Net -->
  <div class="bg-white dark:bg-gray-800 shadow-card hover:shadow-card-hover rounded-xl p-6 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:scale-105">
    <div class="flex items-start justify-between">
      <div class="flex-1 min-w-0">
        <div class="flex items-center mb-3">
          <div class="p-3 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg">
            <ArrowTrendingUpIcon class="h-6 w-6" />
          </div>
          <div class="ml-3 flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
              Bénéfice Net
            </p>
          </div>
        </div>
        
        <div class="space-y-2">
          <p class="text-xl lg:text-2xl font-bold break-words leading-tight"
             :class="summary.netProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400'">
            {{ formatCurrencyCompact(summary.netProfit) }}
          </p>
          
          <div class="flex items-center">
            <div class="flex items-center px-2 py-1 rounded-full text-xs font-medium"
                 :class="summary.netProfit >= 0 ? 'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300' : 'bg-error-100 text-error-800 dark:bg-error-900/30 dark:text-error-300'">
              <ArrowTrendingUpIcon v-if="summary.netProfit >= 0" class="h-3 w-3 mr-1" />
              <ArrowTrendingDownIcon v-else class="h-3 w-3 mr-1" />
              {{ summary.netProfit >= 0 ? 'Positif' : 'Négatif' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Marge -->
  <div class="bg-white dark:bg-gray-800 shadow-card hover:shadow-card-hover rounded-xl p-6 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:scale-105">
    <div class="flex items-start justify-between">
      <div class="flex-1 min-w-0">
        <div class="flex items-center mb-3">
          <div class="p-3 rounded-xl bg-gradient-to-br from-warning-500 to-warning-600 text-white shadow-lg">
            <ChartBarIcon class="h-6 w-6" />
          </div>
          <div class="ml-3 flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
              Marge Bénéficiaire
            </p>
          </div>
        </div>
        
        <div class="space-y-2">
          <p class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white break-words leading-tight">
            {{ summary.margin }}%
          </p>
          
          <div class="flex items-center">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div 
                class="h-2 rounded-full transition-all duration-500"
                :class="getMarginColorClass(summary.margin)"
                :style="{ width: Math.min(Math.abs(summary.margin), 100) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Revenue Trend Chart -->
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Tendance des Revenus
          </h3>
          <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <TrendingUpIcon class="h-4 w-4 mr-1" />
            {{ isDaily ? 'Evolution journalière' : 'Evolution mensuelle' }}
          </div>
        </div>
        <div class="h-80">
          <canvas ref="revenueChart"></canvas>
        </div>
      </div>

      <!-- Profit Trend Chart -->
      <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Évolution du Bénéfice
          </h3>
          <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <ChartBarIcon class="h-4 w-4 mr-1" />
            Comparaison période
          </div>
        </div>
        <div class="h-80">
          <canvas ref="profitChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Detailed table -->
    <div class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
          {{ isDaily ? 'Détail par Jour' : 'Détail par Mois' }}
        </h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ isDaily ? 'Jour' : 'Mois' }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Revenus
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Dépenses
              </th>
              <th v-if="!isDaily" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
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
            <tr v-for="item in displayData.slice().reverse()" :key="isDaily ? item.day : item.month"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ isDaily ? item.day_name : formatMonth(item.month) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <div class="flex items-center">
                  <span>{{ formatCurrency(item.revenue) }}</span>
                  <TrendingUpIcon v-if="item.revenue > 0" class="ml-2 h-3 w-3 text-success-500" />
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatCurrency(item.expenses) }}
              </td>
              <td v-if="!isDaily" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatCurrency(item.charges) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold"
                  :class="item.profit >= 0 ? 'text-success-600' : 'text-error-600'">
                <div class="flex items-center">
                  <ArrowTrendingUpIcon v-if="item.profit > 0" class="mr-1 h-4 w-4" />
                  <ArrowTrendingDownIcon v-else-if="item.profit < 0" class="mr-1 h-4 w-4" />
                  {{ formatCurrency(item.profit) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span class="px-2 py-1 text-xs font-medium rounded-full"
                      :class="item.margin > 20 ? 'bg-success-100 text-success-800' :
                             item.margin > 10 ? 'bg-warning-100 text-warning-800' :
                             'bg-error-100 text-error-800'">
                  {{ item.margin }}%
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  ChartBarIcon,
  CurrencyEuroIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  DocumentArrowDownIcon,
  CalendarDaysIcon,
  ArrowLeftIcon,
//   TrendingUpIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  monthlyData: Array,
  dailyData: Array,
  apartments: Array,
  selectedPeriod: String,
  selectedApartment: String,
  selectedMonth: String,
  showDaily: Boolean,
  isDaily: Boolean,
  availableMonths: Array,
  monthName: String,
})

const selectedPeriod = ref(props.selectedPeriod || '12')
const selectedApartment = ref(props.selectedApartment || '')
const selectedMonth = ref(props.selectedMonth || '')
const revenueChart = ref(null)
const profitChart = ref(null)

const displayData = computed(() => {
  return props.isDaily ? props.dailyData : props.monthlyData
})

const summary = computed(() => {
  const data = displayData.value || []
  const totalRevenue = data.reduce((sum, item) => sum + item.revenue, 0)
  const totalExpenses = data.reduce((sum, item) => sum + item.expenses + (item.charges || 0), 0)
  const netProfit = totalRevenue - totalExpenses
  const margin = totalRevenue > 0 ? ((netProfit / totalRevenue) * 100).toFixed(1) : 0

  return {
    totalRevenue,
    totalExpenses,
    netProfit,
    margin
  }
})

const revenuesTrend = computed(() => {
  const data = displayData.value || []
  if (data.length < 2) return 0

  const lastRevenue = data[data.length - 1]?.revenue || 0
  const previousRevenue = data[data.length - 2]?.revenue || 0

  if (previousRevenue === 0) return 0
  return ((lastRevenue - previousRevenue) / previousRevenue) * 100
})

const updatePeriod = () => {
  const params = {
    period: selectedPeriod.value,
    apartment: selectedApartment.value
  }

  if (selectedPeriod.value === '1' && selectedMonth.value) {
    params.selected_month = selectedMonth.value
  }

  router.get(route('reports.monthly'), params)
}

const showDailyDetails = () => {
  router.get(route('reports.monthly'), {
    period: '1',
    apartment: selectedApartment.value,
    selected_month: selectedMonth.value || new Date().toISOString().slice(0, 7),
    show_daily: true
  })
}

const backToMonthly = () => {
  router.get(route('reports.monthly'), {
    period: '1',
    apartment: selectedApartment.value,
    selected_month: selectedMonth.value
  })
}

import * as XLSX from 'xlsx'

const exportData = () => {
  const data = displayData.value || []
  const isDaily = props.isDaily

  // Créer un nouveau classeur Excel
  const workbook = XLSX.utils.book_new()

  // Préparer les données avec en-têtes
  const worksheetData = [
    isDaily
      ? ['Jour', 'Revenus (F CFA)', 'Dépenses (F CFA)', 'Bénéfice (F CFA)', 'Marge (%)']
      : ['Mois', 'Revenus (F CFA)', 'Dépenses (F CFA)', 'Charges (F CFA)', 'Bénéfice (F CFA)', 'Marge (%)']
  ]

  // Ajouter les données
  data.forEach(item => {
    if (isDaily) {
      worksheetData.push([
        item.day_name,
        parseFloat(item.revenue) || 0,
        parseFloat(item.expenses) || 0,
        parseFloat(item.profit) || 0,
        parseFloat(item.margin) || 0
      ])
    } else {
      worksheetData.push([
        item.month,
        parseFloat(item.revenue) || 0,
        parseFloat(item.expenses) || 0,
        parseFloat(item.charges) || 0,
        parseFloat(item.profit) || 0,
        parseFloat(item.margin) || 0
      ])
    }
  })

  const worksheet = XLSX.utils.aoa_to_sheet(worksheetData)
  XLSX.utils.book_append_sheet(workbook, worksheet, isDaily ? 'Rapport Journalier' : 'Rapport Mensuel')

  const fileName = isDaily
    ? `rapport-journalier-${props.monthName?.replace(' ', '-')}.xlsx`
    : `rapport-mensuel-${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}.xlsx`

  XLSX.writeFile(workbook, fileName)
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF'
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

  const { Chart, registerables } = await import('chart.js')
  Chart.register(...registerables)

  const data = displayData.value || []
  const isDailyView = props.isDaily

  // Configuration commune pour les graphiques
  const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
      intersect: false,
      mode: 'index',
    },
    plugins: {
      legend: {
        position: 'top',
        labels: {
          usePointStyle: true,
          padding: 20
        }
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        titleColor: '#fff',
        bodyColor: '#fff',
        cornerRadius: 8,
        displayColors: true
      }
    },
    scales: {
      x: {
        grid: {
          display: false
        },
        ticks: {
          maxTicksLimit: isDailyView ? 15 : 12
        }
      },
      y: {
        beginAtZero: true,
        grid: {
          color: 'rgba(0, 0, 0, 0.1)'
        },
        ticks: {
          callback: function(value) {
            return formatCurrency(value)
          }
        }
      }
    }
  }

  // Graphique des revenus (courbe qui suit les variations)
  if (revenueChart.value) {
    new Chart(revenueChart.value, {
      type: 'line',
      data: {
        labels: data.map(item =>
          isDailyView ? item.day_name : formatMonth(item.month)
        ),
        datasets: [
          {
            label: 'Revenus',
            data: data.map(item => item.revenue),
            borderColor: '#22C55E',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#22C55E',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 8
          },
          {
            label: 'Dépenses',
            data: data.map(item => item.expenses + (item.charges || 0)),
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#EF4444',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          }
        ]
      },
      options: {
        ...commonOptions,
        plugins: {
          ...commonOptions.plugins,
          title: {
            display: true,
            text: isDailyView ? 'Évolution journalière des revenus et dépenses' : 'Évolution mensuelle des revenus et dépenses',
            font: {
              size: 14,
              weight: 'bold'
            }
          }
        }
      }
    })
  }

  // Graphique des profits (barres avec couleurs conditionnelles)
  if (profitChart.value) {
    new Chart(profitChart.value, {
      type: 'bar',
      data: {
        labels: data.map(item =>
          isDailyView ? item.day_name : formatMonth(item.month)
        ),
        datasets: [
          {
            label: 'Bénéfice',
            data: data.map(item => item.profit),
            backgroundColor: data.map(item =>
              item.profit >= 0 ? 'rgba(59, 130, 246, 0.8)' : 'rgba(239, 68, 68, 0.8)'
            ),
            borderColor: data.map(item =>
              item.profit >= 0 ? '#3B82F6' : '#EF4444'
            ),
            borderWidth: 1,
            borderRadius: 4,
            borderSkipped: false,
          }
        ]
      },
      options: {
        ...commonOptions,
        plugins: {
          ...commonOptions.plugins,
          title: {
            display: true,
            text: isDailyView ? 'Bénéfices par jour' : 'Bénéfices par mois',
            font: {
              size: 14,
              weight: 'bold'
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

// Réinitialiser les graphiques quand les données changent
watch(() => props.isDaily, () => {
  nextTick(() => {
    initCharts()
  })
}, { immediate: false })

watch(() => displayData.value, () => {
  nextTick(() => {
    initCharts()
  })
}, { deep: true })

// Fonctions utilitaires à ajouter dans votre composant
const formatCurrencyCompact = (amount) => {
  const absAmount = Math.abs(amount)
  
  if (absAmount >= 1000000000) {
    return (amount / 1000000000).toFixed(1) + 'Md F CFA'
  } else if (absAmount >= 1000000) {
    return (amount / 1000000).toFixed(1) + 'M F CFA'
  } else if (absAmount >= 1000) {
    return (amount / 1000).toFixed(0) + 'k F CFA'
  } else {
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'XOF',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount)
  }
}

const getExpensePercentage = (expenses, revenue) => {
  if (revenue === 0) return 0
  return Math.round((expenses / revenue) * 100)
}

const getMarginColorClass = (margin) => {
  if (margin >= 30) return 'bg-success-500'
  if (margin >= 15) return 'bg-warning-500'
  if (margin >= 0) return 'bg-orange-500'
  return 'bg-error-500'
}
</script>


