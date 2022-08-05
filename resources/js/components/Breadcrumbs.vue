<template>
  <nav
      class="flex dark:bg-gray-400/5 bg-gray-800/5 rounded-md p-2 justify-between items-center"
      aria-label="Breadcrumb"
  >
    <ol
        role="list"
        class="flex items-center space-x-2 flex-wrap gap-y-0.5"
    >
      <li>
        <button
            @click.prevent="setPath('/')"
            class="flex items-center text-gray-400 dark:text-gray-600 hover:text-blue-500 dark:hover:text-blue-500 focus:outline-none"
        >
          <HomeIcon class="flex-shrink-0 h-4 w-4"/>
        </button>
      </li>
      <li v-for="page in items">
        <div class="flex items-center">
          <ChevronRightIcon class="flex-shrink-0 h-4 w-4 text-gray-400 dark:text-gray-600"/>
          <button
              @click.prevent="setPath(page.path)"
              :class="`ml-2 text-xs font-regular hover:text-blue-500 ${
              page.current ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-600'
            }`"
          >
            {{ page.name }}
          </button>
        </div>
      </li>
    </ol>
<!--    todo-->
<!--    <div v-if="selection.length" class="flex items-center border border-blue-500 rounded-md p-1">-->
<!--      <span class="text-xs text-blue-500">{{ __('NovaFileManager.totalFilesCount', {count: selection.length}) }}</span>-->
<!--    </div>-->
  </nav>
</template>

<script>
import {ChevronRightIcon, HomeIcon} from '@heroicons/vue/outline'
import {mapState} from "vuex";

export default {
  components: {
    HomeIcon,
    ChevronRightIcon,
  },
  props: {
    items: null,
    setPath: null,
  },
  computed: {
    ...mapState('nova-file-manager', ['selection']),
  }
}
</script>
