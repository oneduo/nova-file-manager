import { computed } from 'vue'
import { useStore } from 'vuex'

export function useErrors(name) {
  const store = useStore()
  const errors = computed(() => store.state['nova-file-manager'].errors)
  const hasErrors = computed(() => errors.values?.has(name))
  const errorsList = computed(() => errors.values?.get(name))

  return { errors, hasErrors, errorsList }
}
