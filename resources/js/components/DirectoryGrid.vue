<template>
  <div
    role="list"
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
  >
    <DirectoryCard
      v-for="directory in directories"
      :disk="directory.disk"
      :name="directory.name"
      :path="directory.path"
      :id="directory.id"
    />
  </div>
</template>

<script>
import { FolderIcon } from '@heroicons/vue/outline'
import DirectoryCard from '@/components/Cards/DirectoryCard'

export default {
  name: 'DirectoryGrid',
  components: {
    DirectoryCard,
    FolderIcon,
  },
  props: ['directories'],
}
</script>
