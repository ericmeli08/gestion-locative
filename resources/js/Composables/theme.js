export const useThemeStore = defineStore('theme', () => {
  const colorMode = useColorMode()

  const theme = ref('light')

  const initializeTheme = () => {
    theme.value = colorMode.value
  }

  const toggleTheme = () => {
    const newTheme = theme.value === 'light' ? 'dark' : 'light'
    colorMode.preference = newTheme
    theme.value = newTheme
  }

  const setTheme = (newTheme) => {
    colorMode.preference = newTheme
    theme.value = newTheme
  }

  return {
    theme: readonly(theme),
    initializeTheme,
    toggleTheme,
    setTheme,
  }
})
