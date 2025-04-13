<script setup lang="ts">
import BaseModal from '@/components/Modals/BaseModal.vue';
import { DialogPanel, DialogTitle } from '@headlessui/vue';

interface Props {
  name: string;
  title: string;
  onSubmit: () => void;
}

defineProps<Props>();
</script>

<template>
  <BaseModal as="template" class="nova-file-manager" :name="name" v-slot="{ close }">
    <DialogPanel
      class="relative bg-gray-200 dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full"
    >
      <form name="input-modal" @submit.prevent="onSubmit">
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="text-left w-full space-y-6">
              <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-700 dark:text-gray-200">
                {{ title }}
              </DialogTitle>
              <div class="mt-2 w-full space-y-6">
                <slot name="inputs" />
              </div>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-4 px-4 sm:px-6 pb-4 sm:flex sm:flex-row-reverse sm:gap-x-2 space-y-3 sm:space-y-0">
          <slot name="submitButton" />
          <slot name="cancelButton" :close="close" />
        </div>
      </form>
    </DialogPanel>
  </BaseModal>
</template>
