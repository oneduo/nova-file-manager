<template>
  <template v-if="view === 'grid'">
    <div class="space-y-2 mb-2">
      <Disclosure v-if="directories?.length" v-slot="{ open }" :default-open="true">
        <DisclosureButton>
          <div class="flex flex-row w-full items-center gap-x-1">
            <span class="text-gray-500 text-xs">{{ __('Folders') }}</span>
            <ChevronDownIcon v-if="open" class="h-3 w-3 text-gray-600" />
            <ChevronRightIcon v-else class="h-3 w-3 text-gray-600" />
          </div>
        </DisclosureButton>

        <DisclosurePanel>
          <section aria-labelledby="gallery-heading">
            <DirectoryGrid :directories="directories" />
          </section>
        </DisclosurePanel>
      </Disclosure>
    </div>
    <div class="space-y-2">
      <Disclosure v-if="files?.length" v-slot="{ open }" :default-open="true">
        <DisclosureButton>
          <div class="flex flex-row w-full items-center gap-x-1">
            <span class="text-gray-500 text-xs">Files</span>
            <ChevronDownIcon v-if="open" class="h-3 w-3 text-gray-600" />
            <ChevronRightIcon v-else class="h-3 w-3 text-gray-600" />
          </div>
        </DisclosureButton>

        <DisclosurePanel>
          <section aria-labelledby="gallery-heading">
            <FileGrid />
          </section>
        </DisclosurePanel>
      </Disclosure>
    </div>
  </template>
  <template v-else>
    <List />
  </template>

  <Empty v-if="!filled" />
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronDownIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import FileGrid from '@/components/FileGrid'
import DirectoryGrid from '@/components/DirectoryGrid'
import List from '@/components/List'
import Empty from '@/components/Empty'

defineProps(['view', 'files', 'directories', 'filled'])
</script>
