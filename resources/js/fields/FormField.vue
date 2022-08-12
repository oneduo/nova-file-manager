<template>
  <DefaultField :errors="errors" :field="field" :show-help-text="showHelpText">
    <template #field>
      <div class="nova-file-manager">
        <div :class="darkMode && 'dark'">
          <div
            v-if="files?.length > 0"
            class="flex flex-row gap-2 flex-wrap w-full"
          >
            <draggable
              v-model="files"
              class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-2 w-full"
              ghost-class="opacity-0"
              item-key="id"
              @end="drag = false"
              @start="drag = true"
            >
              <template #item="{ element }">
                <FieldCard
                  :field="field"
                  :file="element"
                  class="cursor-grab"
                  mode="form"
                />
              </template>
            </draggable>
          </div>

          <button
            class="relative flex flex-row shrink-0 items-center px-4 py-2 rounded-md border border-gray-300 dark:hover:border-blue-500 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 focus:z-10 focus:outline-none"
            type="button"
            @click="openBrowser"
          >
            <CloudIcon
              aria-hidden="true"
              class="-ml-1 mr-2 h-5 w-5 text-gray-400 dark:text-gray-200"
            />
            {{ __('NovaFileManager.openBrowser') }}
          </button>
        </div>
      </div>
    </template>
  </DefaultField>

  <TransitionRoot
    v-if="displayModal"
    :show="isOpen"
    as="template"
    class="nova-file-manager w-full"
  >
    <Dialog as="div" class="relative" @close="closeBrowser">
      <TransitionChild
        as="template"
        class="z-[60]"
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
        :class="`fixed z-[60] inset-0 overflow-y-auto w-full ${
          darkMode && 'dark'
        }`"
      >
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
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import Browser from '@/components/Browser'
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
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
        TransitionChild,
        TransitionRoot,
        draggable,
    },

    props: ['resourceName', 'resourceId', 'field'],

    data: () => ({
        drag: false,
        displayModal: false,
    }),

    mounted() {
        this.init()
        this.setIsFieldMode(true)
        this.initField({
            attribute: this.field.attribute,
            limit: this.field.limit || null,
            selection: this.field.value?.files || [],
        })
    },

    beforeUnmount() {
        this.destroy()
    },

    computed: {
        ...mapState('nova-file-manager', ['darkMode', 'disk', 'currentField']),
        ...mapGetters('nova-file-manager', ['fieldByAttribute', 'allModals']),
        isOpen() {
            return this.allModals?.includes('browser')
        },

        files: {
            get() {
                return this.fieldByAttribute(this.field.attribute)?.selection
            },
            set(value) {
                this.setFieldSelection({
                    attribute: this.field.attribute,
                    value,
                })
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
            'initField',
            'setCurrentField',
            'destroy',
            'setSelectedFile',
            'setIsFieldMode',
            'setValue',
            'deselectFieldFile',
        ]),
        fill(formData) {
            if (this.files?.length) {
                formData.append(
                    this.field.attribute,
                    JSON.stringify({
                        disk: this.disk,
                        files: this.files || [],
                    })
                )
            }
        },

        openBrowser() {
            this.displayModal = true
            this.setCurrentField(this.field.attribute)
            this.openModal('browser')
        },

        closeBrowser() {
            this.displayModal = false
            this.setCurrentField(null)
            this.closeModal('browser')
        },
    },

    watch: {
        isOpen(newValue, oldValue) {
            if (!newValue && oldValue) {
                this.displayModal = false
            }
        },
    },
}
</script>
