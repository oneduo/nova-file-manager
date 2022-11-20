<script setup lang="ts">
import { DocumentIcon } from '@heroicons/vue/24/outline'
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  PlayIcon,
  XCircleIcon,
} from '@heroicons/vue/24/solid'
import { Entity } from '__types__'
import { computed } from 'vue'
import ImageLoader from '../Elements/ImageLoader.vue'
import Spinner from '../Elements/Spinner.vue'

interface Props {
  file: Entity
  isUploading?: boolean
  isUploaded?: boolean
  uploadRatio?: number
  selected: boolean
  onDeselect: (file: Entity) => void
  singleDisk?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  selected: false,
})

const isImage = computed(() => props.file.type === 'image')
const isVideo = computed(() => props.file.type === 'video')
const isFile = computed(() => props.file.type !== 'image' && props.file.type !== 'video')
const missing = computed(() => !props.file.exists)
const name = computed(() => (missing.value ? props.file.path : props.file.name))
</script>
<template>
  <button
    class="relative cursor-pointer focus:rounded-md group focus:outline-none flex flex-col items-start"
    :title="name"
  >
    <div
      :class="[
        'relative block aspect-square w-full overflow-hidden rounded-lg hover:shadow-md hover:opacity-75 border border-gray-200/50 dark:border-gray-700/50 text-left',
        'group-focus-visible:outline group-focus-visible:outline-2 group-focus-visible:outline-blue-500/50',
        selected ? 'outline outline-2 outline-blue-500 group-focus-visible:outline-blue-500' : '',
      ]"
    >
      <div class="absolute z-40 inset-0 flex justify-center items-center w-full h-full" v-if="isUploading">
        <Spinner class="w-16 h-16" v-if="isUploaded === null" />
        <ExclamationCircleIcon class="w-16 h-16 text-red-500" v-else-if="isUploaded === false" />
        <CheckCircleIcon class="w-16 h-16 text-green-500" v-else-if="isUploaded === true" />
      </div>

      <div class="absolute inset-0 w-full h-full bg-gray-100/50 dark:bg-gray-800/50" v-if="isUploading" />

      <div
        class="absolute inset-0 flex flex-row items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-100"
        v-if="isUploading && isUploaded === null"
      >
        <span>{{ uploadRatio }}%</span>
      </div>

      <div class="m-auto z-20 flex h-full items-center justify-center select-none">
        <template v-if="missing && !isUploading">
          <div class="m-auto flex h-full w-full items-center justify-center bg-gray-50 dark:bg-gray-900 text-red-500">
            <ExclamationTriangleIcon class="w-16 h-16" />
          </div>
        </template>
        <template v-else>
          <div
            class="m-auto flex h-full w-full items-center justify-center bg-gray-50 dark:bg-gray-900 text-gray-600"
            v-if="isFile"
          >
            <DocumentIcon class="w-16 h-16" v-if="!isUploading" />
          </div>

          <ImageLoader v-if="isImage" :src="file.url" :alt="file.name" />

          <template v-if="isVideo">
            <video class="pointer-events-none w-full h-full object-cover">
              <source :src="file.url" />
              Sorry, your browser doesn't support embedded videos.
            </video>

            <div class="absolute m-auto flex items-center justify-center bg-transparent" v-if="!isUploading">
              <PlayIcon class="h-16 w-16 text-white/60" />
            </div>
          </template>
        </template>
      </div>

      <div class="absolute right-1 top-1" v-if="onDeselect">
        <button v-if="onDeselect" @click="onDeselect(file)" class="text-red-500">
          <XCircleIcon class="h-4 w-4" />
        </button>
      </div>
    </div>

    <p
      v-if="!missing || isUploading"
      :class="[
        'pointer-events-none mt-2 block truncate font-medium text-gray-900 dark:text-gray-50 text-left w-full',
        isUploading || onDeselect ? 'text-xs' : 'text-sm',
      ]"
      :title="!isUploading ? name : file.name"
    >
      {{ !isUploading ? name : file.name }}
    </p>

    <p v-if="missing && !isUploading" class="text-sm text-red-500 font-semibold text-left break-all">
      {{ __('NovaFileManager.fileMissing', { path: file.path }) }}
    </p>

    <div
      class="gap-x-0.5 inline-flex flex-wrap items-center text-xs pointer-events-none block font-medium text-gray-500 text-left break-all"
    >
      <span v-if="file.size">{{ file.size }}</span>
      <span v-if="!singleDisk && file.disk?.length > 0" class="ml-0.5">&centerdot; {{ file.disk }}</span>
    </div>

    <span class="absolute top-1 right-1" v-if="selected">
      <CheckCircleIcon class="h-5 w-5 text-blue-500" aria-hidden="true" />
    </span>
  </button>
</template>
