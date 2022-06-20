<template>
  <template v-if="view === 'grid'">
    <div class="space-y-2">
      <Disclosure
        v-if="directories?.length"
        :default-open="true"
        v-slot="{ open }"
      >
        <DisclosureButton v-slot="{ open }">
          <div class="flex flex-row w-full items-center gap-x-1">
            <span class="text-gray-500 text-xs">{{ __('Folders') }}</span>
            <ChevronDownIcon
              class="h-3 w-3 text-gray-600"
              v-if="open"
            />
            <ChevronRightIcon
              class="h-3 w-3 text-gray-600"
              v-else
            />
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
      <Disclosure
        v-if="files?.length"
        :default-open="true"
        v-slot="{ open }"
      >
        <DisclosureButton>
          <div class="flex flex-row w-full items-center gap-x-1">
            <span class="text-gray-500 text-xs">Files</span>
            <ChevronDownIcon
              class="h-3 w-3 text-gray-600"
              v-if="open"
            />
            <ChevronRightIcon
              class="h-3 w-3 text-gray-600"
              v-else
            />
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

<script>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronDownIcon, ChevronRightIcon } from '@heroicons/vue/outline'
import FileGrid from '@/components/FileGrid'
import DirectoryGrid from '@/components/DirectoryGrid'
import List from '@/components/List'
import Empty from '@/components/Empty'

export default {
  name: 'BrowserContent',
  components: {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    ChevronDownIcon,
    ChevronRightIcon,
    FileGrid,
    DirectoryGrid,
    List,
    Empty,
  },
  props: ['view', 'files', 'directories', 'filled'],
}
</script>

<style scoped></style>
