<template>
  <ConfirmModal
    :name="name"
    :is-open="isOpen"
    :on-close="onClose"
    :icon="icon"
    :title="__('Are you sure you want to delete this file?')"
    :content="__('This will will delete the file from the storage. This action cannot be undone.')"
    variant="danger"
  >
    <template v-slot:confimButton>
      <Button
        type="button"
        variant="danger"
        :icon="false"
        @click="onConfirm"
        class="w-full sm:w-auto"
      >
        {{ __('Delete') }}
      </Button>
    </template>
    <template v-slot:cancelButton>
      <Button
        type="button"
        variant="secondary"
        :icon="false"
        @click="closeModal(name)"
        class="w-full sm:w-auto"
      >
        {{ __('Cancel') }}
      </Button>
    </template>
  </ConfirmModal>
</template>

<script>
import { ExclamationIcon } from '@heroicons/vue/outline'
import ConfirmModal from '@/components/Modals/ConfirmModal'
import Button from '@/components/Elements/Button'
import { mapActions } from 'vuex'

export default {
  components: {
    ConfirmModal,
    Button,
    ExclamationIcon,
  },

  props: {
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
      default: () => {},
    },
  },

  computed: {
    icon() {
      return ExclamationIcon
    },
  },

  methods: {
    ...mapActions('nova-file-manager', ['closeModal']),
  }
}
</script>
