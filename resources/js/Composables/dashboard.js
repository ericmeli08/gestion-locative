export const useDashboardStore = defineStore('dashboard', () => {
  const metrics = ref({
    revenue: { value: 0, change: 0 },
    expenses: { value: 0, change: 0 },
    profit: { value: 0, change: 0 },
    occupancy: { value: 0, change: 0 },
  })

  const recentReservations = ref([])
  const stockAlerts = ref([])

  const loading = ref(false)

  const loadDashboardData = async () => {
    loading.value = true
    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 1000))

      metrics.value = {
        revenue: { value: 12450, change: 12.5 },
        expenses: { value: 3280, change: -5.2 },
        profit: { value: 9170, change: 18.7 },
        occupancy: { value: 85, change: 3.1 },
      }

      recentReservations.value = [
        {
          id: 1,
          client: 'Jean Dupont',
          apartment: 'Apt 1',
          checkIn: '2024-01-15',
          checkOut: '2024-01-20',
          total: 450,
          status: 'confirmed',
        },
      ]

      stockAlerts.value = [
        {
          id: 1,
          item: 'Papier toilette',
          current: 2,
          threshold: 5,
          status: 'low',
        },
      ]
    } finally {
      loading.value = false
    }
  }

  return {
    metrics: readonly(metrics),
    recentReservations: readonly(recentReservations),
    stockAlerts: readonly(stockAlerts),
    loading: readonly(loading),
    loadDashboardData,
  }
})
