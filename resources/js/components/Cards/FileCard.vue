<template>
  <EntityCard :as="as" :checked="checked" :file="file">
    <div
      class="m-auto flex items-center justify-center bg-gray-200 dark:bg-gray-900 group-hover:opacity-75"
    >
      <DocumentIcon class="h-16 w-16 text-gray-600" />
    </div>
  </EntityCard>
</template>

<script setup>
import { DocumentIcon } from '@heroicons/vue/outline'
import EntityCard from '@/components/Elements/EntityCard'

defineProps(['file', 'checked', 'as'])
</script>
