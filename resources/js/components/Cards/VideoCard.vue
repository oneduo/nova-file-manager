<template>
  <EntityCard
    :file="file"
    :checked="checked"
  >
    <video class="group-hover:opacity-75 pointer-events-none object-cover">
      <source :src="file.url" />
      Sorry, your browser doesn't support embedded videos.
    </video>

    <div class="absolute m-auto flex items-center justify-center bg-transparent">
      <PlayIcon class="h-16 w-16 text-white/60" />
    </div>
  </EntityCard>
</template>

<script>
import { CheckCircleIcon, PlayIcon } from '@heroicons/vue/solid'
import Sidebar from '@/components/Sidebar'
import EntityCard from '@/components/Elements/EntityCard'

export default {
  components: {
    EntityCard,
    CheckCircleIcon,
    PlayIcon,
    Sidebar,
  },
  props: ['file', 'checked'],
}
</script>
