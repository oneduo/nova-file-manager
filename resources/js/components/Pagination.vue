<template>
  <div
    class="pt-3 flex items-center justify-between border-t border-gray-200/50 dark:border-gray-700/50 px-4 py-2"
  >
    <div class="flex-1 flex items-center justify-between md:hidden">
      <button
        class="relative inline-flex items-center px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100/50 dark:bg-gray-900 text-sm font-semibold text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800"
        @click.prevent="() => setPage(Math.max(1, currentPage - 1))"
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
        class="relative inline-flex items-center px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100/50 dark:bg-gray-900 text-sm font-semibold text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800"
        @click.prevent="() => setPage(Math.min(lastPage, currentPage + 1))"
      >
        {{ __('Next') }}
      </button>
    </div>
    <div class="hidden md:flex-1 md:flex md:items-center md:justify-between md:flex-wrap">
      <div>
        <p class="text-xs text-gray-500 space-x-1">
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
          aria-label="Pagination"
          class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
        >
          <button
            :disabled="currentPage === 1"
            class="relative inline-flex items-center p-2 rounded-l-md border border-gray-300 dark:border-gray-700 bg-gray-100/50 dark:bg-gray-900/30 text-xs font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200"
            @click.prevent="() => setPage(Math.max(1, currentPage - 1))"
          >
            <span class="sr-only">{{ __('Previous') }}</span>
            <ChevronLeftIcon aria-hidden="true" class="h-4 w-4" />
          </button>
          <button
            v-for="(link, index) in links?.slice(1, -1)"
            :key="index"
            :class="{
              'z-10 bg-blue-50 dark:bg-blue-800/30 border-blue-500 text-blue-600 dark:text-blue-300 relative inline-flex items-center py-1 px-3 border text-xs font-medium':
                link.active,
              'bg-gray-100/50 dark:bg-gray-900/30 border-gray-300 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200 relative inline-flex items-center py-1 px-3 border text-xs font-medium':
                !link.active,
            }"
            @click.prevent="Number(link.label) ? setPage(link.label) : null"
          >
            {{ link.label }}
          </button>
          <button
            :disabled="lastPage === currentPage"
            class="relative inline-flex items-center p-2 rounded-r-md border border-gray-300 dark:border-gray-700 bg-gray-100/50 dark:bg-gray-900/30 text-xs font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200"
            @click.prevent="() => setPage(Math.min(lastPage, currentPage + 1))"
          >
            <span class="sr-only">{{ __('Next') }}</span>
            <ChevronRightIcon aria-hidden="true" class="h-4 w-4" />
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { useStore } from '@/store'

const store = useStore()

const pagination = computed(() => store.pagination)

const {
  current_page: currentPage,
  last_page: lastPage,
  from,
  to,
  total,
  links,
} = pagination.value

const setPage = page => store.setPage({ page })
</script>