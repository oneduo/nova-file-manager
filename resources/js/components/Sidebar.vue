<template>
  <aside
    class="absolute top-0 right-0 h-full w-96 z-10 overflow-y-auto bg-gray-200/80 dark:bg-gray-900/80 backdrop-blur-xl py-4 px-8 lg:block shadow-lg"
  >
    <div class="relative space-y-4">
      <div class="relative z-0 inline-flex shadow-sm rounded-md w-full justify-between">
        <div class="inline-flex gap-x-2">
          <IconButton
            variant="success"
            v-if="isFieldMode"
            @click="closeBrowser"
          >
            <CheckIcon class="w-5 h-5" />
          </IconButton>
          <IconButton
            variant="secondary"
            @click.prevent="setSelectedFile(null)"
          >
            <XIcon class="w-5 h-5" />
          </IconButton>
        </div>
        <div class="inline-flex gap-x-2">
          <IconButton
            variant="secondary"
            :as-anchor="true"
            :href="file.url"
            :download="file.name"
          >
            <DownloadIcon class="w-5 h-5" />
          </IconButton>
          <IconButton
            variant="secondary"
            @click="openModal(`renameFile-${file.id}`)"
          >
            <PencilAltIcon class="w-5 h-5" />
          </IconButton>
          <IconButton
            variant="danger"
            @click="openModal(`deleteFile-${file.id}`)"
          >
            <TrashIcon class="w-5 h-5" />
          </IconButton>
        </div>
      </div>
      <div>
        <div class="relative block w-full">
          <img
            v-if="file.type === 'image'"
            :src="file.url"
            alt=""
            class="rounded-md"
          />
          <template v-else-if="file.type === 'video'">
            <div class="relative">
              <video
                controls="controls"
                class="rounded-md"
              >
                <source :src="file.url" />
                Sorry, your browser doesn't support embedded videos.
              </video>
            </div>
          </template>
        </div>
        <div class="w-full space-y-2 mt-2">
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 break-all">
            {{ file.name }}
          </h2>
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-gray-400">{{ file.size }}</p>
          </div>
        </div>
      </div>
      <div>
        <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ __('Information') }}</h3>
        <dl
          class="mt-2 divide-y divide-gray-200 dark:divide-gray-800 border-t border-b border-gray-400 dark:border-gray-600"
        >
          <div class="flex justify-between py-3 text-sm font-medium">
            <dt class="text-gray-500">{{ __('NovaFileManagerMeta::mime') }}</dt>
            <dd class="text-gray-400 dark:text-gray-600">{{ file.mime }}</dd>
          </div>
          <div class="flex justify-between py-3 text-sm font-medium">
            <dt class="text-gray-500">
              {{ __('NovaFileManagerMeta::lastModifiedAt') }}
            </dt>
            <dd class="text-gray-400 dark:text-gray-600">
              {{ file.lastModifiedAt }}
            </dd>
          </div>
          <template v-for="(value, key) in file.meta">
            <div
              v-if="value"
              class="flex justify-between py-3 text-sm font-medium"
            >
              <dt class="text-gray-500">{{ __(`NovaFileManagerMeta::${key}`) }}</dt>
              <dd class="text-gray-400 dark:text-gray-600">{{ value }}</dd>
            </div>
          </template>
        </dl>
      </div>
    </div>
  </aside>
  <DeleteFileModal
    :name="`deleteFile-${file.id}`"
    :on-confirm="onDelete"
  />

  <RenameFileModal
    :name="`renameFile-${file.id}`"
    :old-name="file.name"
    :on-submit="onRename"
  />
</template>

<script>
import {
  CheckIcon,
  DocumentIcon,
  DownloadIcon,
  EyeIcon,
  PencilAltIcon,
  TrashIcon,
  XIcon,
} from '@heroicons/vue/outline'
import { PlayIcon } from '@heroicons/vue/solid'
import IconButton from '@/components/Elements/IconButton'
import { mapActions, mapMutations, mapState } from 'vuex'
import { TransitionRoot } from '@headlessui/vue'
import DeleteFileModal from '@/components/Modals/DeleteFileModal'
import RenameFileModal from '@/components/Modals/RenameFileModal'

export default {
  name: 'Sidebar',
  components: {
    IconButton,
    DownloadIcon,
    PencilAltIcon,
    TrashIcon,
    EyeIcon,
    XIcon,
    CheckIcon,
    TransitionRoot,
    DocumentIcon,
    PlayIcon,
    DeleteFileModal,
    RenameFileModal,
  },
  props: ['file'],
  computed: {
    ...mapState('nova-file-manager', ['selectedFile', 'isFieldMode']),
  },
  methods: {
    ...mapMutations('nova-file-manager', ['setSelectedFile']),
    ...mapActions('nova-file-manager', ['deleteFile', 'renameFile', 'openModal', 'closeBrowser']),
    onRename(value) {
      this.renameFile({ id: this.file.id, oldPath: this.file.path, newPath: value })
    },
    onDelete() {
      this.deleteFile({ id: this.file.id, path: this.file.path })
    },
  },
}
</script>

<style scoped></style>
