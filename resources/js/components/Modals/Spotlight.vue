<template>
  <TransitionRoot
    :show="isOpen"
    as="template"
    class="nova-file-manager"
    @after-leave="close"
    appear
  >
    <Dialog as="div" class="relative z-[60]" @close="close">
      <TransitionChild
        as="template"
        enter="ease-out duration-100"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-100"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-800/20 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <div :class="{ dark }" class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 scale-95"
          enter-to="opacity-100 scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 scale-100"
          leave-to="opacity-0 scale-95"
        >
          <DialogPanel
            class="mx-auto max-w-xl transform divide-y divide-gray-100 dark:divide-gray-800 overflow-hidden rounded-xl bg-white dark:bg-gray-900 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
          >
            <Combobox @update:modelValue="onSelect" :default-value="null">
              <div class="relative">
                <MagnifyingGlassIcon
                  class="pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-400"
                  aria-hidden="true"
                  v-if="!isSearching || help"
                />

                <Spinner
                  class="pointer-events-none absolute top-3.5 left-4 h-5 w-5"
                  aria-hidden="true"
                  v-else
                />

                <ComboboxInput
                  class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none sm:text-sm"
                  :placeholder="__('NovaFileManager.spotlight.placeholder')"
                  @change.prevent.stop="onSearch"
                />
              </div>

              <ComboboxOptions
                v-if="query.length && hasResults && !isSearching"
                class="max-h-80 scroll-py-10 scroll-pb-2 space-y-4 overflow-y-auto p-4 pb-2"
              >
                <li v-if="folders?.length">
                  <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-600 uppercase">
                    {{ __('Folders') }}
                  </h2>
                  <ul class="-mx-4 mt-2 text-sm text-gray-700">
                    <ComboboxOption
                      v-for="folder in folders"
                      :key="folder.id"
                      :value="folder"
                      as="template"
                      v-slot="{ active }"
                    >
                      <li
                        :class="[
                          'flex cursor-default select-none items-center px-4 py-2',
                          active ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-400',
                        ]"
                      >
                        <FolderIcon
                          :class="[
                            'h-6 w-6 flex-none',
                            active ? 'text-white' : 'text-gray-400 dark:text-gray-400',
                          ]"
                          aria-hidden="true"
                        />
                        <span class="ml-3 flex-auto truncate">{{ folder.name }}</span>
                      </li>
                    </ComboboxOption>
                  </ul>
                </li>
                <li v-if="files?.length">
                  <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-600 uppercase">
                    {{ __('Files') }}
                  </h2>
                  <ul class="-mx-4 mt-2 text-sm text-gray-700">
                    <ComboboxOption
                      v-for="file in files"
                      :key="file.id"
                      :value="file"
                      as="template"
                      v-slot="{ active }"
                    >
                      <li
                        :class="[
                          'flex cursor-default select-none items-center px-4 py-2',
                          active ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-400',
                        ]"
                      >
                        <DocumentIcon
                          :class="[
                            'h-6 w-6 flex-none',
                            active ? 'text-white' : 'text-gray-400 dark:text-gray-400',
                          ]"
                          aria-hidden="true"
                        />
                        <span class="ml-3 flex-auto truncate">{{ file.name }}</span>
                      </li>
                    </ComboboxOption>
                  </ul>
                </li>
              </ComboboxOptions>

              <div v-if="help && !isSearching" class="py-14 px-6 text-center text-sm sm:px-14">
                <LifebuoyIcon class="mx-auto h-6 w-6 text-gray-400" aria-hidden="true" />
                <p class="mt-4 font-semibold text-gray-900">
                  {{ __('NovaFileManager.spotlight.helpTitle') }}
                </p>
                <p class="mt-2 text-gray-500">
                  {{ __('NovaFileManager.spotlight.helpText') }}
                </p>
              </div>

              <div
                v-if="query?.length && !isSearching && !hasResults"
                class="py-14 px-6 text-center text-sm sm:px-14"
              >
                <ExclamationTriangleIcon class="mx-auto h-6 w-6 text-gray-400" aria-hidden="true" />
                <p class="mt-4 font-semibold text-gray-900">
                  {{ __('NovaFileManager.spotlight.noResults') }}
                </p>
                <p class="mt-2 text-gray-500">
                  {{ __('NovaFileManager.spotlight.noResultsTryAgain') }}
                </p>
              </div>

              <div
                class="flex flex-wrap items-center bg-gray-50 dark:bg-gray-900 py-2.5 px-4 text-xs text-gray-700 dark:text-gray-500"
              >
                {{ __('Type') }}
                <template v-for="tip in tips" :key="tip.key">
                  <kbd
                    :class="[
                      'mx-1 flex h-5 w-5 items-center justify-center rounded border bg-white dark:bg-gray-800 font-semibold sm:mx-2',
                      tip?.active
                        ? 'border-blue-500 text-blue-500'
                        : 'border-gray-400 dark:border-gray-600 text-gray-500 dark:text-gray-500',
                    ]"
                    >{{ tip.key }}</kbd
                  >
                  <span>{{ tip.label }}</span>
                </template>
              </div>
            </Combobox>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import {
  DocumentIcon,
  ExclamationTriangleIcon,
  FolderIcon,
  LifebuoyIcon,
} from '@heroicons/vue/24/outline'
import {
  Combobox,
  ComboboxInput,
  ComboboxOption,
  ComboboxOptions,
  Dialog,
  DialogPanel,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'
import { useSearchStore } from '@/store/search'
import { useStore } from '@/store'
import Spinner from '@/components/Elements/Spinner'

const searchStore = useSearchStore()
const store = useStore()

// HOOKS
onMounted(() => {
  window.addEventListener('keydown', function (e) {
    if (e.metaKey && e.code === 'KeyK') {
      searchStore.open()
    }
  })
})

// STATE
const dark = computed(() => store.dark)
const folders = computed(() => searchStore.folders)
const files = computed(() => searchStore.files)
const query = computed(() => searchStore.query)
const isSearching = computed(() => searchStore.isLoading)
const isOpen = computed(() => searchStore.isOpen)
const hasResults = computed(() => searchStore.hasResults)
const isFolderOnly = computed(() => searchStore.isFolderOnly)
const isFileOnly = computed(() => searchStore.isFileOnly)
const help = computed(() => searchStore.help)

const tips = computed(() => [
  {
    key: '#',
    label: 'for folders',
    active: isFolderOnly.value,
  },
  {
    key: '>',
    label: 'for files',
    active: isFileOnly.value,
  },
  {
    key: '?',
    label: 'for help',
    active: help.value,
  },
])

// ACTIONS
const close = () => searchStore.close()
const onSelect = item => searchStore.select({ item })

const onSearch = event => {
  searchStore.setSearch({ search: event.target.value })
}
</script>
