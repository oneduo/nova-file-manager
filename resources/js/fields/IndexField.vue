<template>
  <span v-if="!field.value?.files">&mdash;</span>
  <span v-else-if="field.value?.files.length === 1">
    {{ field.value.files[0].path }}
  </span>
  <span v-else>
    {{
      __('NovaFileManager.totalFilesCount', {
        count: field.value?.files.length ?? 0,
      })
    }}
  </span>
</template>

<script setup>
defineProps({
    field: {
        type: Object,
        required: true,
    },
})
</script>
