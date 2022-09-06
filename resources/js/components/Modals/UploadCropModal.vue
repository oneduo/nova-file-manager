<template>
  <InputModal :name="name" :on-submit="submit" :title="__('NovaFileManager.uploadCropTitle')">
    <template v-slot:inputs>
      <div class="rounded-xl overflow-auto py-4">
        <div class="relative rounded-lg text-center overflow-hidden w-full">
          <div class="absolute inset-0 opacity-50 bg-stripes bg-stripes-gray-700"></div>
          <div
            v-if="item"
            class="absolute z-40 inset-0 flex justify-center items-center w-full h-full"
          >
            <Spinner class="w-16 h-16" v-if="item.status === null" />
            <ExclamationCircleIcon class="w-16 h-16 text-red-500" v-else-if="item.status === false" />
            <CheckCircleIcon class="w-16 h-16 text-green-500" v-else-if="item.status === true" />
          </div>

          <div
            class="absolute inset-0 w-full h-full bg-gray-100/50 dark:bg-gray-800/50"
            v-if="item"
          />

          <div
            class="absolute inset-0 flex flex-row items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-100"
            v-if="item"
          >
            <span>{{ item.ratio }}%</span>
          </div>
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
const props = defineProps(['file', 'name', 'onSubmit', 'data'])
const value = ref(null)
const item = computed(() => {
  return store.state['nova-file-manager'].queue.find((file) => file.id === props.file.id)
})

onMounted(() => {
  value.value = props.data?.name
})

const image = computed(() => URL.createObjectURL(props.data?.blob))

const { hasErrors, errorsList } = useErrors('uploadCrop')

const closeModal = name => store.dispatch('nova-file-manager/closeModal', name)

const submit = () => props.onSubmit(value.value)
</script>
