<template>
  <Select :current="perPage" :on-click="setPerPage" :options="perPageOptions" />
</template>

<script setup>
import Select from '@/components/Elements/Select'

defineProps(['perPage', 'perPageOptions', 'setPerPage'])
</script>
