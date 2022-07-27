<template>
  <PanelItem :index="index" :field="field">
    <template v-slot:value v-if="field.value?.file">
      <div>
        <div class="w-full mb-3 flex items-center space-x-2">
        <span
          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-400"
        >
          {{ field.value?.disk }}
        </span
        >
          <span
            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-400"
          >{{ field.value?.file.size }}</span
          >
        </div>
        <template v-if="isImage">
          <ImageLoader
            :src="field.value?.file.url"
            :maxWidth="maxWidth"
            :rounded="rounded"
            @missing="value => (missing = value)"
          />
        </template>
        <template v-else-if="isVideo">
          <video
            class="bg-white dark:bg-gray-800 rounded-lg shadow relative relative border border-lg border-gray-300 dark:border-gray-600 overflow-hidden px-0 py-0"
            controls="controls"
            :style="{ maxWidth: `${maxWidth}px`}"
          >
            <source :src="field.value?.file.url"/>
            Sorry, your browser doesn't support embedded videos.
          </video>
        </template>
        <div v-else class="flex items-center justify-center h-full border-gray-500">
          <DocumentIcon class="h-8 w-8"/>
        </div>
        <div class="w-full mt-3 sm">
          {{ field.value?.file.name }}
        </div>
      </div>
    </template>
  </PanelItem>
</template>

<script>
import { DocumentIcon } from '@heroicons/vue/outline'
import { mapState } from 'vuex'
import ImageLoader from '@/components/ImageLoader'

export default {
  name: 'DetailField',
  components: { DocumentIcon, ImageLoader },
  props: ['field', 'index'],

  computed: {
    ...mapState('nova-file-manager', ['darkMode']),
    isImage() {
      return this.field.value?.file.type === 'image'
    },
    isVideo() {
      return this.field.value?.file.type === 'video'
    },
    rounded() {
      return this.field.rounded
    },

    maxWidth() {
      return this.field.maxWidth || 320
    },
  }
}
</script>
<style scoped>
.video {

}
</style>
