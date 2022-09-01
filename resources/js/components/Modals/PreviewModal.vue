<template>
  <TransitionRoot :show="isOpen" as="template" class="nova-file-manager">
    <Dialog :initial-focus="buttonRef" as="div" class="relative z-[60]" @close="closeModal">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-800/20 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <div :class="['fixed z-10 inset-0 overflow-y-auto', darkMode ? 'dark' : '']">
        <div class="flex items-center justify-center min-h-full p-4">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              class="relative bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full max-w-7xl p-4 flex flex-col gap-4"
            >
              <div
                class="w-full flex flex-col flex-col-reverse gap-y-2 md:flex-row justify-between"
              >
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-400 break-all w-full">
                  {{ file.name }}
                </h2>

                <div class="flex flex-row gap-2 justify-end">
                  <IconButton
                    v-if="!readOnly"
                    variant="danger"
                    @click="openModal(`delete-file-${file.id}`)"
                    :title="__('NovaFileManager.actions.delete')"
                  >
                    <TrashIcon class="w-5 h-5" />
                  </IconButton>

                  <IconButton
                    v-if="readOnly"
                    @click="copy(file)"
                    variant="secondary"
                    :title="__('NovaFileManager.actions.copy')"
                  >
                    <ClipboardDocumentIcon class="w-5 h-5" />
                  </IconButton>

                  <IconButton
                    :as-anchor="true"
                    :download="file.name"
                    :href="file.url"
                    variant="secondary"
                    :title="__('NovaFileManager.actions.download')"
                  >
                    <CloudArrowDownIcon class="w-5 h-5" />
                  </IconButton>

                  <IconButton
                    v-if="!readOnly"
                    variant="secondary"
                    @click="openModal(`rename-file-${file.id}`)"
                    :title="__('NovaFileManager.actions.rename')"
                  >
                    <PencilSquareIcon class="w-5 h-5" />
                  </IconButton>

                  <IconButton
                    ref="buttonRef"
                    @click="closeModal"
                    :title="__('NovaFileManager.actions.close')"
                  >
                    <XMarkIcon class="w-5 h-5" />
                  </IconButton>
                </div>
              </div>

              <div class="overflow-hidden flex flex-col md:flex-row gap-4 w-full">
                <div
                  class="block w-full md:w-4/6 overflow-hidden rounded-lg bg-gray-500/10 flex items-center justify-center"
                >
                  <img
                    v-if="file.type === 'image'"
                    :src="file.url"
                    :alt="file.name"
                    class="object-cover"
                  />

                  <div v-else-if="file.type === 'video'" class="w-full h-full">
                    <video class="w-full max-w-screen max-h-screen" controls="controls">
                      <source :src="file.url" />
                      Sorry, your browser doesn't support embedded videos.
                    </video>
                  </div>

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
                          {{ file.size }}
                        </dd>
                      </div>

                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-500">
                          {{ __('NovaFileManager.meta.mime') }}
                        </dt>
                        <dd class="text-gray-400 dark:text-gray-600">
                          {{ file.mime }}
                        </dd>
                      </div>

                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-500">
                          {{ __('NovaFileManager.meta.lastModifiedAt') }}
                        </dt>
                        <dd class="text-gray-400 dark:text-gray-600">
                          {{ file.lastModifiedAt }}
                        </dd>
                      </div>

                      <template v-for="(value, key) in file.meta">
                        <div
                          v-if="value"
                          :key="key"
                          class="flex justify-between py-3 text-sm font-medium"
                        >
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
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>

  <DeleteFileModal :name="`delete-file-${file.id}`" :on-confirm="onDelete" />

  <RenameFileModal :name="`rename-file-${file.id}`" :old-name="file.name" :on-submit="onRename" />
</template>

<script setup>
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
    ClipboardDocumentIcon,
    CloudArrowDownIcon,
    DocumentIcon,
    PencilSquareIcon,
    TrashIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'
import IconButton from '@/components/Elements/IconButton'
import DeleteFileModal from '@/components/Modals/DeleteFileModal'
import RenameFileModal from '@/components/Modals/RenameFileModal'
import Entity from '@/types/Entity'
import { useClipboard } from '@/hooks'

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
const buttonRef = ref(null)

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)
const preview = computed(() => store.state['nova-file-manager'].preview)
const isOpen = computed(() => preview.value?.id === props.file.id)

const openModal = name => {
    return store.dispatch('nova-file-manager/openModal', name)
}

const closeModal = () => {
    store.commit('nova-file-manager/previewFile', null)
    store.commit('nova-file-manager/fixPortal')
}

const onRename = value => {
    store.dispatch('nova-file-manager/renameFile', {
        id: props.file.id,
        oldPath: props.file.path,
        newPath: value,
    })
}

const onDelete = () => {
    store.dispatch('nova-file-manager/deleteFile', {
        id: props.file.id,
        path: props.file.path,
    })
}

const copy = file => {
    copyToClipboard(file.url)

    Nova.success('Copied !')
}
</script>
