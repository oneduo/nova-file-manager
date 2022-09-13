<template>
  <div
    @dragleave.prevent.self="dragLeave"
    class="absolute inset-0 z-50 pt-16 bg-gray-100/90 dark:bg-gray-700/80 rounded-md backdrop-blur-sm w-full h-full flex justify-start flex-col items-center border border-blue-500"
  >
    <CloudArrowUpIcon class="w-16 h-16 text-blue-500 animate-bounce" />
    <p class="font-bold text-gray-900 dark:text-gray-50 p-2 rounded-md">
      {{ __('NovaFileManager.dropzone.prompt') }}
    </p>
  </div>
</template>

<script setup>
import { CloudArrowUpIcon } from '@heroicons/vue/24/outline'

defineProps({
  dragLeave: {
    type: Function,
    required: true,
  },
})
</script>
