<template>
  <div class="nova-file-manager">
    <div :class="darkMode && 'dark'">
      <Head :title="__('NovaFileManager.title')" />

      <Heading class="mb-6">{{ __('NovaFileManager.title') }}</Heading>

      <div class="relative bg-amber-600/10 rounded-md mb-4 text-xs" v-if="config.outdated">
        <div class="mx-auto max-w-7xl py-3 px-3 sm:px-6 lg:px-8">
          <div class="pr-16 sm:px-16 sm:text-center">
            <p class="font-medium text-amber-700 dark:text-amber-500">
              <span class="inline"
                >You are running an outdated version of the package, a new version is
                available.</span
              >
              <span class="block sm:ml-2 sm:inline-block">
                <a
                  href="https://github.com/BBS-Lab/nova-file-manager/releases"
                  class="font-bold text-amber-800 dark:text-amber-400 inline-flex gap-1"
                  target="_blank"
                >
                  Learn more
                  <ArrowTopRightOnSquareIcon class="w-4 h-4 text-amber-800 dark:text-amber-400" />
                </a>
              </span>
            </p>
          </div>
        </div>
      </div>

      <Browser />
    </div>
  </div>
</template>
<script setup>
import { useStore } from 'vuex'
import Browser from '@/components/Browser'
import { computed, onBeforeMount } from 'vue'
import { ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline'

const store = useStore()
const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
})

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)

onBeforeMount(() => {
    store.commit('nova-file-manager/setSelection', [])
    store.commit('nova-file-manager/setLimit', null)
    store.commit('nova-file-manager/init')
    store.commit('nova-file-manager/setIsFieldMode', false)
    store.commit('nova-file-manager/setMultiple', true)
    store.commit('nova-file-manager/setCustomDisk', props.config.customDisk)
    store.dispatch('nova-file-manager/setPermissions', props.config.permissions)
})
</script>
