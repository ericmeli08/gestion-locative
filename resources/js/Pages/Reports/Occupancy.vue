<template>
  <AppLayout>
    <Head title="Taux d'Occupation" />

    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        <PresentationChartLineIcon class="inline h-8 w-8 mr-3 text-primary-600" />
        Taux d'Occupation
      </h1>
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Analyse de l'occupation de vos appartements
      </p>
    </div>

    <!-- Filters -->
    <div
      class="mb-6 bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
    >
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Période
          </label>
          <select v-model="selectedPeriod" @change="updateFilters" class="input-field">
            <option value="6">6 derniers mois</option>
            <option value="12">12 derniers mois</option>
            <option value="24">24 derniers mois</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Appartement
          </label>
          <select v-model="selectedApartment" @change="updateFilters" class="input-field">
            <option value="">Tous les appartements</option>
            <option v-for="apt in apartments" :key="apt.id" :value="apt.id">
              {{ apt.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Vue
          </label>
          <select v-model="viewType" @change="updateCharts" class="input-field">
            <option value="monthly">Mensuelle</option>
            <option value="yearly">Annuelle</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
      >
        <div class="flex items-center">
          <div
            class="p-3 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg"
          >
            <HomeIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Taux Moyen</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ averageOccupancy ?? 0 }}%
            </p>
          </div>
        </div>
      </div>

      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
      >
        <div class="flex items-center">
          <div
            class="p-3 rounded-xl bg-gradient-to-br from-success-500 to-success-600 text-white shadow-lg"
          >
            <CalendarDaysIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jours Occupés</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalOccupiedDays }}</p>
          </div>
        </div>
      </div>

      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
      >
        <div class="flex items-center">
          <div
            class="p-3 rounded-xl bg-gradient-to-br from-warning-500 to-warning-600 text-white shadow-lg"
          >
            <CalendarIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jours Disponibles</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalAvailableDays }}</p>
          </div>
        </div>
      </div>

      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
      >
        <div class="flex items-center">
          <div
            class="p-3 rounded-xl bg-gradient-to-br from-error-500 to-error-600 text-white shadow-lg"
          >
            <ArrowTrendingUpIcon class="h-6 w-6" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Meilleur Mois</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ bestMonth }}%</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Occupancy Trend Chart -->
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
      >
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Évolution du Taux d'Occupation
        </h3>
        <div class="h-80">
          <canvas ref="occupancyTrendChart"></canvas>
        </div>
      </div>

      <!-- Apartment Comparison Chart -->
      <div
        class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700"
      >
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Comparaison par Appartement
        </h3>
        <div class="h-80">
          <canvas ref="apartmentChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Heatmap -->
    <div
      class="bg-white dark:bg-gray-800 shadow-card rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8"
    >
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        Carte de Chaleur - Occupation Mensuelle
      </h3>
      <div class="overflow-x-auto">
        <div class="grid grid-cols-12 gap-1 min-w-max">
          <div class="text-xs font-medium text-gray-500 dark:text-gray-400 p-2"></div>
          <div
            v-for="month in months"
            :key="month"
            class="text-xs font-medium text-gray-500 dark:text-gray-400 p-2 text-center"
          >
            {{ month }}
          </div>
          <template v-for="apartment in apartments" :key="apartment.id">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 p-2">
              {{ apartment.name }}
            </div>
            <div v-for="month in months" :key="`${apartment.id}-${month}`" class="p-1">
              <div
                :class="getHeatmapColor(getOccupancyForMonth(apartment.id, month))"
                class="w-8 h-8 rounded flex items-center justify-center text-xs font-medium"
                :title="`${apartment.name} - ${month}: ${getOccupancyForMonth(apartment.id, month)}%`"
              >
                {{ getOccupancyForMonth(apartment.id, month) }}
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Detailed table -->
    <div
      class="bg-white dark:bg-gray-800 shadow-card rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Détail par Période</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Période
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Appartement
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Jours Disponibles
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Jours Occupés
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Taux d'Occupation
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                Revenus
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="data in occupancyData.slice().reverse()"
              :key="`${data.month}-${data.apartment_id}`"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
              >
                {{ formatMonth(data.month) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ data.apartment_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ data.available_days }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ data.occupied_days }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                <span :class="getOccupancyColorClass(data.occupancy_rate)">
                  {{ data.occupancy_rate }}%
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatCurrency(data.revenue) }}
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
  PresentationChartLineIcon,
  HomeIcon,
  CalendarDaysIcon,
  CalendarIcon,
  ArrowTrendingUpIcon,
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

interface OccupancyDataItem {
  month: string
  apartment_id: number
  apartment_name: string
  available_days: number
  occupied_days: number
  occupancy_rate: number
  revenue: number
}

interface Apartment {
  id: number
  name: string
}

const props = defineProps<{
  occupancyData: OccupancyDataItem[]
  apartments: Apartment[]
  selectedPeriod: string
  selectedApartment: string
}>()

