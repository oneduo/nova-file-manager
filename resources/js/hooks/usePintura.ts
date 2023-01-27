import { computed } from 'vue'
import useBrowserStore from '@/stores/browser'

export function usePintura() {
  const store = useBrowserStore()

  const usePinturaEditor = computed(() => store.usePintura)
  const pinturaOptions = computed(() => store.pinturaOptions)

  return {
    usePinturaEditor,
    pinturaOptions,
  }
}
