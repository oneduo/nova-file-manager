<script setup lang="ts">
import File from '@/components/Cards/File.vue';
import BaseModal from '@/components/Modals/BaseModal.vue';
import nativeFileToEntity from '@/helpers/transformers';
import useBrowserStore from '@/stores/browser';
import { DialogPanel } from '@headlessui/vue';
import { computed } from 'vue';

const store = useBrowserStore();

interface Props {
  name: string;
}

defineProps<Props>();

const queue = computed(() => store.queue);
</script>

<template>
  <BaseModal as="template" class="nova-file-manager" :name="name">
    <DialogPanel
      class="relative bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 max-w-4xl mx-auto"
    >
      <div class="flex flex-col justify-center gap-6 rounded-md px-6 pt-5 pb-6">
        <div class="w-full flex flex-row justify-between items-center">
          <h1 class="text-xs uppercase text-gray-400 font-bold">Queue</h1>
        </div>
        <ul class="grid grid-cols-2 md:grid-cols-4 content-start gap-6">
          <template v-for="item in queue" :key="item.id">
            <File
              :file="nativeFileToEntity(item.file)"
              :is-uploading="true"
              :is-uploaded="item.status"
              :upload-ratio="item.ratio"
              :selected="false"
              class="cursor-default"
            />
          </template>
        </ul>
      </div>
    </DialogPanel>
  </BaseModal>
</template>
