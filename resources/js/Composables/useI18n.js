import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useI18n() {
  const page = usePage()

  const locale = computed(() => page.props.locale || 'fr')
  const translations = computed(() => page.props.translations || {})

  const $t = (key, params = {}) => {
    const keys = key.split('.')
    let value = translations.value

    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k]
      } else {
        return key // Return key if translation not found
      }
    }

    if (typeof value === 'string') {
      // Replace parameters in the translation
      return value.replace(/:(\w+)/g, (match, param) => {
        return params[param] !== undefined ? params[param] : match
      })
    }

    return key
  }

  return {
    locale,
    $t,
  }
}
