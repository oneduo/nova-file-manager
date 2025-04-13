<script setup lang="ts">
import Button from '@/components/Elements/Button.vue';
import ConfirmModal from '@/components/Modals/ConfirmModal.vue';
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';

interface Props {
  isOpen?: boolean;
  name: string;
  onConfirm: () => void;
  count?: number;
}

withDefaults(defineProps<Props>(), {
  isOpen: false,
});

const icon = computed(() => ExclamationCircleIcon);
</script>

<template>
  <ConfirmModal
    :content="__('NovaFileManager.deleteFileContent')"
    :icon="icon"
    :is-open="isOpen"
    :name="name"
    :title="__('NovaFileManager.deleteFileTitle', { count })"
    variant="danger"
    attribute="deleteFile"
  >
    <template v-slot:confirmButton>
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
