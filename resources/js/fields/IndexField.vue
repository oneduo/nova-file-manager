<template>
  <span v-if="field.value?.files.length === 1">
    {{ field.value.files[0].path }}
  </span>
  <span v-else>
    {{
      __('NovaFileManager.totalFilesCount', {
        count: field.value?.files.length,
      })
    }}
  </span>
</template>

<script setup>
defineProps({
    field: null,
})
</script>
