<template>
  <TransitionRoot
    as="template"
    :show="isOpen"
    class="nova-file-manager"
  >
    <Dialog
      as="div"
      class="relative z-[60]"
      style="z-index: 999"
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
        <div class="fixed inset-0 bg-gray-800/20 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <div
        class="fixed z-10 inset-0 overflow-y-auto"
        :class="darkMode && 'dark'"
      >
        <div
          class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0"
        >
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
              class="relative bg-gray-200 dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full"
            >
              <form
                @submit.prevent="onSubmit"
                name="input-modal"
              >
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                  <div class="sm:flex sm:items-start">
                    <div class="text-left w-full space-y-6">
                      <DialogTitle
                        as="h3"
                        class="text-lg leading-6 font-medium text-gray-700 dark:text-gray-200"
                      >
                        {{ title }}
                      </DialogTitle>
                      <div class="mt-2 w-full space-y-6">
                        <slot name="inputs" />
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="mt-5 sm:mt-4 px-4 sm:px-6 pb-4 sm:flex sm:flex-row-reverse sm:gap-x-2 space-y-3 sm:space-y-0"
                >
                  <slot name="submitButton" />
                  <slot name="cancelButton" />
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { mapState } from 'vuex'

export default {
  components: {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
  },

  props: ['name', 'title', 'onSubmit'],

  computed: {
    ...mapState('nova-file-manager', ['darkMode']),
    isOpen() {
      return this.$store.getters['nova-file-manager/allModals'].includes(this.name)
    },
  },

  methods: {
    closeModal() {
      this.$store.dispatch('nova-file-manager/closeModal', this.name)
    },
  },

  beforeUnmount() {
    if (this.isOpen) this.closeModal()
  },
}
</script>
