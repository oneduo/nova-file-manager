<template>
  <component
    :is="!asAnchor ? 'button' : 'a'"
    :type="!asAnchor ? type ?? 'button' : undefined"
    :class="`inline-flex items-center rounded-full border border-transparent p-1 shadow-sm focus:outline-none focus:ring-1 ${variantClass}`"
  >
    <slot></slot>
  </component>
</template>

<script>
const variants = {
  primary: 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-white',
  secondary:
    'bg-gray-300 dark:bg-gray-700 text-black dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 focus:ring-blue-500',
  danger: 'bg-red-500 dark:bg-red-500 text-white hover:bg-red-600 focus:ring-white',
  success: 'bg-green-500 text-white hover:bg-green-600 focus:ring-white',
  transparent:
    'bg-transparent text-black dark:text-white hover:bg-gray-900/20 dark:hover:bg-gray-900/50',
}
export default {
  name: 'IconButton',
  props: ['variant', 'asAnchor', 'type'],
  computed: {
    variantClass() {
      return variants[this.variant] ?? variants.secondary
    },
  },
}
</script>
