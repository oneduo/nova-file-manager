<script setup lang="ts">
import range from 'lodash/range'
import Select from './Select.vue'

interface Props {
  perPage: number
  perPageOptions: number[]
  setPerPage: (perPage: number) => void
}

withDefaults(defineProps<Props>(), {
  perPage: 15,
  perPageOptions: () => range(10, 50, 10),
})
</script>

<template>
  <Select :current="perPage" :on-click="setPerPage" :options="perPageOptions" />
</template>
