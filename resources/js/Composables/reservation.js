export const useReservationStore = defineStore('reservation', () => {
  const reservations = ref([])
  const loading = ref(false)
  const filters = ref({})

  const pagination = ref({
    currentPage: 1,
    perPage: 10,
    total: 0,
    totalPages: 0,
  })

  const loadReservations = async () => {
    loading.value = true
    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 800))

      reservations.value = [
        {
          id: 1,
          client: 'Jean Dupont',
          email: 'jean@example.com',
          apartment: 'Appartement 1',
          checkIn: '2024-01-15',
          checkOut: '2024-01-20',
          platform: 'Airbnb',
          total: 450,
          paymentStatus: 'paid',
          status: 'confirmed',
        },
        {
          id: 2,
          client: 'Marie Martin',
          email: 'marie@example.com',
          apartment: 'Appartement 2',
          checkIn: '2024-01-22',
          checkOut: '2024-01-25',
          platform: 'Booking.com',
          total: 320,
          paymentStatus: 'unpaid',
          status: 'pending',
        },
      ]

      pagination.value = {
        currentPage: 1,
        perPage: 10,
        total: reservations.value.length,
        totalPages: Math.ceil(reservations.value.length / 10),
      }
    } finally {
      loading.value = false
    }
  }

  const getFilteredReservations = (filterParams) => {
    let filtered = [...reservations.value]

    if (filterParams.status) {
      filtered = filtered.filter((r) => r.status === filterParams.status)
    }

    if (filterParams.paymentStatus) {
      filtered = filtered.filter((r) => r.paymentStatus === filterParams.paymentStatus)
    }

    if (filterParams.apartment) {
      filtered = filtered.filter((r) =>
        r.apartment.toLowerCase().includes(filterParams.apartment.toLowerCase())
      )
    }

    if (filterParams.platform) {
      filtered = filtered.filter((r) => r.platform === filterParams.platform)
    }

    return filtered
  }

  const createReservation = async (formData) => {
    loading.value = true
    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 1000))

      const newReservation = {
        id: Date.now(),
        ...formData,
        status: 'pending',
        paymentStatus: 'unpaid',
      }

      reservations.value.unshift(newReservation)
    } finally {
      loading.value = false
    }
  }

  const deleteReservation = async (id) => {
    loading.value = true
    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 500))

      const index = reservations.value.findIndex((r) => r.id === id)
      if (index > -1) {
        reservations.value.splice(index, 1)
      }
    } finally {
      loading.value = false
    }
  }

  const applyFilters = (filterParams) => {
    filters.value = { ...filterParams }
  }

  const setPage = (page) => {
    pagination.value.currentPage = page
  }

  return {
    reservations: readonly(reservations),
    loading: readonly(loading),
    pagination: readonly(pagination),
    loadReservations,
    getFilteredReservations,
    createReservation,
    deleteReservation,
    applyFilters,
    setPage,
  }
})
