<template>
  <InputModal :name="name" :on-submit="submit" :title="__('NovaFileManager.uploadCropTitle')">
    <template v-slot:inputs>
      <div class="rounded-md overflow-auto">
        <div class="relative rounded-md text-center overflow-hidden w-full">
          <div class="absolute inset-0 opacity-50 bg-stripes bg-stripes-gray-400"></div>
          <img class="relative z-10 object-contain h-48 w-full" :src="image">
        </div>
      </div>
      <div>
        <div
          :class="[
            'w-full border rounded-md space-y-2 px-3 py-2 bg-gray-100 dark:bg-gray-900 shadow-sm focus-within:ring-1 focus-within:ring-blue-600 focus-within:border-blue-600',
            !hasErrors
              ? 'border-gray-400 dark:border-gray-700'
              : 'border-red-400 dark:border-red-700',
          ]"
        >
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-200" for="name">
            {{ __('Name') }}
          </label>
          <input
            id="name"
            v-model="value"
            :placeholder="__('Type your cropped image name here')"
            class="block w-full border-0 p-0 bg-gray-100 dark:bg-gray-900 placeholder-gray-400 sm:text-sm text-black dark:text-white focus:outline-none focus:ring-0"
            name="name"
            type="text"
          />
        </div>
        <template v-if="hasErrors">
          <p
            v-for="(error, index) in errorsList"
            :key="index"
            id="email-error"
            class="mt-2 text-sm text-red-600"
          >
            {{ error }}
          </p>
        </template>
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

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useStore } from 'vuex'
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  PlayIcon,
  XCircleIcon,
} from '@heroicons/vue/24/solid'
import Spinner from '@/components/Elements/Spinner'
import Button from '@/components/Elements/Button'
import InputModal from '@/components/Modals/InputModal'
import { useErrors } from '@/hooks'
import entityTransformer from '@/transformers/entityTransformer'
import File from '@/components/Cards/File'

const store = useStore()
const props = defineProps(['name', 'onSubmit', 'data'])
const value = ref(null)

onMounted(() => {
  value.value = props.data?.name
})

const image = computed(() => URL.createObjectURL(props.data?.blob))

const { hasErrors, errorsList } = useErrors('uploadCrop')

const closeModal = name => store.dispatch('nova-file-manager/closeModal', name)

const submit = () => props.onSubmit(value.value)
</script>
