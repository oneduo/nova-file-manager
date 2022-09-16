<template>
  <div class="relative bg-amber-600/10 rounded-md mb-4 text-xs" id="nfm-update-checker">
    <div class="mx-auto max-w-7xl py-3 px-3 sm:px-6 lg:px-8">
      <div class="pr-16 sm:px-16 sm:text-center">
        <p class="font-medium text-amber-700 dark:text-amber-500">
          <span class="inline">{{
            __('You are running an outdated version of the package, a new version is available.')
          }}</span>
          <span class="block sm:ml-2 sm:inline-block">
            <a
              href="https://github.com/BBS-Lab/nova-file-manager/releases"
              class="font-bold text-amber-800 dark:text-amber-400 inline-flex gap-1"
              target="_blank"
            >
              {{ __('Learn more') }}
              <ArrowTopRightOnSquareIcon class="w-4 h-4 text-amber-800 dark:text-amber-400" />
            </a>
          </span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline'
</script>