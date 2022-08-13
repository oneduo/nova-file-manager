import { computed } from 'vue'
import { useStore } from 'vuex'

export function useErrors(name) {
    const store = useStore()
    const errors = computed(() => store.state['nova-file-manager'].errors)
    const hasErrors = computed(() => errors.value?.has(name))
    const errorsList = computed(() => errors.value?.get(name))

    return { errors, hasErrors, errorsList }
}
