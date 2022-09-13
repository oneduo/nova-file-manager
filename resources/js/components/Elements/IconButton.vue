<template>
  <component
    :is="!asAnchor ? 'button' : 'a'"
    :class="`inline-flex items-center rounded-full border-0 p-2 shadow-sm focus:outline-none focus:ring-1 hover:opacity-75 disabled:opacity-25 ${variantClass}`"
    :type="!asAnchor ? type ?? 'button' : undefined"
  >
    <slot></slot>
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'secondary',
    validator: value => Object.keys(variants).includes(value),
  },
  type: {
    type: String,
    default: 'button',
    validator: value => ['button', 'submit', 'reset'].includes(value),
  },
  asAnchor: {
    type: Boolean,
    default: false,
  },
})

const variantClass = computed(() => variants[props.variant] ?? variants.secondary)
</script>

<script>
const variants = {
  primary: 'bg-blue-500 text-white focus:outline-blue-500',
  secondary: 'bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-blue-500',
  danger: 'bg-red-500 text-white focus:outline-red-500',
  success: 'bg-green-500 text-white focus:outline-green-500',
  transparent: 'bg-transparent text-gray-800 dark:text-gray-100',
}
</script>
