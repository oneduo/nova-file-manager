<template>
  <BaseModal as="template" class="nova-file-manager" name="preview">
    <DialogPanel
      class="relative flex flex-col w-full gap-4 p-4 overflow-hidden transition-all transform bg-gray-100 rounded-lg shadow-xl dark:bg-gray-900 max-w-7xl"
    >
      <div
        class="flex flex-col flex-col-reverse items-start justify-between w-full gap-y-2 md:flex-row"
      >
        <h2 class="w-full text-lg font-medium text-gray-900 break-all dark:text-gray-400">
          {{ file?.name }}
        </h2>

        <div class="flex flex-row justify-end flex-shrink-0 gap-2">
          <IconButton
            v-if="!readOnly && showDeleteFile"
            variant="danger"
            @click="openModal(`delete-file-${file?.id}`)"
            :title="__('NovaFileManager.actions.delete')"
          >
            <TrashIcon class="w-5 h-5" />
          </IconButton>
          <IconButton
            v-if="!readOnly && showCropImage && !usePinturaEditor && file?.type === 'image'"
            variant="secondary"
            @click="openModal(`crop-image-${file?.id}`)"
            :title="__('NovaFileManager.actions.cropImage', { image: file?.name })"
          >
            <CropIcon class="w-5 h-5" />
          </IconButton>
          <IconButton
            v-if="!readOnly && showCropImage && usePinturaEditor && file?.type === 'image'"
            variant="secondary"
            @click="openModal(`edit-image-${file?.id}`)"
            :title="__('NovaFileManager.actions.editImage', { image: file?.name })"
          >
            <CropIcon class="w-5 h-5" />
          </IconButton>

          <IconButton
            v-if="!readOnly && showUnzipFile && file?.type === 'zip'"
            variant="secondary"
            @click="onUnzip(file.path)"
            :title="__('NovaFileManager.actions.unzip')"
          >
            <ArchiveBoxIcon class="w-5 h-5" />
          </IconButton>

          <IconButton
            @click="copy(file)"
            variant="secondary"
            :title="__('NovaFileManager.actions.copy')"
          >
            <ClipboardDocumentIcon class="w-5 h-5" />
          </IconButton>

          <IconButton
            :as-anchor="true"
            :download="file?.name"
            :href="file?.url"
            variant="secondary"
            :title="__('NovaFileManager.actions.download')"
          >
            <CloudArrowDownIcon class="w-5 h-5" />
          </IconButton>

          <IconButton
            v-if="!readOnly && showRenameFile"
            variant="secondary"
            @click="openModal(`rename-file-${file?.id}`)"
            :title="__('NovaFileManager.actions.rename')"
          >
            <PencilSquareIcon class="w-5 h-5" />
          </IconButton>

          <IconButton
            v-if="store.isField && !store.multiple"
            variant="success"
            @click="selectFile(file)"
            :disabled="!!limit && selection?.length > limit"
            data-tour="nfm-confirm-selection-button"
          >
            <CheckIcon class="w-5 h-5" />
          </IconButton>

          <IconButton
            ref="buttonRef"
            @click="closePreview"
            :title="__('NovaFileManager.actions.close')"
          >
            <XMarkIcon class="w-5 h-5" />
          </IconButton>
        </div>
      </div>

      <div class="flex flex-col w-full gap-4 overflow-hidden md:flex-row">
        <div
          class="relative flex items-center justify-center block w-full overflow-hidden rounded-lg md:w-4/6 bg-gray-500/10"
        >
          <ImageLoader
            v-if="file?.type === 'image'"
            :src="file.url"
            :is-thumbnail="false"
            :full-width="false"
            class="relative"
          />

          <video
            v-else-if="file?.type === 'video'"
            class="w-full max-w-screen max-h-[80vh] relative"
            controls="controls"
          >
            <source :src="file?.url" />
            Sorry, your browser doesn't support embedded videos.
          </video>

          <embed
            v-else-if="file?.type === 'pdf'"
            :src="file?.url"
            type="application/pdf"
            class="w-full max-w-screen h-[80vh]"
          />

          <DocumentIcon v-else class="w-40 h-40 m-12 text-gray-500" />
        </div>

        <div class="w-full md:w-2/6">
          <div>
            <h3 class="font-medium text-gray-800 dark:text-gray-100">
              {{ __('NovaFileManager.preview.information') }}
            </h3>
            <dl
              class="mt-2 border-t border-b border-gray-300 divide-y divide-gray-200 dark:divide-gray-800/40 dark:border-gray-800/70"
            >
              <div class="flex justify-between py-3 text-sm font-medium">
                <dt class="text-gray-500">
                  {{ __('NovaFileManager.meta.size') }}
                </dt>
                <dd class="text-gray-400 dark:text-gray-600">
                  {{ file?.size }}
                </dd>
              </div>

              <div class="flex justify-between py-3 text-sm font-medium">
                <dt class="text-gray-500">
                  {{ __('NovaFileManager.meta.mime') }}
                </dt>
                <dd class="text-gray-400 dark:text-gray-600">
                  {{ file?.mime }}
                </dd>
              </div>

              <div class="flex justify-between py-3 text-sm font-medium">
                <dt class="text-gray-500">
                  {{ __('NovaFileManager.meta.lastModifiedAt') }}
                </dt>
                <dd class="text-gray-400 dark:text-gray-600">
                  {{ file?.lastModifiedAt }}
                </dd>
              </div>

              <template v-for="(value, key) in file?.meta">
                <div v-if="value" :key="key" class="flex justify-between py-3 text-sm font-medium">
                  <dt class="text-gray-500">
                    {{ __(`NovaFileManager.meta.${key}`) }}
                  </dt>
                  <dd class="text-gray-400 dark:text-gray-600">
                    {{ value }}
                  </dd>
                </div>
              </template>
            </dl>
          </div>
        </div>
      </div>

      <DeleteFileModal
        v-if="showDeleteFile"
        :name="`delete-file-${file?.id}`"
        :on-confirm="onDelete"
      />

      <CropImageModal
        v-if="showCropImage && isCropModalOpened"
        :name="`crop-image-${file?.id}`"
        :file="file"
        :on-confirm="onEditImage"
      />

      <EditImageModal
        v-if="showCropImage && isEditModalOpened"
        :name="`edit-image-${file?.id}`"
        :file="file"
        :on-confirm="onEditImage"
      />

      <RenameFileModal
        v-if="showRenameFile"
        :name="`rename-file-${file?.id}`"
        :from="file?.name"
        :on-submit="onRename"
      />
    </DialogPanel>
  </BaseModal>