const selectedPeriod = ref(props.selectedPeriod || '12')
const selectedApartment = ref(props.selectedApartment || '')
const viewType = ref('monthly')
const occupancyTrendChart = ref(null)
const apartmentChart = ref(null)

const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']

const averageOccupancy = computed(() => {
  if (props.occupancyData?.length ?? 0 === 0) return 0
  const total = props.occupancyData.reduce((sum, data) => sum + data.occupancy_rate, 0)
  return Math.round(total / props.occupancyData.length)
})

const totalOccupiedDays = computed(() => {
  return props.occupancyData?.reduce((sum, data) => sum + data.occupied_days, 0) ?? 0
})

const totalAvailableDays = computed(() => {
  return props.occupancyData?.reduce((sum, data) => sum + data.available_days, 0) ?? 0
})

const bestMonth = computed(() => {
  const data = props.occupancyData
  if (!Array.isArray(data) || data.length === 0) return 0

  return Math.max(...data.map((d) => d?.occupancy_rate ?? 0))
})

const updateFilters = () => {
  router.get(route('reports.occupancy'), {
    period: selectedPeriod.value,
    apartment: selectedApartment.value,
  })
}

const updateCharts = () => {
  // Re-initialize charts with new view type
  initCharts()
}

const getHeatmapColor = (occupancy) => {
  if (occupancy >= 90) return 'bg-success-600 text-white'
  if (occupancy >= 75) return 'bg-success-400 text-white'
  if (occupancy >= 50) return 'bg-warning-400 text-white'
  if (occupancy >= 25) return 'bg-warning-200 text-gray-800'
  return 'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300'
}

const getOccupancyForMonth = (apartmentId, month) => {
  const data = props.occupancyData.find(
    (d) => d.apartment_id === apartmentId && new Date(d.month).getMonth() === months.indexOf(month)
  )
  return data ? data.occupancy_rate : 0
}

const getOccupancyColorClass = (rate) => {
  if (rate >= 80) return 'text-success-600'
  if (rate >= 60) return 'text-warning-600'
  return 'text-error-600'
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
  }).format(amount)
}

const formatMonth = (month) => {
  return new Date(month + '-01').toLocaleDateString('fr-FR', {
    month: 'long',
    year: 'numeric',
  })
}

const initCharts = async () => {
  await nextTick()

  // Import Chart.js dynamically
  const { Chart, registerables } = await import('chart.js')
  Chart.register(...registerables)

  // Occupancy Trend Chart
  if (occupancyTrendChart.value) {
    const groupedData = props.occupancyData?.reduce((acc, data) => {
      if (!acc[data.month]) {
        acc[data.month] = { total: 0, count: 0 }
      }
      acc[data.month].total += data.occupancy_rate
      acc[data.month].count += 1
      return acc
    }, {})

    const chartData = Object.entries(groupedData??{})?.map(([month, data]) => ({
      month,
      average: data.total / data.count,
    }))

    new Chart(occupancyTrendChart.value, {
      type: 'line',
      data: {
        labels: chartData.map((d) => formatMonth(d.month)),
        datasets: [
          {
            label: "Taux d'occupation (%)",
            data: chartData.map((d) => d.average),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              callback: function (value) {
                return value + '%'
              },
            },
          },
        },
      },
    })
  }

  // Apartment Comparison Chart
  if (apartmentChart.value) {
    const apartmentData = props.apartments?.map((apartment) => {
      const apartmentOccupancy =
        props.occupancyData
          .filter((d) => d.apartment_id === apartment.id)
          .reduce((sum, d) => sum + d.occupancy_rate, 0) /
          props.occupancyData.filter((d) => d.apartment_id === apartment.id).length || 0

      return {
        name: apartment.name,
        occupancy: apartmentOccupancy,
      }
    })

    new Chart(apartmentChart.value, {
      type: 'doughnut',
      data: {
        labels: apartmentData?.map((d) => d.name),
        datasets: [
          {
            data: apartmentData?.map((d) => d.occupancy),
            backgroundColor: [
              'rgba(59, 130, 246, 0.8)',
              'rgba(34, 197, 94, 0.8)',
              'rgba(249, 115, 22, 0.8)',
              'rgba(239, 68, 68, 0.8)',
              'rgba(168, 85, 247, 0.8)',
              'rgba(236, 72, 153, 0.8)',
            ],
            borderColor: [
              'rgb(59, 130, 246)',
              'rgb(34, 197, 94)',
              'rgb(249, 115, 22)',
              'rgb(239, 68, 68)',
              'rgb(168, 85, 247)',
              'rgb(236, 72, 153)',
            ],
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return context.label + ': ' + context.parsed.toFixed(1) + '%'
              },
            },
          },
        },
      },
    })
  }
}

onMounted(() => {
  initCharts()
})
</script>
