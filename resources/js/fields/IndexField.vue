<template>
  <span>{{ path }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  field: null,
})

const path = computed(() => props.field.value?.path ?? null)
</script>
