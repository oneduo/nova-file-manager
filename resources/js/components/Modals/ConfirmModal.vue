<script setup lang="ts">
import { DialogPanel, DialogTitle } from '@headlessui/vue'
import { Component, computed } from 'vue'
import { useErrors } from '@/hooks'
import BaseModal from './BaseModal.vue'

const variants = {
  danger: {
    iconBackground: 'bg-red-100 dark:bg-red-800/30',
    iconColor: 'text-red-600 dark:text-red-500',
  },
}

interface Props {
  name: string
  attribute: string
  title: string
  content: string
  icon: Component
  variant: keyof typeof variants
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'danger',
})

const { hasErrors, errorsList } = useErrors(props.attribute)

// STATE
const iconColorClass = computed(() => (props.variant ? variants[props.variant].iconColor : ''))
const iconBackgroundClass = computed(() => (props.variant ? variants[props.variant].iconBackground : ''))
</script>

<template>
  <BaseModal as="template" class="nova-file-manager" :name="name" v-slot="{ close }">
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
          <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
            {{ title }}
          </DialogTitle>
          <div class="mt-2">
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ content }}
            </p>
          </div>
          <template v-if="hasErrors">
            <p v-for="(error, index) in errorsList" :key="index" class="mt-2 text-sm text-red-600">
              {{ error }}
            </p>
          </template>
        </div>
      </div>
      <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-x-2 space-y-3 sm:space-y-0">
        <slot name="confimButton" />
        <slot name="cancelButton" :close="close" />
      </div>
    </DialogPanel>
  </BaseModal>
</template>