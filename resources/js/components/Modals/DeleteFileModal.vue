<template>
  <ConfirmModal
    :content="__('NovaFileManager.deleteFileContent')"
    :icon="icon"
    :is-open="isOpen"
    :name="name"
    :title="__('NovaFileManager.deleteFileTitle')"
    variant="danger"
    attribute="deleteFile"
  >
    <template v-slot:confimButton>
      <Button class="w-full sm:w-auto" type="button" variant="danger" @click="onConfirm">
        {{ __('Delete') }}
      </Button>
    </template>
    <template v-slot:cancelButton="{ close }">
      <Button class="w-full sm:w-auto" type="button" variant="secondary" @click="close">
        {{ __('Cancel') }}
      </Button>
    </template>
  </ConfirmModal>
</template>

<script setup>
import { computed } from 'vue'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import ConfirmModal from '@/components/Modals/ConfirmModal'
import Button from '@/components/Elements/Button'

defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  name: {
    type: String,
    required: true,
  },
  onConfirm: {
    type: Function,
  },
  onClose: {
    type: Function,
  },
})

const icon = computed(() => ExclamationCircleIcon)
</script>
