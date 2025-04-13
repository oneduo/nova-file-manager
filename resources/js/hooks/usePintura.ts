import useBrowserStore from '@/stores/browser';
import { computed } from 'vue';

export function usePintura() {
  const store = useBrowserStore();

  const usePinturaEditor = computed(() => store.usePintura);
  const pinturaOptions = computed(() => store.pinturaOptions);

  return {
    usePinturaEditor,
    pinturaOptions,
  };
}
