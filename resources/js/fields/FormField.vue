<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <div class="nova-file-manager">
        <div :class="darkMode && 'dark'">
          <div
            class="flex flex-row gap-2 flex-wrap w-full"
            v-if="selection.length > 0"
          >
            <draggable
              v-model="files"
              @start="drag = true"
              @end="drag = false"
              item-key="id"
              class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-2 w-full"
              ghost-class="opacity-0"
            >
              <template #item="{ element }">
                <FieldCard
                  :file="element"
                  mode="form"
                  class="cursor-grab"
                />
              </template>
            </draggable>
          </div>

          <button
            @click="openModal('browser')"
            type="button"
            class="relative flex flex-row shrink-0 items-center px-4 py-2 rounded-md border border-gray-300 dark:hover:border-blue-500 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 focus:z-10 focus:outline-none"
          >
            <CloudIcon
              class="-ml-1 mr-2 h-5 w-5 text-gray-400 dark:text-gray-200"
              aria-hidden="true"
            />
            {{ __('NovaFileManager.openBrowser') }}
          </button>
        </div>
      </div>
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
import { CloudIcon, DocumentIcon, TrashIcon } from '@heroicons/vue/outline'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import Browser from '@/components/Browser'
import ImageLoader from '@/components/ImageLoader'
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { mapActions, mapMutations, mapState } from 'vuex'
import draggable from 'vuedraggable'
import FieldCard from '@/components/Cards/FieldCard'

export default {
  mixins: [FormField, HandlesValidationErrors],

  components: {
    FieldCard,
    Browser,
    CloudIcon,
    DocumentIcon,
    TrashIcon,
    Dialog,
    DialogPanel,
    ImageLoader,
    TransitionChild,
    TransitionRoot,
    draggable,
  },

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      drag: false,
    }
  },

  mounted() {
    this.setIsFieldMode(true)
    this.init()
    this.setSelection(this.field.value?.files ?? [])
    this.setLimit(this.field?.limit ?? 1)
  },

  beforeUnmount() {
    this.destroy()
  },

  computed: {
    ...mapState('nova-file-manager', ['darkMode', 'disk', 'selection']),

    isOpen() {
      return this.$store.getters['nova-file-manager/allModals'].includes('browser')
    },

    files: {
      get() {
        return this.selection
      },
      set(value) {
        this.setSelection(value)
      },
    },

    maxWidth() {
      return this.field.maxWidth || 320
    },
  },

  methods: {
    ...mapActions('nova-file-manager', ['openModal', 'closeModal']),
    ...mapMutations('nova-file-manager', [
      'init',
      'destroy',
      'setSelectedFile',
      'setIsFieldMode',
      'setValue',
      'setLimit',
      'deselectFile',
      'setSelection',
    ]),
    fill(formData) {
      formData.append(
        this.field.attribute,
        JSON.stringify({
          disk: this.disk,
          files: this.selection,
        }),
      )
    },

    remove(file) {
      this.deselectFile(file)
    },
  },
}
</script>
