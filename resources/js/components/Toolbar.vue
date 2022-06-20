<template>
  <div class="flex flex-col gap-y-4 pt-4">
    <div class="flex items-center justify-between">
      <div class="flex flex-row gap-x-2">
        <DiskSelector
          :disk="disk"
          :disks="disks"
          :set-disk="setDisk"
        />
        <PaginationSelector
          :per-page="perPage"
          :per-page-options="perPageOptions"
          :set-per-page="setPerPage"
        />
        <ViewToggle
          :current="view"
          :set-view="setView"
        />
      </div>
      <div class="flex flex-row gap-x-2">
        <IconButton
          variant="transparent"
          v-if="isFieldMode"
          @click="closeBrowser"
        >
          <XIcon class="w-5 h-5" />
        </IconButton>
        <IconButton @click="openModal('createFolder')">
          <FolderAddIcon class="w-5 h-5" />
        </IconButton>
        <IconButton
          @click="openModal('upload')"
          variant="primary"
        >
          <CloudUploadIcon class="h-5 w-5" />
        </IconButton>
      </div>
    </div>
    <Breadcrumbs
      :items="breadcrumbs"
      :set-path="setPath"
    />
  </div>

  <UploadModal name="upload" />

  <CreateFolderModal
    name="createFolder"
    :on-submit="createFolder"
  />
</template>

<script>
import { mapActions, mapMutations, mapState } from 'vuex'
import { CheckIcon, CloudUploadIcon, FolderAddIcon, XIcon } from '@heroicons/vue/outline'
import DiskSelector from '@/components/DiskSelector'
import PaginationSelector from '@/components/Elements/PaginationSelector'
import Breadcrumbs from '@/components/Breadcrumbs'
import IconButton from '@/components/Elements/IconButton'
import ViewToggle from '@/components/Elements/ViewToggle'
import UploadModal from '@/components/Modals/UploadModal'
import CreateFolderModal from '@/components/Modals/CreateFolderModal'

export default {
  name: 'Toolbar',
  components: {
    ViewToggle,
    IconButton,
    Breadcrumbs,
    PaginationSelector,
    DiskSelector,
    CloudUploadIcon,
    FolderAddIcon,
    CheckIcon,
    XIcon,
    UploadModal,
    CreateFolderModal,
  },
  methods: {
    ...mapMutations('nova-file-manager', ['setSelectedFile']),
    ...mapActions('nova-file-manager', [
      'setDisk',
      'setPath',
      'setPerPage',
      'setPath',
      'createFolder',
      'openModal',
      'closeBrowser',
      'setView',
    ]),
  },
  computed: {
    ...mapState('nova-file-manager', [
      'path',
      'disk',
      'disks',
      'view',
      'perPage',
      'breadcrumbs',
      'perPageOptions',
      'selectedFile',
      'isFieldMode',
    ]),
  },
}
</script>
