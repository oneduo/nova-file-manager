<template>
  <Select :current="disk" :on-click="setDisk" :options="disks" />
</template>

<script setup>
import Select from '@/components/Elements/Select'

const props = defineProps(['disk', 'disks', 'setDisk'])
</script>
