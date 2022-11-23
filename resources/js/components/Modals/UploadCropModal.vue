<script setup lang="ts">
import { Entity } from '__types__'
import { computed, onMounted, ref } from 'vue'
import Button from '@/components/Elements/Button.vue'
import InputModal from '@/components/Modals/InputModal.vue'
import useBrowserStore from '@/stores/browser'

interface Props {
  file: Entity
  name: string
  onSubmit: (value: string) => void
  destFile: File
  destName?: string
}

const props = defineProps<Props>()

const store = useBrowserStore()

const value = ref<string | null>(null)

onMounted(() => {
  value.value = props.destName ?? ''
})

const image = computed(() => URL.createObjectURL(props.destFile))

// ACTIONS
const closeModal = (name: string) => store.closeModal({ name })
const submit = () => value.value && props.onSubmit(value.value)
</script>

<template>
  <InputModal :name="name" :on-submit="submit" :title="__('NovaFileManager.uploadCropTitle')">
    <template v-slot:inputs>
      <div class="rounded-md overflow-auto">
        <div class="relative rounded-md text-center overflow-hidden w-full">
          <div class="absolute inset-0 opacity-50 bg-stripes bg-stripes-gray-400"></div>
          <img class="relative z-10 object-contain h-48 w-full" :src="image" :alt="name" />
        </div>
      </div>
      <div>
        <div
          class="w-full border rounded-md space-y-2 px-3 py-2 bg-gray-100 dark:bg-gray-900 shadow-sm focus-within:ring-1 focus-within:ring-blue-600 focus-within:border-blue-600 border-gray-400 dark:border-gray-700"
        >
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-200" for="name">
            {{ __('Name') }}
          </label>
          <input
            id="name"
            v-model="value"
            :placeholder="__('NovaFileManager.actions.uploadCrop')"
            class="block w-full border-0 p-0 bg-gray-100 dark:bg-gray-900 placeholder-gray-400 sm:text-sm text-black dark:text-white focus:outline-none focus:ring-0"
            name="name"
            type="text"
          />
        </div>
        <p class="mt-2 text-xs text-gray-400" id="name-description">
          {{ __('NovaFileManager.edit.originalName', { name: file.name }) }}
        </p>
      </div>
    </template>
    <template v-slot:submitButton>
      <Button :disabled="!value" class="w-full sm:w-auto" type="submit" variant="primary">
        {{ __('NovaFileManager.actions.upload') }}
      </Button>
    </template>
    <template v-slot:cancelButton>
      <Button class="w-full sm:w-auto" type="reset" variant="secondary" @click="closeModal(name)">
        {{ __('Cancel') }}
      </Button>
    </template>
  </InputModal>
</template>
