import { useStore } from '@/store'
import { computed } from 'vue'

export function usePintura() {
  const store = useStore()

  const usePinturaEditor = computed(() => store.usePintura)
  const pinturaOptions = computed(() => store.pinturaOptions)

  return {
    usePinturaEditor,
    pinturaOptions,
  }
}
