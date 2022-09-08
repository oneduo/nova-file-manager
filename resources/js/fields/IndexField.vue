<template>
  <div :class="`text-${field.textAlign}`">
    <span class="text-90 whitespace-nowrap" v-if="!field.value || !field.value?.length"
      >&mdash;
    </span>
    <span class="text-90 whitespace-nowrap" v-else-if="field.value?.length === 1">
      {{ field.value[0].path }}
    </span>
    <span class="text-90 whitespace-nowrap" v-else>
      {{ __('NovaFileManager.totalFilesCount', { count: field.value?.length ?? 0 }) }}
    </span>
  </div>
</template>

<script setup>
defineProps({
    field: {
        type: Object,
        required: true,
    },
})
</script>
