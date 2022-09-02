<template>
  <ConfirmModal
    :content="__('NovaFileManager.deleteFileContent')"
    :icon="icon"
    :is-open="isOpen"
    :name="name"
    :title="__('NovaFileManager.deleteFileTitle')"
    variant="danger"
  >
    <template v-slot:confimButton>
      <Button class="w-full sm:w-auto" type="button" variant="danger" @click="onConfirm">
        {{ __('Delete') }}
      </Button>
    </template>
    <template v-slot:cancelButton>
      <Button class="w-full sm:w-auto" type="button" variant="secondary" @click="closeModal(name)">
        {{ __('Cancel') }}
      </Button>
    </template>
  </ConfirmModal>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import ConfirmModal from '@/components/Modals/ConfirmModal'
import Button from '@/components/Elements/Button'

const store = useStore()

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
const closeModal = name => store.dispatch('nova-file-manager/closeModal', name)
</script>
