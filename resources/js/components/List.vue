<template>
  <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600/50">
    <tbody class="divide-y divide-gray-200 dark:divide-gray-900/20">
      <tr v-if="folders?.length" class="border-t border-gray-200 dark:border-gray-700/50">
        <th class="py-2 text-xs font-semibold text-left text-gray-500" colspan="2" scope="colgroup">
          {{ __('Folders') }}
        </th>
      </tr>
      <template v-for="folder in folders" :key="folder.id">
        <tr class="cursor-pointer">
          <td class="py-4 text-sm whitespace-nowrap" @click="setPath(folder.path)">
            <div class="flex items-center text-gray-500 dark:text-gray-300 hover:text-blue-500">
              <FolderIcon class="w-5 h-5" />
              <div class="ml-4">
                <div class="font-medium">
                  {{ folder.name }}
                </div>
              </div>
            </div>
          </td>
          <td
            class="relative py-4 text-xs font-medium text-right whitespace-nowrap"
            v-if="showRenameFolder || showDeleteFolder"
          >
            <div class="inline-flex items-center">
              <div class="relative flex items-center justify-end flex-1">
                <Menu as="div" class="relative inline-block text-left">
                  <MenuButton
                    class="
                      flex
                      items-center
                      text-gray-500
                      hover:text-blue-500 hover:bg-gray-50
                      dark:hover:bg-gray-600/50
                      rounded-full
                      focus:outline-none
                      mr-2
                      p-0.5
                    "
                  >
                    <EllipsisHorizontalIcon class="w-3 h-3" />
                  </MenuButton>

                  <MenuItems
                    class="absolute right-0 z-50 px-1 mt-2 overflow-hidden origin-top-right bg-white border border-gray-200 rounded-md select-none  w-36 dark:bg-gray-900 dark:border-gray-700"
                  >
                    <div class="py-1">
                      <MenuItem v-if="showRenameFolder">
                        <button
                          class="block w-full px-3 py-2 text-left text-gray-500 truncate rounded cursor-pointer  hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring whitespace-nowrap active:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600"
                          @click="openModal(`rename-folder-${folder.id}`)"
                        >
                          {{ __('Rename') }}
                        </button>
                      </MenuItem>
                      <MenuItem v-if="showDeleteFolder">
                        <button
                          class="block w-full px-3 py-2 text-left text-red-500 truncate rounded cursor-pointer  hover:bg-red-50 dark:hover:bg-red-600/20 focus:outline-none focus:ring whitespace-nowrap dark:text-red-500 dark:hover:text-red-700"
                          @click="openModal(`delete-folder-${folder.id}`)"
                        >
                          {{ __('Delete') }}
                        </button>
                      </MenuItem>
                    </div>
                  </MenuItems>
                </Menu>
              </div>
            </div>
          </td>
        </tr>
        <DeleteFolderModal
          v-if="showDeleteFolder"
          :name="`delete-folder-${folder.id}`"
          :on-confirm="() => onFolderDelete(folder.id, folder.path)"
        />

        <RenameFolderModal
          v-if="showRenameFolder"
          :name="`rename-folder-${folder.id}`"
          :from="folder.name"
          :on-submit="value => onFolderRename(folder.id, folder.path, value)"
        />
      </template>
      <tr v-if="files?.length" class="border-t border-gray-200 dark:border-gray-700">
        <th class="py-2 text-xs font-semibold text-left text-gray-500" colspan="2" scope="colgroup">
          {{ __('Files') }}
        </th>
      </tr>
      <template v-for="file in files" :key="file.id">
        <tr class="cursor-pointer" @click="toggleSelection(file)" @dblclick="openPreview(file)">
          <td class="w-full py-4 text-sm whitespace-nowrap" colspan="2">
            <div
              :class="[
                isSelected(file)
                  ? 'text-blue-500 hover:text-blue-300'
                  : 'text-gray-500 dark:text-gray-300 hover:text-blue-500',
              ]"
              class="flex items-center"
            >
              <DocumentIcon class="w-5 h-5" />
              <div class="ml-4">
                <div :class="[isSelected(file) ? 'font-bold' : 'font-medium']">
                  {{ file.name }}
                </div>
              </div>
            </div>
          </td>
          <PreviewModal :file="entity(file)" v-if="!!preview && preview?.id === entity(file)?.id" />
        </tr>
      </template>
    </tbody>
  </table>
</template>

<script setup>
import { computed } from 'vue'
import { DocumentIcon, FolderIcon } from '@heroicons/vue/24/outline'
import DeleteFolderModal from '@/components/Modals/DeleteFolderModal'
import { EllipsisHorizontalIcon } from '@heroicons/vue/24/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import RenameFolderModal from '@/components/Modals/RenameFolderModal'
import PreviewModal from '@/components/Modals/PreviewModal'
import { usePermissions } from '@/hooks'
import { useStore } from '@/store'
import { entity } from '@/transformers/entityTransformer'

const store = useStore()
const { showRenameFolder, showDeleteFolder } = usePermissions()

// STATE
const files = computed(() => store.files)
const folders = computed(() => store.folders)
const isSelected = computed(() => store.isSelected)
const preview = computed(() => store.preview)

// ACTIONS
const onFolderRename = (id, from, to) => store.renameFile({ id, from, to })
const onFolderDelete = (id, path) => store.deleteFolder({ id, path })
const openPreview = file => (store.preview = file)
const toggleSelection = file => store.toggleSelection({ file })
const setPath = path => store.setPath({ path })
const openModal = name => store.openModal({ name })
</script>
