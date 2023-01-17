<script setup lang="ts">
import { computed } from 'vue'
import Spinner from '@/components/Elements/Spinner.vue'

const variants = {
  primary: 'bg-blue-500 dark:bg-blue-600/50 focus:outline-blue-500',
  secondary: 'bg-gray-600 dark:bg-gray-600/50 focus:outline-gray-600',
  success: 'bg-green-500 dark:bg-green-600/50 focus:outline-green-500',
  warning: 'bg-orange-400 dark:bg-orange-600/50 focus:outline-orange-400',
  danger: 'bg-red-500 dark:bg-red-600/50 focus:outline-red-500',
  transparent: 'bg-transparent',
}

interface Props {
  variant: keyof typeof variants
  type: 'button' | 'submit' | 'reset'
  href?: string
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'secondary',
  type: 'button',
  loading: false,
})

const style = computed(() => variants[props.variant])
</script>

<template>
  <component
    :is="href ? 'a' : 'button'"
    :class="[
      'inline-flex items-center py-2 px-4 border-0 uppercase rounded-md shadow-sm text-xs font-medium text-white hover:shadow-md hover:opacity-75 disabled:opacity-50 focus:outline-none focus:ring-1',
      style,
    ]"
    :href="href"
    :type="type"
  >
    <Spinner v-if="loading" class="mr-2 w-3 h-3" />
    <slot />
  </component>
</template>