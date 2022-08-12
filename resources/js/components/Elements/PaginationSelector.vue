<template>
  <Select :current="perPage" :on-click="setPerPage" :options="perPageOptions" />
</template>

<script setup>
import Select from '@/components/Elements/Select'

const props = defineProps(['perPage', 'perPageOptions', 'setPerPage'])
</script>
