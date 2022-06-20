<template>
  <component
    class="inline-flex items-center py-2 rounded-md shadow-sm text-sm font-medium text-white hover:shadow-md"
    :is="href ? 'a' : 'button'"
    :href="href"
    :type="type"
    :class="buttonClass"
  >
    <slot></slot>
  </component>
</template>

<script>
const variants = {
  primary: 'bg-blue-500 dark:bg-blue-600/50 hover:bg-blue-500 dark:hover:bg-blue-600',
  secondary: 'bg-gray-400 dark:bg-gray-600/50 hover:bg-gray-500 dark:hover:bg-gray-600',
  success: 'bg-green-400 dark:bg-green-600/50 hover:bg-green-500 dark:hover:bg-green-600',
  warning: 'bg-orange-400 dark:bg-orange-600/50 hover:bg-orange-500 dark:hover:bg-orange-600',
  danger: 'bg-red-500 dark:bg-red-600/50 hover:bg-red-700 dark:hover:bg-red-600',
  transparent: 'bg-transparent',
}

export default {
  props: ['id', 'type', 'variant', 'icon', 'href'],

  computed: {
    buttonClass() {
      return `${this.colorClass} ${this.paddingClass}`
    },

    colorClass() {
      return variants[this.variant] || variants.primary
    },

    paddingClass() {
      return this.icon ? 'px-3' : 'px-4'
    },
  },
}
</script>
