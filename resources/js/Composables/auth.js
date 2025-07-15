export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const isAuthenticated = computed(() => !!user.value)
  
  const login = async (credentials) => {
    // Simulate login
    user.value = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      avatar: 'https://images.pexels.com/photos/1040880/pexels-photo-1040880.jpeg?auto=compress&cs=tinysrgb&w=150&h=150&dpr=2'
    }
  }
  
  const logout = () => {
    user.value = null
  }
  
  return {
    user: readonly(user),
    isAuthenticated,
    login,
    logout
  }
})