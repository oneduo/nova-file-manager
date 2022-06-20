<template>
  <RadioGroup v-model="selected">
    <ul
      role="list"
      class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    >
      <RadioGroupOption
        as="template"
        v-for="file in files"
        :value="file"
        v-slot="{ checked, active }"
      >
        <div>
          <ImageCard
            v-if="file.type === 'image'"
            :file="file"
            :checked="checked"
          />
          <VideoCard
            v-else-if="file.type === 'video'"
            :file="file"
            :checked="checked"
          />
          <FileCard
            v-else
            :file="file"
            :checked="checked"
          />
        </div>
      </RadioGroupOption>
    </ul>
  </RadioGroup>
</template>

<script>
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import ImageCard from './Cards/ImageCard.vue'
import VideoCard from './Cards/VideoCard.vue'
import FileCard from './Cards/FileCard.vue'
import { mapMutations, mapState } from 'vuex'
import Sidebar from '@/components/Sidebar'
export default {
  components: {
    RadioGroup,
    RadioGroupOption,
    ImageCard,
    VideoCard,
    FileCard,
    Sidebar,
  },
  methods: {
    ...mapMutations('nova-file-manager', ['setSelectedFile']),
  },
  computed: {
    ...mapState('nova-file-manager', ['files', 'selectedFile']),
    selected: {
      get() {
        return this.selectedFile
      },
      set(value) {
        this.setSelectedFile(value)
      },
    },
  },
}
</script>
