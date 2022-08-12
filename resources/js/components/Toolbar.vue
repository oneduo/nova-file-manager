<template>
  <div class="flex flex-col gap-y-4 pt-4">
    <div
      class="flex items-center justify-between flex-wrap sm:flex-nowrap gap-2 gap-y-2 flex-col-reverse sm:flex-row"
    >
      <div class="flex flex-row items-center gap-x-2 w-full">
        <DiskSelector :disk="disk" :disks="disks" :set-disk="setDisk" />
        <PaginationSelector
          :per-page="perPage"
          :per-page-options="perPageOptions"
          :set-per-page="setPerPage"
        />
        <ViewToggle :current="view" :set-view="setView" />
        <ToolbarSearch />
      </div>
      <div class="flex flex-row gap-x-2 justify-end w-full md:w-auto">
        <IconButton
          v-if="isFieldMode"
          variant="transparent"
          @click="closeBrowser"
        >
          <XIcon class="w-5 h-5" />
        </IconButton>
        <IconButton @click="openModal('createFolder')">
          <FolderAddIcon class="w-5 h-5" />
        </IconButton>
        <IconButton variant="primary" @click="openModal('upload')">
          <CloudUploadIcon class="h-5 w-5" />
        </IconButton>
        <IconButton v-if="isFieldMode" variant="success" @click="closeBrowser">
          <CheckIcon class="h-5 w-5" />
        </IconButton>
      </div>
    </div>
    <Breadcrumbs :items="breadcrumbs" :set-path="setPath" />
  </div>

  <UploadModal name="upload" />

  <CreateFolderModal :on-submit="createFolder" name="createFolder" />
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import {
    CheckIcon,
    CloudUploadIcon,
    FolderAddIcon,
    XIcon,
} from '@heroicons/vue/outline'
import DiskSelector from '@/components/DiskSelector'
import PaginationSelector from '@/components/Elements/PaginationSelector'
import Breadcrumbs from '@/components/Breadcrumbs'
import IconButton from '@/components/Elements/IconButton'
import ToolbarSearch from '@/components/Elements/ToolbarSearch'
import ViewToggle from '@/components/Elements/ViewToggle'
import UploadModal from '@/components/Modals/UploadModal'
import CreateFolderModal from '@/components/Modals/CreateFolderModal'

const store = useStore()

const path = computed(() => store.state['nova-file-manager'].path)
const disk = computed(() => store.state['nova-file-manager'].disk)
const disks = computed(() => store.state['nova-file-manager'].disks)
const view = computed(() => store.state['nova-file-manager'].view)
const perPage = computed(() => store.state['nova-file-manager'].perPage)
const perPageOptions = computed(
    () => store.state['nova-file-manager'].perPageOptions
)
const isFieldMode = computed(() => store.state['nova-file-manager'].isFieldMode)
const breadcrumbs = computed(() => store.state['nova-file-manager'].breadcrumbs)

const setDisk = disk => store.dispatch('nova-file-manager/setDisk', disk)
const setPerPage = perPage =>
    store.dispatch('nova-file-manager/setPerPage', perPage)
const setView = view => store.dispatch('nova-file-manager/setView', view)
const closeBrowser = () => store.dispatch('nova-file-manager/closeBrowser')
const openModal = name => store.dispatch('nova-file-manager/openModal', name)
const setPath = path => store.dispatch('nova-file-manager/setPath', path)
const createFolder = path =>
    store.dispatch('nova-file-manager/createFolder', path)
</script>
