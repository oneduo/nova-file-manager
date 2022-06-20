<template>
  <div class="nova-file-manager">
    <div :class="darkMode && 'dark'">
      <Head :title="__('NovaFileManager.title')" />

      <Heading class="mb-6">{{ __('NovaFileManager.title') }}</Heading>

      <Browser />
    </div>
  </div>
</template>
<script>
import Browser from '@/components/Browser'
import { mapState } from 'vuex'

export default {
  components: {
    Browser,
  },

  props: {
    config: {
      type: Object,
      required: true,
    },
  },

  computed: {
    ...mapState('nova-file-manager', ['darkMode']),
  },
}
</script>
