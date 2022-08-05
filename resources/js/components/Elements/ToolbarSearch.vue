<template>
  <div
    class="relative rounded-md shadow-sm w-full md:w-1/3 md:focus-within:w-full md:duration-500 md:transition-all"
  >
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <SearchIcon
        class="h-4 w-4 text-gray-400"
        aria-hidden="true"
      />
    </div>
    <input
      type="search"
      class="rounded-full pr-3 h-9 pl-8 w-full bg-gray-100 focus:dark:bg-gray-700 dark:bg-gray-700/40 dark:focus:bg-gray-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm placeholder-gray-500 text-gray-500 dark:text-gray-200"
      :placeholder="__('Search')"
      :value="search"
      @input="_search"
    />
  </div>
</template>

<script>
import { SearchIcon } from '@heroicons/vue/outline'
import { mapActions, mapState } from 'vuex'
import debounce from 'lodash/debounce'

export default {
  components: {
    SearchIcon,
  },

  computed: {
    ...mapState('nova-file-manager', ['search']),
  },

  methods: {
    ...mapActions('nova-file-manager', ['setSearch']),

    _search: debounce(function ({ target: { value } }) {
      this.setSearch(value)
    }, Nova.config('debounce')),
  },
}
</script>
