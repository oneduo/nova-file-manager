<template>
  <EntityCard
    :file="file"
    :checked="checked"
  >
    <img
      :src="file.url"
      alt=""
      class="group-hover:opacity-75 pointer-events-none object-cover"
    />
  </EntityCard>
</template>

<script>
import { CheckCircleIcon } from '@heroicons/vue/solid'
import Sidebar from '@/components/Sidebar'
import EntityCard from '@/components/Elements/EntityCard'

export default {
  components: {
    EntityCard,
    CheckCircleIcon,
    Sidebar,
  },
  props: ['file', 'checked'],
}
</script>

<style scoped></style>
