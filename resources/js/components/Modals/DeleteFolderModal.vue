<script setup lang="ts">
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'
import Button from '@/components/Elements/Button.vue'
import ConfirmModal from '@/components/Modals/ConfirmModal.vue'

interface Props {
  name: string
  onConfirm: () => void
}

defineProps<Props>()

const icon = computed(() => ExclamationCircleIcon)
</script>

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
