<script setup lang="ts">
import { onMounted, ref } from 'vue'
import Button from '@/components/Elements/Button.vue'
import InputModal from '@/components/Modals/InputModal.vue'
import { OPERATIONS } from '@/constants'
import { useErrors } from '@/hooks'

interface Props {
  name: string
  onSubmit: (value: string) => void
  from: string
  loading?: boolean
}

const props = defineProps<Props>()

const { invalid, errors } = useErrors(OPERATIONS.RENAME_FOLDER)

//STATE
let value = ref(null as string | null)

// HOOKS
onMounted(() => (value.value = props.from))

// ACTIONS
const submit = () => value.value && props.onSubmit(value.value)
</script>

<template>
  <InputModal :name="name" :on-submit="submit" :title="__('NovaFileManager.renameFolderTitle')">
    <template v-slot:inputs>
      <div>
        <div
          :class="[
            'w-full border rounded-md space-y-2 px-3 py-2 bg-gray-100 dark:bg-gray-900 shadow-sm focus-within:ring-1 focus-within:ring-blue-600 focus-within:border-blue-600',
            !invalid ? 'border-gray-400 dark:border-gray-700' : 'border-red-400 dark:border-red-700',
          ]"
        >
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-200" for="name">
            {{ __('Folder Name') }}
          </label>
          <input
            id="name"
            v-model="value"
            :placeholder="__('Type your folder name here')"
            class="block w-full border-0 p-0 bg-gray-100 dark:bg-gray-900 placeholder-gray-400 sm:text-sm text-black dark:text-white focus:outline-none focus:ring-0"
            name="name"
            type="text"
          />
        </div>
        <template v-if="invalid">
          <ul v-for="(errors, index) in errors" :key="index" class="mt-2 text-sm text-red-600">
            <li v-for="error in errors">
              {{ error }}
            </li>
          </ul>
        </template>
      </div>
    </template>
    <template v-slot:submitButton>
      <Button :disabled="value === from" class="w-full sm:w-auto" type="submit" variant="primary">
        {{ __('Rename Folder') }}
      </Button>
    </template>
    <template v-slot:cancelButton="{ close }">
      <Button class="w-full sm:w-auto" type="button" variant="secondary" @click="close">
        {{ __('Cancel') }}
      </Button>
    </template>
  </InputModal>
</template>
