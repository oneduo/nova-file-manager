<script setup lang="ts">
import Select from '@/components/Elements/Select.vue';

interface Props {
  disk?: string;
  disks: string[];
  setDisk: (disk: string) => void;
  isLoading: boolean;
}

withDefaults(defineProps<Props>(), {
  isLoading: false,
  disks: () => [],
});
</script>

<template>
  <Select :current="disk" :on-click="setDisk" :options="disks" :is-loading="isLoading" />
</template>
