<template>
  <ConfirmModal
    :content="__('NovaFileManager.deleteFolderContent')"
    :icon="icon"
    :name="name"
    :title="__('NovaFileManager.deleteFolderTitle')"
    variant="danger"
    attribute="deleteFolder"
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
  name: {
    type: String,
    required: true,
  },
  onConfirm: {
    type: Function,
    required: true,
  },
})

const icon = computed(() => ExclamationCircleIcon)
</script>
