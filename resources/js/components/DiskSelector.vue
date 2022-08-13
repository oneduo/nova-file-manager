<template>
  <Select :current="disk" :on-click="setDisk" :options="disks" />
</template>

<script setup>
import Select from '@/components/Elements/Select'

defineProps(['disk', 'disks', 'setDisk'])
</script>
