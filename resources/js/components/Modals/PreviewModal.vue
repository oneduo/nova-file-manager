<template>
  <TransitionRoot :show="isOpen" as="template" class="nova-file-manager">
    <Dialog
      :initial-focus="completeButtonRef"
      as="div"
      class="relative z-[60]"
      @close="closeModal"
    >
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div
          class="fixed inset-0 bg-gray-800/20 backdrop-blur-sm transition-opacity"
        />
      </TransitionChild>

      <div
        :class="darkMode && 'dark'"
        class="fixed z-10 inset-0 overflow-y-auto"
      >
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
                <h2
                  class="text-lg font-medium text-gray-900 dark:text-gray-400 break-all w-full"
                >
                  {{ file.name }}
                </h2>
                <div class="flex flex-row gap-2 justify-end">
                  <IconButton
                    v-if="!withoutActions"
                    tabindex="0"
                    variant="danger"
                    @click="openModal(`deleteFile-${file.id}`)"
                  >
                    <TrashIcon class="w-5 h-5"/>
                  </IconButton>
                  <IconButton
                    :as-anchor="true"
                    :download="file.name"
                    :href="file.url"
                    tabindex="1"
                    variant="secondary"
                  >
                    <cloud-download-icon class="w-5 h-5"/>
                  </IconButton>
                  <IconButton
                    v-if="!withoutActions"
                    variant="secondary"
                    @click="openModal(`renameFile-${file.id}`)"
                  >
                    <pencil-alt-icon class="w-5 h-5"/>
                  </IconButton>
                  <IconButton
                    ref="completeButtonRef"
                    tabindex="1"
                    @click="closeModal"
                  >
                    <x-icon class="w-5 h-5"/>
                  </IconButton>
                </div>
              </div>
              <div
                class="overflow-hidden flex flex-col md:flex-row gap-4 w-full"
              >
                <div
                  class="block w-full md:w-4/6 overflow-hidden rounded-lg bg-gray-500/10 flex items-center justify-center"
                >
                  <img
                    v-if="file.type === 'image'"
                    :src="file.url"
                    alt=""
                    class="object-cover"
                  />
                  <div v-else-if="file.type === 'video'" class="w-full h-full">
                    <video
                      class="w-full max-w-screen max-h-screen"
                      controls="controls"
                    >
                      <source :src="file.url"/>
                      Sorry, your browser doesn't support embedded videos.
                    </video>
                  </div>

                  <DocumentIcon v-else class="h-40 w-40 text-gray-500 m-12"/>
                </div>
                <div class="w-full md:w-2/6">
                  <div>
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">
                      {{ __('Information') }}
                    </h3>
                    <dl
                      class="mt-2 divide-y divide-gray-200 dark:divide-gray-800/40 border-t border-b border-gray-300 dark:border-gray-800/70"
                    >
                      <div
                        class="flex justify-between py-3 text-sm font-medium"
                      >
                        <dt class="text-gray-500">
                          {{ __('NovaFileManager.meta.size') }}
                        </dt>
                        <dd class="text-gray-400 dark:text-gray-600">
                          {{ file.size }}
                        </dd>
                      </div>
                      <div
                        class="flex justify-between py-3 text-sm font-medium"
                      >
                        <dt class="text-gray-500">
                          {{ __('NovaFileManager.meta.mime') }}
                        </dt>
                        <dd class="text-gray-400 dark:text-gray-600">
                          {{ file.mime }}
                        </dd>
                      </div>
                      <div
                        class="flex justify-between py-3 text-sm font-medium"
                      >
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

  <DeleteFileModal :name="`deleteFile-${file.id}`" :on-confirm="onDelete"/>

  <RenameFileModal
    :name="`renameFile-${file.id}`"
    :old-name="file.name"
    :on-submit="onRename"
  />
</template>

<script setup>
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot, } from '@headlessui/vue'
import { CloudDownloadIcon, DocumentIcon, PencilAltIcon, TrashIcon, XIcon, } from '@heroicons/vue/outline'
import IconButton from '@/components/Elements/IconButton'
import DeleteFileModal from '@/components/Modals/DeleteFileModal'
import RenameFileModal from '@/components/Modals/RenameFileModal'

const store = useStore()
const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
    withoutActions: {
        type: Boolean,
        default: false,
    },
})

const completeButtonRef = ref(null)

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)
const preview = computed(() => store.state['nova-file-manager'].preview)
const isOpen = computed(() => preview.value?.id === props.file.id)

const openModal = name => store.dispatch('nova-file-manager/openModal', name)
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
</script>
