import { computed } from 'vue'
import { useStore } from 'vuex'

export function useNovaFileManagerStore() {
  const store = useStore()

  const storeComputed = (name) => {
    return computed(() => store.state['nova-file-manager'][name])
  }

  return { storeComputed }
}
