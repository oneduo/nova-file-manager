<script setup lang="ts">
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronDownIcon, ChevronRightIcon, ChevronLeftIcon } from '@heroicons/vue/24/outline'
import { Entity, Folder, View } from '__types__'
import { computed } from 'vue'
import Empty from '@/components/Empty.vue'
import FileGrid from '@/components/FileGrid.vue'
import FolderGrid from '@/components/FolderGrid.vue'
import List from '@/components/List.vue'

interface Props {
  view: View
  files: Entity[]
  folders: Folder[]
  filled?: boolean
}

withDefaults(defineProps<Props>(), {
  folders: () => [],
  files: () => [],
  filled: false,
  view: 'grid',
})

const isLtr = computed(() => document.dir === 'ltr')
</script>

<template>
  <template v-if="view === 'grid'">
    <div class="space-y-2 mb-2">
      <Disclosure v-if="folders?.length" v-slot="{ open }" :default-open="true">
        <DisclosureButton>
          <div class="flex flex-row w-full items-center gap-x-1">
            <span class="text-gray-500 text-xs">{{ __('Folders') }}</span>
            <ChevronDownIcon v-if="open" class="h-3 w-3 text-gray-600" />
            <ChevronRightIcon v-if="!open && isLtr" class="h-3 w-3 text-gray-600" />
            <ChevronLeftIcon v-if="!open && !isLtr" class="h-3 w-3 text-gray-600" />
          </div>
        </DisclosureButton>

        <DisclosurePanel>
          <section aria-labelledby="gallery-heading">
            <FolderGrid :folders="folders" />
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
            <ChevronRightIcon v-if="!open && isLtr" class="h-3 w-3 text-gray-600" />
            <ChevronLeftIcon v-if="!open && !isLtr" class="h-3 w-3 text-gray-600" />
          </div>
        </DisclosureButton>

        <DisclosurePanel>
          <section aria-labelledby="gallery-heading">
            <FileGrid :files="files" />
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
