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
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center space-x-4 flex-wrap gap-4">
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
            <button
              v-if="selectedPeriod === '1' && !isDaily"
              @click="showDailyDetails"
              class="btn btn-primary"
            >
              <CalendarDaysIcon class="w-4 h-4 mr-2" />
              Voir par jour
            </button>

            <!-- Bouton pour revenir à la vue mensuelle -->
            <button
              v-if="isDaily"
              @click="backToMonthly"
              class="btn btn-secondary"
            >
              <ArrowLeftIcon class="w-4 h-4 mr-2" />
              Retour mensuel
            </button>

            <button @click="handleExport" class="btn btn-secondary">
              <DocumentArrowDownIcon class="w-4 h-4 mr-2" />
              Exporter Excel
            </button>
          </div>
        </div>
      </div>

      <!-- Bandeau info export multi-appartements -->
      <div
        v-if="!selectedApartment && !isDaily"
        class="mb-6 flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl px-5 py-4"
      >
        <InformationCircleIcon class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" />
        <p class="text-sm text-blue-700 dark:text-blue-300">
          <span class="font-semibold">Export multi-appartements actif.</span>
          Le fichier Excel contiendra un récapitulatif par appartement pour chacun des 3 derniers mois,
          ainsi qu'un tableau d'évolution comparatif inter-mois.
        </p>
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
                  <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">vs période préc.</span>
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
                <p
                  class="text-xl lg:text-2xl font-bold break-words leading-tight"
                  :class="summary.netProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400'"
                >
                  {{ formatCurrencyCompact(summary.netProfit) }}
                </p>
                <div class="flex items-center">
                  <div
                    class="flex items-center px-2 py-1 rounded-full text-xs font-medium"
                    :class="summary.netProfit >= 0
                      ? 'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300'
                      : 'bg-error-100 text-error-800 dark:bg-error-900/30 dark:text-error-300'"
                  >
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
              <ArrowTrendingUpIcon class="h-4 w-4 mr-1" />
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
                <th
                  v-if="!isDaily"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
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
              <tr
                v-for="item in tableData"
                :key="isDaily ? item.day : item.month"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  {{ isDaily ? item.day_name : formatMonth(item.month) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <div class="flex items-center">
                    <span>{{ formatCurrency(item.revenue) }}</span>
                    <ArrowTrendingUpIcon v-if="item.revenue > 0" class="ml-2 h-3 w-3 text-success-500" />
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ formatCurrency(item.expenses) }}
                </td>
                <td
                  v-if="!isDaily"
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                >
                  {{ formatCurrency(item.charges) }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-semibold"
                  :class="item.profit >= 0 ? 'text-success-600' : 'text-error-600'"
                >
                  <div class="flex items-center">
                    <ArrowTrendingUpIcon v-if="item.profit > 0" class="mr-1 h-4 w-4" />
                    <ArrowTrendingDownIcon v-else-if="item.profit < 0" class="mr-1 h-4 w-4" />
                    {{ formatCurrency(item.profit) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <span
                    class="px-2 py-1 text-xs font-medium rounded-full"
                    :class="item.margin > 20
                      ? 'bg-success-100 text-success-800'
                      : item.margin > 10
                        ? 'bg-warning-100 text-warning-800'
                        : 'bg-error-100 text-error-800'"
                  >
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
  import { router }  from '@inertiajs/vue3'
  import { Head }    from '@inertiajs/vue3'
  import {
    ChartBarIcon,
    CurrencyEuroIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    DocumentArrowDownIcon,
    CalendarDaysIcon,
    ArrowLeftIcon,
    InformationCircleIcon,
  } from '@heroicons/vue/24/outline'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { exportData } from '@/utils/exportData.js'

  // ─── Props ────────────────────────────────────────────────────────────────────
  const props = defineProps({
    monthlyData:       { type: Array,   default: () => [] },
    dailyData:         { type: Array,   default: () => [] },
    apartments:        { type: Array,   default: () => [] },
    selectedPeriod:    { type: [String, Number], default: '12' },
    selectedApartment: { type: [String, Number, null], default: null },
    selectedMonth:     { type: [String, null], default: null },
    showDaily:         { type: Boolean, default: false },
    isDaily:           { type: Boolean, default: false },
    availableMonths:   { type: Array,   default: () => [] },
    monthName:         { type: [String, null], default: null },
  })

  // ─── State réactif ────────────────────────────────────────────────────────────
  const selectedPeriod    = ref(String(props.selectedPeriod    || '12'))
  const selectedApartment = ref(String(props.selectedApartment || ''))
  const selectedMonth     = ref(props.selectedMonth            || '')
  const revenueChart      = ref(null)
  const profitChart       = ref(null)

  // ─── Données affichées ────────────────────────────────────────────────────────
  /**
   * En mode multi-appartements (pas de filtre appart), monthlyData contient
   * des entrées avec apartment_id. Pour le tableau et les graphiques, on agrège
   * par mois pour éviter les doublons de lignes dans la vue.
   */
  const displayData = computed(() => {
    if (props.isDaily) return props.dailyData || []
    return props.monthlyData || []
  })

  /**
   * Données agrégées par mois pour les graphiques et le tableau résumé.
   * Si les données sont déjà par mois sans apartment_id (mode filtre appart),
   * on les retourne telles quelles.
   * Sinon on somme par mois.
   */
  const aggregatedByMonth = computed(() => {
    if (props.isDaily) return props.dailyData || []

    const data = props.monthlyData || []
    if (!data.length) return []

    // Vérifier si les données sont déjà agrégées (pas de apartment_id) ou pas
    const hasApartmentId = data.some(d => d.apartment_id != null)
    if (!hasApartmentId) return data

    // Agréger par mois
    const map = new Map()
    for (const row of data) {
      const existing = map.get(row.month)
      if (!existing) {
        map.set(row.month, {
          month:    row.month,
          revenue:  parseFloat(row.revenue)  || 0,
          expenses: parseFloat(row.expenses) || 0,
          charges:  parseFloat(row.charges)  || 0,
          profit:   parseFloat(row.profit)   || 0,
        })
      } else {
        existing.revenue  += parseFloat(row.revenue)  || 0
        existing.expenses += parseFloat(row.expenses) || 0
        existing.charges  += parseFloat(row.charges)  || 0
        existing.profit   += parseFloat(row.profit)   || 0
      }
    }

    return [...map.values()]
      .sort((a, b) => a.month.localeCompare(b.month))
      .map(row => ({
        ...row,
        margin: row.revenue > 0
          ? parseFloat(((row.profit / row.revenue) * 100).toFixed(1))
          : 0,
      }))
  })

  // Données pour le tableau HTML (inversées = plus récent en premier)
  const tableData = computed(() => aggregatedByMonth.value.slice().reverse())

  // ─── Summary cards ────────────────────────────────────────────────────────────
  const summary = computed(() => {
    const data        = aggregatedByMonth.value
    const totalRevenue  = data.reduce((s, i) => s + (parseFloat(i.revenue)  || 0), 0)
    const totalExpenses = data.reduce((s, i) => s + (parseFloat(i.expenses) || 0) + (parseFloat(i.charges) || 0), 0)
    const netProfit   = totalRevenue - totalExpenses
    const margin      = totalRevenue > 0 ? parseFloat(((netProfit / totalRevenue) * 100).toFixed(1)) : 0

    return { totalRevenue, totalExpenses, netProfit, margin }
  })

  const revenuesTrend = computed(() => {
    const data = aggregatedByMonth.value
    if (data.length < 2) return 0
    const last     = data[data.length - 1]?.revenue || 0
    const previous = data[data.length - 2]?.revenue || 0
    if (previous === 0) return 0
    return ((last - previous) / previous) * 100
  })

  // ─── Navigation ───────────────────────────────────────────────────────────────
  const updatePeriod = () => {
    const params: Record<string, string | number> = {
      period:    selectedPeriod.value,
      apartment: selectedApartment.value,
    }
    if (selectedPeriod.value === '1' && selectedMonth.value) {
      params.selected_month = selectedMonth.value
    }
    router.get(route('reports.monthly'), params)
  }

  const showDailyDetails = () => {
    router.get(route('reports.monthly'), {
      period:         '1',
      apartment:      selectedApartment.value,
      selected_month: selectedMonth.value || new Date().toISOString().slice(0, 7),
      show_daily:     true,
    })
  }

  const backToMonthly = () => {
    router.get(route('reports.monthly'), {
      period:         '1',
      apartment:      selectedApartment.value,
      selected_month: selectedMonth.value,
    })
  }

  // ─── Export ───────────────────────────────────────────────────────────────────
  const handleExport = () => {
    exportData({
      monthlyData:  props.monthlyData  || [],
      apartments:   props.apartments   || [],
      isDaily:      props.isDaily      || false,
      dailyData:    props.dailyData    || [],
      monthName:    props.monthName    || '',
      apartmentId:  props.selectedApartment || null,
    })
  }

  // ─── Formateurs ───────────────────────────────────────────────────────────────
  const formatCurrency = (amount: number) =>
    new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(amount)

  const formatCurrencyCompact = (amount: number) => {
    const abs = Math.abs(amount)
    if (abs >= 1_000_000_000) return (amount / 1_000_000_000).toFixed(1) + ' Md F CFA'
    if (abs >= 1_000_000)     return (amount / 1_000_000).toFixed(1)     + ' M F CFA'
    if (abs >= 1_000)         return (amount / 1_000).toFixed(0)         + ' k F CFA'
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency', currency: 'XOF',
      minimumFractionDigits: 0, maximumFractionDigits: 0,
    }).format(amount)
  }

  const formatMonth = (month: string) =>
    new Date(month + '-01').toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })

  const getExpensePercentage = (expenses: number, revenue: number) =>
    revenue === 0 ? 0 : Math.round((expenses / revenue) * 100)

  const getMarginColorClass = (margin: number) => {
    if (margin >= 30) return 'bg-success-500'
    if (margin >= 15) return 'bg-warning-500'
    if (margin >= 0)  return 'bg-orange-500'
    return 'bg-error-500'
  }

  // ─── Graphiques Chart.js ──────────────────────────────────────────────────────
  let chartInstances: any[] = []

  const destroyCharts = () => {
    chartInstances.forEach(c => c?.destroy())
    chartInstances = []
  }

  const initCharts = async () => {
    await nextTick()
    destroyCharts()

    const { Chart, registerables } = await import('chart.js')
    Chart.register(...registerables)

    const data        = aggregatedByMonth.value
    const isDailyView = props.isDaily

    const commonOptions = {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { intersect: false, mode: 'index' as const },
      plugins: {
        legend: { position: 'top' as const, labels: { usePointStyle: true, padding: 20 } },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          titleColor: '#fff',
          bodyColor:  '#fff',
          cornerRadius: 8,
          displayColors: true,
        },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { maxTicksLimit: isDailyView ? 15 : 12 },
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.1)' },
          ticks: { callback: (value: number) => formatCurrencyCompact(value) },
        },
      },
    }

    const labels = data.map(item =>
      isDailyView ? (item as any).day_name : formatMonth(item.month)
    )

    // Graphique Revenus
    if (revenueChart.value) {
      const instance = new Chart(revenueChart.value, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'Revenus',
              data: data.map(i => i.revenue),
              borderColor: '#22C55E',
              backgroundColor: 'rgba(34,197,94,0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#22C55E',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointRadius: 5,
              pointHoverRadius: 8,
            },
            {
              label: 'Dépenses + Charges',
              data: data.map(i => (i.expenses || 0) + (i.charges || 0)),
              borderColor: '#EF4444',
              backgroundColor: 'rgba(239,68,68,0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#EF4444',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointRadius: 4,
              pointHoverRadius: 6,
            },
          ],
        },
        options: {
          ...commonOptions,
          plugins: {
            ...commonOptions.plugins,
            title: {
              display: true,
              text: isDailyView
                ? 'Évolution journalière des revenus et dépenses'
                : 'Évolution mensuelle des revenus et dépenses',
              font: { size: 14, weight: 'bold' as const },
            },
          },
        },
      })
      chartInstances.push(instance)
    }

    // Graphique Bénéfices
    if (profitChart.value) {
      const instance = new Chart(profitChart.value, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'Bénéfice',
              data: data.map(i => i.profit),
              backgroundColor: data.map(i =>
                i.profit >= 0 ? 'rgba(59,130,246,0.8)' : 'rgba(239,68,68,0.8)'
              ),
              borderColor: data.map(i =>
                i.profit >= 0 ? '#3B82F6' : '#EF4444'
              ),
              borderWidth: 1,
              borderRadius: 4,
              borderSkipped: false,
            },
          ],
        },
        options: {
          ...commonOptions,
          plugins: {
            ...commonOptions.plugins,
            title: {
              display: true,
              text: isDailyView ? 'Bénéfices par jour' : 'Bénéfices par mois',
              font: { size: 14, weight: 'bold' as const },
            },
          },
        },
      })
      chartInstances.push(instance)
    }
  }

  // ─── Lifecycle ────────────────────────────────────────────────────────────────
  onMounted(() => initCharts())

  watch(
    () => props.isDaily,
    () => nextTick(() => initCharts()),
    { immediate: false }
  )

  watch(
    () => displayData.value,
    () => nextTick(() => initCharts()),
    { deep: true }
  )
  </script>
