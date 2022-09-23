<template>
  <div class="items-center rounded-lg bg-gray-100 dark:bg-gray-700/40 p-0.5 flex">
    <button
      v-for="view in views"
      :key="view.name"
      :class="[
        'rounded-md p-1.5 focus:outline-none focus:ring-1 focus:outline-blue-500',
        current === view.name ? selectedClass : unselectedClass,
      ]"
      type="button"
      @click.prevent="setView(view.name)"
    >
      <component :is="view.icon" class="h-5 w-5" />
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { QueueListIcon, Squares2X2Icon } from '@heroicons/vue/24/outline'

defineProps({
  current: {
    type: String,
    required: true,
  },
  setView: {
    type: Function,
    required: true,
  },
})

const views = ref([
  {
    name: 'list',
    icon: QueueListIcon,
  },
  {
    name: 'grid',
    icon: Squares2X2Icon,
  },
])

const selectedClass = 'bg-white dark:bg-gray-700 text-blue-500 shadow-sm '
const unselectedClass = 'text-gray-400 dark:hover:text-white hover:text-black'
</script>
