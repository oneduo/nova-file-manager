<template>
  <div class="nova-file-manager">
    <div :class="darkMode && 'dark'">
      <Head :title="__('NovaFileManager.title')" />

      <Heading class="mb-6">{{ __('NovaFileManager.title') }}</Heading>

      <Browser />
    </div>
  </div>
</template>
<script setup>
import { useStore } from 'vuex'
import Browser from '@/components/Browser'
import { computed, onBeforeMount } from 'vue'

const store = useStore()
const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
})

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)

onBeforeMount(() => {
    store.commit('nova-file-manager/destroy')
    store.commit('nova-file-manager/setSelection', [])
    store.commit('nova-file-manager/setLimit', null)
    store.commit('nova-file-manager/init')
    store.commit('nova-file-manager/setIsFieldMode', false)
    store.commit('nova-file-manager/setMultiple', true)
    store.commit('nova-file-manager/setCustomDisk', props.config.customDisk)
    store.dispatch('nova-file-manager/setPermissions', props.config.permissions)
})
</script>
