import { computed } from 'vue'
import { useStore } from '../store'

export function usePintura() {
  const store = useStore()

  const usePinturaEditor = computed(() => store.usePintura)
  const pinturaOptions = computed(() => store.pinturaOptions)

  return {
    usePinturaEditor,
    pinturaOptions,
  }
}
