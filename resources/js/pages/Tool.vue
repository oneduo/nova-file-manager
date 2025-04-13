<script setup lang="ts">
import Browser from '@/components/Browser.vue';
import UpdateChecker from '@/components/Elements/UpdateChecker.vue';
import { useTranslation } from '@/hooks';
import useBrowserStore from '@/stores/browser';
import { Config } from '__types__';
import { computed, onBeforeMount } from 'vue';

const store = useBrowserStore();
const { __ } = useTranslation();

type Props = {
  config: Config;
};

const props = defineProps<Props>();

const dark = computed(() => store.dark);

onBeforeMount(() => {
  store.prepareTool({
    singleDisk: props.config.singleDisk,
    permissions: props.config.permissions,
    tour: props.config.tour,
    usePintura: props.config.usePintura || false,
    pinturaOptions: props.config.pinturaOptions || {},
    cropperOptions: props.config.cropperOptions || {},
    paginationOptions: props.config.paginationOptions || undefined,
  });

  store.loadFromQueryString();
});
</script>

<template>
  <div class="nova-file-manager">
    <div :class="{ dark }">
      <Head :title="__('NovaFileManager.title')">
        <title>{{ __('NovaFileManager.title') }}</title>
      </Head>

      <Heading class="mb-6" data-tour="nfm-tool-title">{{ __('NovaFileManager.title') }}</Heading>

      <UpdateChecker v-if="config.outdated" />

      <Browser />
    </div>
  </div>
</template>
