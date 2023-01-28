<script setup lang="ts">
import { DocumentIcon } from '@heroicons/vue/24/outline'
import { Entity, NovaField } from '__types__'
import isNil from 'lodash/isNil'
import take from 'lodash/take'
import { computed, onMounted } from 'vue'
import useBrowserStore from '@/stores/browser'

interface Props {
  field: NovaField
}

const props = defineProps<Props>()
const store = useBrowserStore()
const dark = computed(() => store.dark)

onMounted(() => store.syncDarkMode())

const filled = (value?: Entity | Entity[] | null) => !isNil(value)

const usesCustomizedDisplay = computed(() => props.field.usesCustomizedDisplay && filled(props.field.displayedAs))
const shouldDisplayAsHtml = computed(() => props.field.asHtml)

const values = computed(() => props.field.value || [])
const thumbnails = computed(() => take(values.value, 3))
const rest = computed(() => Math.max(values.value.length - 3, 0))

const fieldHasValue = computed(() => !!props.field.value?.length)
const fieldValue = computed(() => {
  if (!usesCustomizedDisplay.value && !fieldHasValue.value) {
    return null
  }

  return usesCustomizedDisplay.value ? String(props.field.displayedAs) : props.field.value
})
</script>

<template>
  <div :class="`text-${field.textAlign} nova-file-manager`">
    <div :class="{ dark }">
      <template v-if="usesCustomizedDisplay">
        <span v-if="fieldValue && !shouldDisplayAsHtml" class="text-90 whitespace-nowrap">
          {{ fieldValue }}
        </span>
        <div @click.stop v-else-if="fieldValue && shouldDisplayAsHtml" v-html="fieldValue" />
      </template>
      <template v-else>
        <div v-if="fieldHasValue">
          <div class="isolate flex -space-x-2">
            <template v-for="(file, index) in thumbnails" :key="file.id">
              <img
                v-if="file.type === 'image'"
                :src="file.url"
                class="relative inline-block h-10 w-10 rounded-xl ring-gray-200 dark:ring-gray-700 ring-1 shadow object-cover"
                :class="`z-${index + 1}0`"
              />

              <template v-else-if="file.type === 'video'">
                <video
                  class="relative inline-block h-10 w-10 rounded-xl ring-gray-200 dark:ring-gray-700 ring-1 shadow object-cover"
                  :class="`z-${index + 1}0`"
                >
                  <source :src="file.url" />
                  {{ __("Sorry, your browser doesn't support embedded videos.") }}
                </video>
              </template>

              <div
                v-else
                class="relative inline-block flex items-center justify-center h-10 w-10 rounded-xl ring-gray-200 dark:ring-gray-700 ring-1 shadow bg-gray-50 dark:bg-gray-900"
                :class="`z-${index + 1}0`"
              >
                <DocumentIcon class="h-6 w-6" />
              </div>
            </template>
            <div
              v-if="rest > 0"
              class="relative z-40 inline-block h-10 w-10 rounded-xl ring-gray-200 dark:ring-gray-700 ring-1 bg-gray-50 dark:bg-gray-900 dark:text-gray-400 flex items-center justify-center shadow"
            >
              +{{ rest }}
            </div>
          </div>
        </div>
        <p v-else>&mdash;</p>
      </template>
    </div>
  </div>
</template>