</template>

<script setup>
import { computed, ref } from 'vue'
import { DialogPanel } from '@headlessui/vue'
import {
  ArchiveBoxIcon,
  ClipboardDocumentIcon,
  CloudArrowDownIcon,
  DocumentIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
  CheckIcon,
} from '@heroicons/vue/24/outline'
import IconButton from '@/components/Elements/IconButton'
import BaseModal from '@/components/Modals/BaseModal'
import DeleteFileModal from '@/components/Modals/DeleteFileModal'
import RenameFileModal from '@/components/Modals/RenameFileModal'
import CropImageModal from '@/components/Modals/CropImageModal'
import EditImageModal from '@/components/Modals/EditImageModal'
import Entity from '@/types/Entity'
import { useClipboard, usePermissions, usePintura } from '@/hooks'
import CropIcon from '@/components/Elements/CropIcon'
import ImageLoader from '@/components/Elements/ImageLoader'
import { useStore } from '@/store'

const props = defineProps({
  file: {
    type: Entity,
    required: true,
  },
  readOnly: {
    type: Boolean,
    default: false,
  },
})

const store = useStore()
const { copyToClipboard } = useClipboard()
const { showRenameFile, showDeleteFile, showCropImage, showUnzipFile } = usePermissions()
const { usePinturaEditor } = usePintura()

// STATE
const buttonRef = ref(null)
const isCropModalOpened = computed(() => store.isOpen(`crop-image-${props.file?.id}`))
const isEditModalOpened = computed(() => store.isOpen(`edit-image-${props.file?.id}`))

// ACTIONS
const openModal = name => store.openModal({ name })
const onRename = value => store.renameFile({ id: props.file.id, from: props.file.path, to: value })
const onDelete = () => store.deleteFile({ id: props.file.id, path: props.file.path })
const onUnzip = path => store.unzipFile({ path })

const selectFile = file => {
  store.clearSelection()
  store.selectFile({ file })
  store.confirm()
}

const closePreview = () => {
  store.preview = null

  store.fixPortal()
}

const onEditImage = file => {
  closePreview()

  openModal('queue')

  store.upload({ files: [file] })
}

const copy = file => {
  copyToClipboard(file.url)

  Nova.success('Copied !')
}
</script>
