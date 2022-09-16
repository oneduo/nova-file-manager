<template>
  <div class="flex flex-col gap-y-4 pt-4">
    <div
      class="flex items-center justify-between flex-wrap sm:flex-nowrap gap-2 gap-y-2 flex-col-reverse sm:flex-row"
    >
      <div class="flex flex-row items-center gap-2 w-full flex-wrap sm:flex-nowrap">
        <DiskSelector
          v-if="!singleDisk"
          :disk="disk"
          :disks="disks"
          :set-disk="setDisk"
          :is-loading="isFetchingDisks"
          data-tour="nfm-disk-selector"
        />

        <PaginationSelector
          :per-page="Number(perPage)"
          :per-page-options="perPageOptions"
          :set-per-page="setPerPage"
          data-tour="nfm-pagination-selector"
        />

        <ViewToggle :current="view" :set-view="setView" data-tour="nfm-view-toggle" />
      </div>
      <div class="flex flex-row gap-x-2 justify-end w-full sm:w-auto flex-shrink-0">
        <div class="p-2 rounded-md font-semibold text-xs text-gray-400" v-if="selection?.length">
          <span
            :class="limit !== null && selection.length > limit ? 'text-red-500' : 'text-blue-500'"
          >
            {{ selection.length }}
          </span>

          <template v-if="!!limit">/{{ limit }}</template>

          {{ __('NovaFileManager.toolbar.selection') }}

          <button @click="clearSelection" class="underline">
            {{ __('NovaFileManager.toolbar.clear') }}
          </button>
        </div>

        <IconButton @click="openSearch" data-tour="nfm-spotlight-search-button">
          <MagnifyingGlassIcon class="w-5 h-5" />
        </IconButton>

        <IconButton
          v-if="showCreateFolder"
          @click="openModal('create-folder')"
          data-tour="nfm-create-folder-button"
        >
          <FolderPlusIcon class="w-5 h-5" />
        </IconButton>

        <IconButton
          v-if="showUploadFile"
          variant="primary"
          @click="openUploadModal"
          data-tour="nfm-upload-file-button"
        >
          <CloudArrowUpIcon class="h-5 w-5" />
        </IconButton>

        <IconButton
          v-if="isField"
          variant="success"
          @click="confirm"
          :disabled="!!limit && selection?.length > limit"
          data-tour="nfm-confirm-selection-button"
        >
          <CheckIcon class="h-5 w-5" />
        </IconButton>
      </div>
    </div>

    <Breadcrumbs :items="breadcrumbs" :set-path="setPath" />
  </div>

  <UploadModal v-if="showUploadFile" name="upload" :queue="queue" :upload="upload" />

  <CreateFolderModal v-if="showCreateFolder" :on-submit="createFolder" name="create-folder" />
</template>

<script setup>
import {
  CheckIcon,
  CloudArrowUpIcon,
  FolderPlusIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'
import DiskSelector from '@/components/DiskSelector'
import PaginationSelector from '@/components/Elements/PaginationSelector'
import Breadcrumbs from '@/components/Breadcrumbs'
import IconButton from '@/components/Elements/IconButton'
import ViewToggle from '@/components/Elements/ViewToggle'
import UploadModal from '@/components/Modals/UploadModal'
import CreateFolderModal from '@/components/Modals/CreateFolderModal'
import { usePermissions } from '@/hooks'
import { computed } from 'vue'
import { useStore } from '@/store'
import { useSearchStore } from '@/store/search'

const store = useStore()
const searchStore = useSearchStore()

const { showCreateFolder, showUploadFile } = usePermissions()

// STATE
const isField = computed(() => store.isField)
const disk = computed(() => store.disk)
const disks = computed(() => store.disks)
const singleDisk = computed(() => store.singleDisk)
const isFetchingDisks = computed(() => store.isFetchingDisks)
const breadcrumbs = computed(() => store.breadcrumbs)
const view = computed(() => store.view)
const queue = computed(() => store.queue)
const perPage = computed(() => store.perPage)
const perPageOptions = computed(() => store.perPageOptions)
const selection = computed(() => store.selection)
const limit = computed(() => store.limit)

// ACTIONS
const setDisk = disk => store.setDisk({ disk })
const setPerPage = perPage => store.setPerPage({ perPage })
const setPath = path => store.setPath({ path })
const setView = view => store.setView({ view })
const openModal = name => store.openModal({ name })
const clearSelection = () => store.clearSelection()
const upload = files => store.upload({ files })
const confirm = () => store.confirm()
const createFolder = path => store.createFolder({ path })
const openSearch = () => searchStore.open()

const openUploadModal = () => {
  openModal(queue.value.length ? 'queue' : 'upload')
}
</script>
