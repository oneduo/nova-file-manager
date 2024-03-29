import { storeToRefs } from 'pinia'
import { computed } from 'vue'
import useBrowserStore from '@/stores/browser'

export function useErrors(attribute: string) {
  const store = useBrowserStore()

  const { error } = storeToRefs(store)

  return {
    message: computed(() => error?.value?.bag?.message),
    invalid: computed(() => error?.value?.attribute === attribute),
    errors: computed(() => error?.value?.bag?.errors),
  }
}
