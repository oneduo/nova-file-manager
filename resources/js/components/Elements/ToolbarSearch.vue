<template>
  <div
    class="relative rounded-md w-full md:w-1/3 md:focus-within:w-full md:duration-500 md:transition-all"
  >
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <SearchIcon aria-hidden="true" class="h-4 w-4 text-gray-400" />
    </div>
    <input
      :placeholder="__('Search')"
      :value="search"
      class="rounded-full pr-3 h-9 pl-8 w-full bg-gray-100 focus:dark:bg-gray-700 dark:bg-gray-700/40 dark:focus:bg-gray-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm placeholder-gray-500 text-gray-500 dark:text-gray-200"
      type="search"
      @input="setSearch"
    />
  </div>
</template>

<script setup>
import { useStore } from 'vuex'
import { computed } from 'vue'
import { SearchIcon } from '@heroicons/vue/outline'
import debounce from 'lodash/debounce'

const store = useStore()
const search = computed(() => store.state['nova-file-manager'].search)

const setSearch = debounce(function ({ target: { value } }) {
    store.dispatch('nova-file-manager/setSearch', value)
}, Nova.config('debounce'))
</script>
