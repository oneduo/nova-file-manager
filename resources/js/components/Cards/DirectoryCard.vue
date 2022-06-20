<template>
  <li
    class="relative col-span-1 flex rounded-md transition duration-100 cursor-pointer bg-gray-200 dark:bg-gray-900 hover:shadow-md rounded-md"
  >
    <button
      class="flex w-full flex-row items-center"
      @click="setPath(path)"
    >
      <div
        class="flex-shrink-0 flex items-center justify-center py-4 pl-3 text-gray-900 dark:text-gray-100 text-sm font-medium"
      >
        <FolderIcon class="h-3 w-3" />
      </div>
      <div class="shrink px-2 py-2 truncate">
        <div
          class="whitespace-normal text-left break-all text-gray-800 dark:text-gray-200 font-medium text-sm hover:text-gray-900 dark:hover:text-gray-100"
        >
          {{ name }}
        </div>
      </div>
    </button>
    <div class="flex flex-row items-center">
      <div class="relative flex-1 flex items-center justify-between">
        <Menu
          as="div"
          class="relative inline-block text-left"
        >
          <MenuButton
            class="flex items-center text-gray-500 hover:text-blue-500 hover:bg-gray-50 dark:hover:bg-gray-600/50 rounded-full focus:outline-none mr-2 p-0.5"
          >
            <DotsVerticalIcon class="h-3 w-3" />
          </MenuButton>

          <MenuItems
            class="z-50 origin-top-right absolute right-0 mt-2 w-36 select-none overflow-hidden bg-white dark:bg-gray-900 rounded-md border border-gray-200 dark:border-gray-700 px-1"
          >
            <div class="py-1">
              <MenuItem>
                <button
                  @click="openModal(`renameFolder-${id}`)"
                  class="hover:bg-gray-50 dark:hover:bg-gray-800 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring rounded truncate whitespace-nowrap text-gray-500 active:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600"
                >
                  {{ __('Rename') }}
                </button>
              </MenuItem>
              <MenuItem>
                <button
                  @click="openModal(`deleteFolder-${id}`)"
                  class="hover:bg-red-50 dark:hover:bg-red-600/20 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring rounded truncate whitespace-nowrap text-red-500 dark:text-red-500 dark:hover:text-red-700"
                >
                  {{ __('Delete') }}
                </button>
              </MenuItem>
            </div>
          </MenuItems>
        </Menu>
      </div>
    </div>
  </li>

  <DeleteFolderModal
    :name="`deleteFolder-${id}`"
    :on-confirm="onDelete"
  />

  <RenameFolderModal
    :name="`renameFolder-${id}`"
    :old-path="name"
    :on-submit="onRename"
  />
</template>

<script>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ExclamationIcon, FolderIcon } from '@heroicons/vue/outline'
import { DotsVerticalIcon } from '@heroicons/vue/solid'
import ConfirmModal from '@/components/Modals/ConfirmModal'
import Button from '@/components/Elements/Button'
import RenameFolderModal from '@/components/Modals/RenameFolderModal'
import DeleteFolderModal from '@/components/Modals/DeleteFolderModal'
import { mapActions } from 'vuex'

export default {
  components: {
    DeleteFolderModal,
    Button,
    ConfirmModal,
    DotsVerticalIcon,
    ExclamationIcon,
    FolderIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    RenameFolderModal,
  },

  props: ['disk', 'name', 'path', 'id'],

  methods: {
    ...mapActions('nova-file-manager', [
      'setPath',
      'openModal',
      'closeModal',
      'renameFolder',
      'deleteFolder',
    ]),

    onRename(value) {
      this.renameFolder({ id: this.id, oldPath: this.path, newPath: value })
    },

    onDelete() {
      this.deleteFolder({ id: this.id, path: this.path })
    },
  },
}
</script>
