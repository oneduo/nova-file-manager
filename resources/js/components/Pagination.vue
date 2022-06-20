<template>
  <div
    class="pt-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-4 py-2"
  >
    <div class="flex-1 flex items-center justify-between lg:hidden">
      <button
        @click.prevent="() => setPage(Math.max(1, currentPage - 1))"
        class="relative inline-flex items-center px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm font-semibold text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800"
      >
        {{ __('Previous') }}
      </button>
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-400 space-x-1">
          <span class="font-semibold">{{ from }}</span>
          <span> {{ __('-') }} </span>
          <span class="font-semibold">{{ to }}</span>
          <span>{{ __('/') }}</span>
          <span class="font-semibold">{{ total }}</span>
        </p>
      </div>
      <button
        @click.prevent="() => setPage(Math.min(lastPage, currentPage + 1))"
        class="relative inline-flex items-center px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm font-semibold text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800"
      >
        {{ __('Next') }}
      </button>
    </div>
    <div class="hidden lg:flex-1 lg:flex lg:items-center lg:justify-between lg:flex-wrap">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-400 space-x-1">
          <span>{{ __('Showing') }}</span>
          <span class="font-semibold">{{ from }}</span>
          <span> {{ __('to') }} </span>
          <span class="font-semibold">{{ to }}</span>
          <span>{{ __('of') }}</span>
          <span class="font-semibold">{{ total }}</span>
        </p>
      </div>
      <div>
        <nav
          class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
          aria-label="Pagination"
        >
          <button
            @click.prevent="() => setPage(Math.max(1, currentPage - 1))"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/30 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200"
          >
            <span class="sr-only">{{ __('Previous') }}</span>
            <ChevronLeftIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </button>
          <button
            v-for="link in links.slice(1, -1)"
            @click.prevent="Number(link.label) ? setPage(link.label) : null"
            :class="{
              'z-10 bg-blue-50 dark:bg-blue-800/30 border-blue-500 text-blue-600 dark:text-blue-300 relative inline-flex items-center px-4 py-2 border text-sm font-medium':
                link.active,
              'bg-white dark:bg-gray-900/30 border-gray-300 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200 relative inline-flex items-center px-4 py-2 border text-sm font-medium':
                !link.active,
            }"
          >
            {{ link.label }}
          </button>
          <button
            @click.prevent="() => setPage(Math.min(lastPage, currentPage + 1))"
            :disabled="lastPage === currentPage"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/30 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200"
          >
            <span class="sr-only">{{ __('Next') }}</span>
            <ChevronRightIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid'
import { mapActions } from 'vuex'

export default {
  components: {
    ChevronLeftIcon,
    ChevronRightIcon,
  },

  props: ['currentPage', 'from', 'to', 'total', 'lastPage', 'links'],

  methods: {
    ...mapActions('nova-file-manager', ['setPage']),
  },
}
</script>
