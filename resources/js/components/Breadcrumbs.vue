<script setup lang="ts">
import { ChevronRightIcon, HomeIcon } from '@heroicons/vue/24/outline'
import { Breadcrumb } from '__types__'

interface Props {
  items: Breadcrumb[]
  setPath: (path: string) => void
}

withDefaults(defineProps<Props>(), {
  items: () => [],
})
</script>

<template>
  <nav
    aria-label="Breadcrumb"
    class="flex dark:bg-gray-400/5 bg-gray-800/5 rounded-md p-2 justify-between items-center"
    data-tour="nfm-breadcrumbs"
  >
    <ol class="flex items-center space-x-2 flex-wrap gap-y-0.5" role="list">
      <li>
        <button
          class="flex items-center text-gray-400 dark:text-gray-600 hover:text-blue-500 dark:hover:text-blue-500 focus:outline-none"
          @click.prevent="setPath('/')"
        >
          <HomeIcon class="flex-shrink-0 h-4 w-4" />
        </button>
      </li>
      <li v-for="page in items" :key="page.path">
        <div class="flex items-center">
          <ChevronRightIcon class="flex-shrink-0 h-4 w-4 text-gray-400 dark:text-gray-600" />
          <button
            :class="`ml-2 text-xs font-regular hover:text-blue-500 ${
              page.current ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-600'
            }`"
            @click.prevent="setPath(page.path)"
          >
            {{ page.name }}
          </button>
        </div>
      </li>
    </ol>
  </nav>
</template>
