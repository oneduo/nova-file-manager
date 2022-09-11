import {computed} from 'vue'
import {useStore} from '@/store'

export function useErrors(name) {
    const store = useStore()

    const errors = computed(() => store.errors)
    const hasErrors = computed(() => errors.value?.has(name))
    const errorsList = computed(() => errors.value?.get(name))

    return {errors, hasErrors, errorsList}
}
