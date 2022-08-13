<template>
  <EntityCard :as="as" :checked="checked" :file="file">
    <img :src="file.url" alt="" class="group-hover:opacity-75 pointer-events-none object-cover" />
  </EntityCard>
</template>

<script setup>
import EntityCard from '@/components/Elements/EntityCard'

defineProps(['file', 'checked', 'as'])
</script>
