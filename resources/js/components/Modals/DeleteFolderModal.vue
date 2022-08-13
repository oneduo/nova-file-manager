<template>
  <ConfirmModal
    :content="
      __(
        'This will will delete the folder from the storage. This action cannot be undone.'
      )
    "
    :icon="icon"
    :name="name"
    :title="__('Are you sure you want to delete this folder?')"
    variant="danger"
  >
    <template v-slot:confimButton>
      <Button
        :icon="false"
        class="w-full sm:w-auto"
        type="button"
        variant="danger"
        @click="onConfirm"
      >
        {{ __('Delete') }}
      </Button>
    </template>
    <template v-slot:cancelButton>
      <Button
        :icon="false"
        class="w-full sm:w-auto"
        type="button"
        variant="secondary"
        @click="closeModal(name)"
      >
        {{ __('Cancel') }}
      </Button>
    </template>
  </ConfirmModal>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import { ExclamationIcon } from '@heroicons/vue/outline'
import ConfirmModal from '@/components/Modals/ConfirmModal'
import Button from '@/components/Elements/Button'

const store = useStore()
defineProps(['name', 'onConfirm'])
const icon = computed(() => ExclamationIcon)
const closeModal = name => store.dispatch('nova-file-manager/closeModal', name)
</script>
