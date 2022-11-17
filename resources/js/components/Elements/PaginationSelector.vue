<template>
  <Select :current="perPage" :on-click="setPerPage" :options="perPageOptions" />
</template>

<script setup>
import Select from './Select.vue'
import { range } from 'lodash/util'

defineProps({
  perPage: {
    type: Number,
    default: 10,
  },
  perPageOptions: {
    type: Array,
    default: range(10, 50, 10),
  },
  setPerPage: {
    type: Function,
    required: true,
  },
})
</script>
