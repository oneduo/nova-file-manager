import { computed } from 'vue'
import useBrowserStore from '@/stores/browser'

export function useErrors(name: string) {
  const store = useBrowserStore()

  const errors = computed(() => store.errors)
  const hasErrors = computed(() => errors.value?.has(name))
  const errorsList = computed(() => errors.value?.get(name))

  return { errors, hasErrors, errorsList }
}
