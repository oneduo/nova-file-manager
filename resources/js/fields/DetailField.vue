<template>
  <PanelItem :index="index" :field="field">
    <template v-slot:value v-if="field.value?.file">
      <div>

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
          <button
            v-if="field.value?.file.name && field.copyable"
            @click.prevent="copy"
            type="button"
            class="flex items-center hover:bg-gray-50 dark:hover:bg-gray-900 text-gray-500 dark:text-gray-400 hover:text-gray-500 active:text-gray-600 rounded-lg px-1 -mx-1"
            v-tooltip="__('Copy to clipboard')"
          >
          <span ref="theFieldValue">
            {{ field.value?.file.name }}
          </span>

            <Icon
              class="text-gray-500 dark:text-gray-400 ml-1"
              :solid="true"
              type="clipboard"
              width="14"
            />
          </button>

          <p v-else>
            {{ field.value?.file.name }}
          </p>
          <div class="w-full mt-3 flex items-center space-x-2">
        <span
          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-400"
        >
          {{ field.value?.disk }}
        </span
        >
            <span
              class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-400"
            >{{ field.value?.file.size }}</span
            >
          </div>
        </div>
      </div>
    </template>
  </PanelItem>
</template>

<script>
import { CopiesToClipboard } from 'laravel-nova'
import { DocumentIcon } from '@heroicons/vue/outline'
import { mapState } from 'vuex'
import ImageLoader from '@/components/ImageLoader'

export default {
  mixins: [CopiesToClipboard],

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
  },

  methods: {
    copy() {
      this.copyValueToClipboard(this.field.value?.file.name)
    },
  },
}
</script>
<style scoped>
.video {

}
</style>
