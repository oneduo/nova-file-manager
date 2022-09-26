<template>
  <div class="nova-file-manager">
    <div :class="{ dark }">
      <Head :title="__('NovaFileManager.title')" />

      <Heading class="mb-6" data-tour="nfm-tool-title">{{ __('NovaFileManager.title') }}</Heading>

      <UpdateChecker v-if="config.outdated" />

      <Browser />
    </div>
  </div>
</template>
<script setup>
import { computed, onBeforeMount } from 'vue'
import Browser from '@/components/Browser'
import UpdateChecker from '@/components/Elements/UpdateChecker'
import { useStore } from '@/store'

const store = useStore()

const props = defineProps({
  config: {
    type: Object,
    required: true,
  },
})

const dark = computed(() => store.dark)

onBeforeMount(() => {
  store.prepareTool({
    singleDisk: props.config.singleDisk,
    permissions: props.config.permissions,
    tour: props.config.tour,
    usePintura: props.config.usePintura || false,
    pinturaOptions: props.config.pinturaOptions || {},
  })

  store.loadFromQueryString()
})
</script>
