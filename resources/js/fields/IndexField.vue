<template>
  <div :class="`text-${field.textAlign}`">
    <template v-if="usesCustomizedDisplay">
      <span v-if="fieldValue && !shouldDisplayAsHtml" class="text-90 whitespace-nowrap">
        {{ fieldValue }}
      </span>
      <div @click.stop v-else-if="fieldValue && shouldDisplayAsHtml" v-html="fieldValue" />
    </template>
    <template v-else>
      <span class="text-90 whitespace-nowrap" v-if="fieldValue && field.value.length === 1">
        {{ field.value[0].path }}
      </span>
      <span class="text-90 whitespace-nowrap" v-else-if="fieldValue && field.value.length > 1">
        {{ __('NovaFileManager.totalFilesCount', { count: field.value?.length ?? 0 }) }}
      </span>
      <p v-else>&mdash;</p>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import isNil from 'lodash/isNil'

const props = defineProps({
  field: {
    type: Object,
    required: true,
  },
})

const filled = value => !isNil(value)

const usesCustomizedDisplay = computed(
  () => props.field.usesCustomizedDisplay && filled(props.field.displayedAs)
)
const fieldHasValue = computed(() => !!props.field.value?.length)
const fieldValue = computed(() => {
  if (!usesCustomizedDisplay.value && !fieldHasValue.value) {
    return null
  }

  return usesCustomizedDisplay.value ? String(props.field.displayedAs) : props.field.value
})
const shouldDisplayAsHtml = computed(() => props.field.asHtml)
</script>
