<template>
  <div class="flex flex-col gap-y-4 pt-4">
    <div
      class="flex items-center justify-between flex-wrap sm:flex-nowrap gap-2 gap-y-2 flex-col-reverse sm:flex-row"
    >
      <div class="flex flex-row items-center gap-2 w-full flex-wrap sm:flex-nowrap">
        <DiskSelector v-if="!customDisk" :disk="disk" :disks="disks" :set-disk="setDisk" />
        <PaginationSelector
          :per-page="Number(perPage)"
          :per-page-options="perPageOptions"
          :set-per-page="setPerPage"
        />
        <ViewToggle :current="view" :set-view="setView" />
        <ToolbarSearch />
      </div>
      <div class="flex flex-row gap-x-2 justify-end w-full md:w-auto flex-shrink-0">
        <div class="p-2 rounded-md font-semibold text-xs text-gray-400" v-if="selection?.length">
          <span
            :class="[
              'text-blue-500',
              limit !== null && selection.length > limit ? 'text-red-500' : '',
            ]"
          >
            {{ selection.length }}
          </span>
          <template v-if="!!limit">/{{ limit }}</template>
          {{ __('NovaFileManager.toolbar.selection') }}
          <button @click="clearSelection" class="underline">
            {{ __('NovaFileManager.toolbar.clear') }}
          </button>
        </div>
        <IconButton v-if="showCreateFolder" @click="openModal('create-folder')">
          <FolderPlusIcon class="w-5 h-5" />
        </IconButton>
        <IconButton v-if="showUploadFile" variant="primary" @click="openModal('upload')">
          <CloudArrowUpIcon class="h-5 w-5" />
        </IconButton>
        <IconButton
          v-if="isFieldMode"
          variant="success"
          @click="submitFieldSelection"
          :disabled="!!limit && selection?.length > limit"
        >
          <CheckIcon class="h-5 w-5" />
        </IconButton>
      </div>
    </div>
    <Breadcrumbs :items="breadcrumbs" :set-path="setPath" />
  </div>

  <UploadModal v-if="showUploadFile" name="upload" />

  <CreateFolderModal v-if="showCreateFolder" :on-submit="createFolder" name="create-folder" />
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import { CheckIcon, CloudArrowUpIcon, FolderPlusIcon } from '@heroicons/vue/24/outline'
import DiskSelector from '@/components/DiskSelector'
import PaginationSelector from '@/components/Elements/PaginationSelector'
import Breadcrumbs from '@/components/Breadcrumbs'
import IconButton from '@/components/Elements/IconButton'
import ToolbarSearch from '@/components/Elements/ToolbarSearch'
import ViewToggle from '@/components/Elements/ViewToggle'
import UploadModal from '@/components/Modals/UploadModal'
import CreateFolderModal from '@/components/Modals/CreateFolderModal'
import { usePermissions } from '@/hooks'

const store = useStore()

const { showCreateFolder, showUploadFile } = usePermissions()

const disk = computed(() => store.state['nova-file-manager'].disk)
const disks = computed(() => store.state['nova-file-manager'].disks)
const view = computed(() => store.state['nova-file-manager'].view)
const perPage = computed(() => store.state['nova-file-manager'].perPage)
const perPageOptions = computed(() => store.state['nova-file-manager'].perPageOptions)
const isFieldMode = computed(() => store.state['nova-file-manager'].isFieldMode)
const breadcrumbs = computed(() => store.state['nova-file-manager'].breadcrumbs)
const limit = computed(() => store.state['nova-file-manager'].limit)
const customDisk = computed(() => store.state['nova-file-manager'].customDisk)
const selection = computed(() => store.getters['nova-file-manager/selection'])

const setDisk = disk => store.dispatch('nova-file-manager/setDisk', disk)
const setPerPage = perPage => store.dispatch('nova-file-manager/setPerPage', perPage)
const setView = view => store.dispatch('nova-file-manager/setView', view)
const submitFieldSelection = () => store.dispatch('nova-file-manager/submitFieldSelection')
const openModal = name => store.dispatch('nova-file-manager/openModal', name)
const setPath = path => store.dispatch('nova-file-manager/setPath', path)
const createFolder = path => store.dispatch('nova-file-manager/createFolder', path)
const clearSelection = () => store.commit('nova-file-manager/clearSelection')
</script>
