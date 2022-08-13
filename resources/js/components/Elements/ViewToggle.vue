<template>
  <div class="hidden items-center rounded-lg bg-gray-100 dark:bg-gray-700/40 p-0.5 sm:flex">
    <button
      v-for="view in views"
      :key="view.name"
      :class="current === view.name ? selectedClass : unselectedClass"
      type="button"
      @click.prevent="setView(view.name)"
    >
      <component :is="view.icon" class="h-5 w-5" />
    </button>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { ViewGridIcon, ViewListIcon } from '@heroicons/vue/outline'

defineProps(['current', 'setView'])

const views = reactive([
    {
        name: 'list',
        icon: ViewListIcon,
    },
    {
        name: 'grid',
        icon: ViewGridIcon,
    },
])
const selectedClass = ref(
    'rounded-md bg-white dark:bg-gray-700 p-1.5 text-blue-500 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500'
)
const unselectedClass = ref(
    'rounded-md p-1.5 text-gray-400 dark:hover:text-white hover:text-black focus:outline-none focus:ring-1 focus:ring-blue-500'
)
</script>
