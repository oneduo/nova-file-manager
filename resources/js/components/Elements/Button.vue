<template>
  <component
    :is="href ? 'a' : 'button'"
    :class="buttonClass"
    :href="href"
    :type="type"
    class="inline-flex items-center py-2 rounded-md shadow-sm text-sm font-medium text-white hover:shadow-md"
  >
    <slot></slot>
  </component>
</template>

<script setup>
import { computed } from 'vue'

const variants = {
    primary:
    'bg-blue-500 dark:bg-blue-600/50 hover:bg-blue-500 dark:hover:bg-blue-600',
    secondary:
    'bg-gray-400 dark:bg-gray-600/50 hover:bg-gray-500 dark:hover:bg-gray-600',
    success:
    'bg-green-400 dark:bg-green-600/50 hover:bg-green-500 dark:hover:bg-green-600',
    warning:
    'bg-orange-400 dark:bg-orange-600/50 hover:bg-orange-500 dark:hover:bg-orange-600',
    danger:
    'bg-red-500 dark:bg-red-600/50 hover:bg-red-700 dark:hover:bg-red-600',
    transparent: 'bg-transparent',
}

const props = defineProps(['id', 'type', 'variant', 'icon', 'href'])
const paddingClass = computed(() => (props.icon ? 'px-3' : 'px-4'))
const colorClass = computed(() => variants[props.variant] || variants.primary)
const buttonClass = computed(() => `${colorClass.value} ${paddingClass.value}`)
</script>
