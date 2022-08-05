<template>
  <EntityCard
      :file="file"
      :checked="checked"
      :as="as"
  >
    <div
        class="m-auto flex items-center justify-center bg-gray-200 dark:bg-gray-900 group-hover:opacity-75"
    >
      <DocumentIcon class="h-16 w-16 text-gray-600"/>
    </div>
  </EntityCard>
</template>

<script>
import {CheckCircleIcon} from '@heroicons/vue/solid'
import {DocumentIcon} from '@heroicons/vue/outline'
import Sidebar from '@/components/Sidebar'
import EntityCard from '@/components/Elements/EntityCard'

export default {
  components: {
    EntityCard,
    CheckCircleIcon,
    DocumentIcon,
    Sidebar,
  },
  props: ['file', 'checked', 'as'],
}
</script>

<style scoped></style>
