<template>
  <TransitionRoot :show="isOpen" as="template" class="nova-file-manager">
    <Dialog as="div" class="relative z-[60]" style="z-index: 999" @close="closeModal">
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

      <div :class="darkMode && 'dark'" class="fixed z-10 inset-0 overflow-y-auto">
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
              class="relative bg-gray-100 dark:bg-gray-900 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full sm:p-6"
            >
              <div class="sm:flex sm:items-start">
                <div
                  :class="`${iconBackgroundClass} mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10`"
                >
                  <component :is="icon" :class="`${iconColorClass} h-6 w-6`" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <DialogTitle
                    as="h3"
                    class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                  >
                    {{ title }}
                  </DialogTitle>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ content }}
                    </p>
                  </div>
                  <template v-if="hasErrors">
                    <p
                      v-for="(error, index) in errorsList"
                      :key="index"
                      id="email-error"
                      class="mt-2 text-sm text-red-600"
                    >
                      {{ error }}
                    </p>
                  </template>
                </div>
              </div>
              <div
                class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-x-2 space-y-3 sm:space-y-0"
              >
                <slot name="confimButton" />
                <slot name="cancelButton" />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
const variants = {
    danger: {
        iconBackground: 'bg-red-100 dark:bg-red-800/30',
        iconColor: 'text-red-600 dark:text-red-500',
    },
}
</script>

<script setup>
import { computed, onBeforeUnmount } from 'vue'
import { useStore } from 'vuex'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useErrors } from '@/hooks'

const store = useStore()

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    attribute: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    content: {
        type: String,
        required: true,
    },
    icon: {
        type: Object,
    },
    variant: {
        type: String,
        default: 'danger',
    },
})

const { hasErrors, errorsList } = useErrors(props.attribute)

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)
const isOpen = computed(() => store.getters['nova-file-manager/allModals'].includes(props.name))
const iconColorClass = computed(() => (props.variant ? variants[props.variant].iconColor : null))

const iconBackgroundClass = computed(() =>
    props.variant ? variants[props.variant].iconBackground : ''
)

const closeModal = () => store.dispatch('nova-file-manager/closeModal', props.name)

onBeforeUnmount(() => {
    if (isOpen.value) {
        closeModal()
    }
})
</script>
