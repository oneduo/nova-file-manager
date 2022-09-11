<template>
  <Select :current="disk" :on-click="setDisk" :options="disks" :is-loading="isLoading" />
</template>

<script setup>
import Select from '@/components/Elements/Select'

defineProps({
  disk: {
    type: String,
  },
  disks: {
    type: Array,
    default: [],
  },
  setDisk: {
    type: Function,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
})
</script>
