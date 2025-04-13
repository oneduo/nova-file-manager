<script setup lang="ts">
import Button from '@/components/Elements/Button.vue';
import InputModal from '@/components/Modals/InputModal.vue';
import { OPERATIONS } from '@/constants';
import { useErrors } from '@/hooks';
import { onMounted, ref } from 'vue';

interface Props {
  name: string;
  onSubmit: (value: string) => void;
  loading?: boolean;
}

const props = defineProps<Props>();

const value = ref<string>();

onMounted(() => (value.value = undefined));

const { invalid, errors } = useErrors(OPERATIONS.CREATE_FOLDER);

const submit = () => {
  if (!value.value) {
    return;
  }

  props.onSubmit(value.value);

  value.value = undefined;
};
</script>

<template>
  <InputModal :name="name" :on-submit="submit" :title="__('NovaFileManager.createFolderTitle')">
    <template v-slot:inputs>
      <div
        :class="[
          'w-full border rounded-md space-y-2 px-3 py-2 bg-gray-100 dark:bg-gray-900 shadow-sm focus-within:ring-1 focus-within:ring-blue-600 focus-within:border-blue-600',
          !invalid ? 'border-gray-400 dark:border-gray-700' : 'border-red-400 dark:border-red-700',
        ]"
      >
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-200" for="name">
          {{ __('Name') }}
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
        <ul v-for="(entry, index) in errors" :key="`entry_${index}`" class="mt-2 text-sm text-red-600">
          <li v-for="(error, errorIndex) in entry" :key="`error_${errorIndex}`">
            {{ error }}
          </li>
        </ul>
      </template>
    </template>

    <template v-slot:submitButton>
      <Button :disabled="!value" class="w-full sm:w-auto" type="submit" variant="primary" :loading="loading">
        {{ __('Create') }}
      </Button>
    </template>

    <template v-slot:cancelButton="{ close }">
      <Button class="w-full sm:w-auto" type="reset" variant="secondary" @click="close">
        {{ __('Cancel') }}
      </Button>
    </template>
  </InputModal>
</template>
