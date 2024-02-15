<script setup lang="ts">
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
import { Entity } from '__types__'
import { computed, ref } from 'vue'
import CropIcon from '@/components/Elements/CropIcon.vue'
import IconButton from '@/components/Elements/IconButton.vue'
import ImageLoader from '@/components/Elements/ImageLoader.vue'
import BaseModal from '@/components/Modals/BaseModal.vue'
import CropImageModal from '@/components/Modals/CropImageModal.vue'
import DeleteFileModal from '@/components/Modals/DeleteFileModal.vue'
import EditImageModal from '@/components/Modals/EditImageModal.vue'
import RenameFileModal from '@/components/Modals/RenameFileModal.vue'
import { MODALS, PREVIEW_MODAL_NAME, QUEUE_MODAL_NAME } from '@/constants'
import { useClipboard, usePermissions, usePintura } from '@/hooks'
import useBrowserStore from '@/stores/browser'

interface Props {
  file: Entity
  readOnly: boolean
}

const props = withDefaults(defineProps<Props>(), {
  readOnly: false,
})

const store = useBrowserStore()
const { copy: clipboardCopy } = useClipboard()
const { showRenameFile, showDeleteFile, showCropImage, showUnzipFile } = usePermissions()
const { usePinturaEditor } = usePintura()

// STATE
const buttonRef = ref<HTMLButtonElement | HTMLAnchorElement>()
const isCropModalOpened = computed(() => store.isOpen(`crop-image-${props.file?.id}`))
const isEditModalOpened = computed(() => store.isOpen(`edit-image-${props.file?.id}`))
const isField = computed(() => store.isField)

// ACTIONS
const openModal = (name: string) => store.openModal({ name })
const onRename = (value: string) => store.renameFile({ id: props.file.id, from: props.file.path, to: value })
const onDelete = () => store.deleteFiles({ paths: [props.file.path] })
const onUnzip = (path: string) => store.unzipFile({ path })

const selectThenConfirm = () => {
  store.selectFile({ file: props.file })

  store.confirm()
}

const closePreview = () => {
  store.preview = undefined

  store.fixPortal()
}

const onEditImage = (file: File) => {
  closePreview()

  openModal(QUEUE_MODAL_NAME)

  store.upload({ files: [file] })
}

const copy = (file: Entity) => {
  clipboardCopy(file.url)

  window.Nova.success('OK!')
}
</script>

<template>
  <BaseModal as="template" class="nova-file-manager" :name="PREVIEW_MODAL_NAME" :initial-focus-ref="buttonRef">
    <DialogPanel
      class="relative bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full max-w-7xl p-4 flex flex-col gap-4"
    >
      <div class="w-full flex flex-col flex-col-reverse gap-y-2 md:flex-row justify-between items-start">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-400 break-all w-full">
          {{ file?.name }}
        </h2>

        <div class="flex flex-row gap-2 justify-end flex-shrink-0">
          <IconButton
            v-if="!readOnly && showDeleteFile"
            variant="danger"
            @click="openModal(`${MODALS.DELETE_FILES}-${file?.id}`)"
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

          <IconButton @click="copy(file)" variant="secondary" :title="__('NovaFileManager.actions.copy')">
            <ClipboardDocumentIcon class="w-5 h-5" />
          </IconButton>

          <IconButton
            :as-anchor="true"
            :download="`/nova-vendor/nova-file-manager/files/download?path=${file?.path}`"
            :href="`/nova-vendor/nova-file-manager/files/download?path=${file?.path}`"
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

          <IconButton ref="buttonRef" @click="closePreview" :title="__('NovaFileManager.actions.close')">
            <XMarkIcon class="w-5 h-5" />
          </IconButton>

          <IconButton v-if="isField" variant="success" @click="selectThenConfirm">
            <CheckIcon class="h-5 w-5" />
          </IconButton>
        </div>
      </div>

      <div class="overflow-hidden flex flex-col md:flex-row gap-4 w-full">
        <div
          class="block relative w-full md:w-4/6 overflow-hidden rounded-lg bg-gray-500/10 flex items-center justify-center"
        >
          <ImageLoader
            v-if="file?.type === 'image'"
            :src="file.url"
            :is-thumbnail="false"
            :full-width="false"
            class="relative"
            :alt="file.name"
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

          <DocumentIcon v-else class="h-40 w-40 text-gray-500 m-12" />
        </div>

        <div class="w-full md:w-2/6">
          <div>
            <h3 class="font-medium text-gray-800 dark:text-gray-100">
              {{ __('NovaFileManager.preview.information') }}
            </h3>
            <dl
              class="mt-2 divide-y divide-gray-200 dark:divide-gray-800/40 border-t border-b border-gray-300 dark:border-gray-800/70"
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

      <DeleteFileModal v-if="showDeleteFile" :name="`${MODALS.DELETE_FILES}-${file?.id}`" :on-confirm="onDelete" />

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
