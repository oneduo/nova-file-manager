<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <div
        v-if="!!file"
        class="block w-full overflow-hidden rounded-lg border border-gray-300 dark:border-gray-700 max-w-xs mb-2"
      >
        <img
          v-if="file.type === 'image'"
          :src="file.url"
          alt=""
          class="rounded-md"
        />
        <template v-else-if="file.type === 'video'">
          <div class="relative">
            <video class="rounded-md">
              <source :src="file.url" />
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
      <div class="nova-file-manager">
        <div :class="darkMode && 'dark'">
          <div
            class="relative z-0 flex flex-row items-center shadow-sm rounded-md border border-transparent hover:border-blue-500 w-full"
          >
            <button
              @click="openModal('browser')"
              type="button"
              class="relative flex flex-row shrink-0 items-center px-4 py-2 rounded-l-md border-l border-t border-b border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 focus:z-10 focus:outline-none"
            >
              <CloudIcon
                class="-ml-1 mr-2 h-5 w-5 text-gray-400 dark:text-gray-200"
                aria-hidden="true"
              />
              {{ __('Open File Manager') }}
            </button>
            <input
              v-model="selectedPath"
              class="w-full relative px-3 py-2 rounded-r-md border placeholder-gray-500 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm font-medium text-gray-500 focus:z-10 focus:outline-none"
              readonly
              :placeholder="__('No file selected')"
            />
          </div>
        </div>
      </div>
      <input
        :id="field.attribute"
        type="hidden"
        class="w-full form-control form-input form-input-bordered"
        :class="errorClasses"
        :placeholder="field?.name"
        v-model="selectedPath"
      />
    </template>
  </DefaultField>

  <TransitionRoot
    as="template"
    :show="isOpen"
    class="nova-file-manager w-full"
  >
    <Dialog
      as="div"
      class="relative"
      @close="closeModal('browser')"
    >
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
        class="z-[60]"
      >
        <div class="fixed inset-0 bg-gray-800/20 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <div :class="`fixed z-[60] inset-0 overflow-y-auto w-full ${darkMode && 'dark'}`">
        <div class="flex items-start justify-center min-h-full">
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
              class="relative bg-transparent rounded-lg overflow-hidden shadow-xl transition-all w-full border border-gray-500 dark:border-gray-600 md:m-8 m-0"
            >
              <Browser class="w-full" />
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { CloudIcon, DocumentIcon } from '@heroicons/vue/outline'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import Browser from '@/components/Browser'
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { mapActions, mapMutations, mapState } from 'vuex'

export default {
  name: 'FormField',
  mixins: [FormField, HandlesValidationErrors],
  components: {
    Browser,
    CloudIcon,
    DocumentIcon,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
  },
  props: ['resourceName', 'resourceId', 'field', 'showHelpText'],
  mounted() {
    this.setIsFieldMode(true)
    this.init()
  },
  beforeUnmount() {
    this.destroy()
  },
  computed: {
    ...mapState('nova-file-manager', ['darkMode', 'disk', 'fieldValue']),
    isOpen() {
      return this.$store.getters['nova-file-manager/allModals'].includes('browser')
    },
    selectedPath() {
      return this.fieldValue?.path || this.field?.value?.file?.path
    },
    file() {
      return this.fieldValue || this.field?.value?.file
    },
  },
  methods: {
    ...mapActions('nova-file-manager', ['openModal', 'closeModal']),
    ...mapMutations('nova-file-manager', ['init', 'destroy', 'setSelectedFile', 'setIsFieldMode']),
    fill(formData) {
      formData.append(
        this.field.attribute,
        JSON.stringify({
          disk: this.disk,
          path: this.selectedPath,
        }),
      )
    },
  },
}
</script>
