<template>
  <PanelItem
    :index="index"
    :field="field"
  >
    <template
      v-slot:value
      v-if="field.value?.file"
    >
      <div
        class="block w-full overflow-hidden rounded-lg border border-gray-300 dark:border-gray-700 max-w-xs mb-2"
      >
        <img
          v-if="field.value?.file.type === 'image'"
          :src="field.value?.file.url"
          alt=""
          class="rounded-md"
        />
        <template v-else-if="field.value?.file.type === 'video'">
          <div class="relative">
            <video
              class="rounded-md"
              controls="controls"
            >
              <source :src="field.value?.file.url" />
              Sorry, your browser doesn't support embedded videos.
            </video>
          </div>
        </template>
        <div
          v-else
          class="flex items-center justify-center h-full border-gray-500"
        >
          <DocumentIcon class="h-8 w-8" />
        </div>
      </div>
      <div class="flex items-center w-full space-x-2">
        <div class="text-xs font-bold text-gray-500">
          {{ field.value?.file.name }}
        </div>
        <span
          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:bg-gray-700"
          >{{ field.value?.file.size }}</span
        >
        <span
          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:bg-gray-700"
          >{{ field.value?.disk }}</span
        >
      </div>
    </template>
  </PanelItem>
</template>

<script>
import { DocumentIcon } from '@heroicons/vue/outline'

export default {
  name: 'DetailField',
  components: { DocumentIcon },
  props: ['field', 'index'],
}
</script>
