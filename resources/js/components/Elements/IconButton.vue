<script setup lang="ts">
import { computed } from 'vue'

const variants = {
  primary: 'bg-blue-500 text-white focus:outline-blue-500',
  secondary: 'bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-blue-500',
  danger: 'bg-red-500 text-white focus:outline-red-500',
  success: 'bg-green-500 text-white focus:outline-green-500',
  transparent: 'bg-transparent text-gray-800 dark:text-gray-100',
}

interface Props {
  variant: keyof typeof variants
  type: 'button' | 'submit' | 'reset'
  asAnchor?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'secondary',
  type: 'button',
  asAnchor: false,
})

const variantClass = computed(() => variants[props.variant])
</script>

<template>
  <component
    :is="!asAnchor ? 'button' : 'a'"
    :class="`inline-flex items-center rounded-full border-0 p-2 shadow-sm focus:outline-none focus:ring-1 hover:opacity-75 disabled:opacity-25 ${variantClass}`"
    :type="!asAnchor ? type ?? 'button' : undefined"
  >
    <slot />
  </component>
</template>
