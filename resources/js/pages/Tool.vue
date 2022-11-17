<script setup lang="ts">
import { computed, onBeforeMount } from 'vue'
import Browser from '../components/Browser.vue'
import UpdateChecker from '../components/Elements/UpdateChecker.vue'
import { useStore } from '../store/index'

const store = useStore()

interface Props {
  foo: string
  bar?: number
}

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

