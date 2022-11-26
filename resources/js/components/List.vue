<script setup lang="ts">
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { DocumentIcon, FolderIcon } from '@heroicons/vue/24/outline'
import { EllipsisHorizontalIcon } from '@heroicons/vue/24/solid'
import { Entity } from '__types__'
import { computed } from 'vue'
import DeleteFolderModal from '@/components/Modals/DeleteFolderModal.vue'
import PreviewModal from '@/components/Modals/PreviewModal.vue'
import RenameFolderModal from '@/components/Modals/RenameFolderModal.vue'
import { usePermissions } from '@/hooks'
import useBrowserStore from '@/stores/browser'

const store = useBrowserStore()
const { showRenameFolder, showDeleteFolder } = usePermissions()

// STATE
const files = computed(() => store.files)
const folders = computed(() => store.folders)
const isSelected = computed(() => store.isSelected)

// ACTIONS
const onFolderRename = (id: string, from: string, to: string) => store.renameFile({ id, from, to })
const onFolderDelete = (id: string, path: string) => store.deleteFolder({ id, path })
const openPreview = (file: Entity) => (store.preview = file)
const toggleSelection = (file: Entity) => store.toggleSelection({ file })
const setPath = (path: string) => store.setPath({ path })
const openModal = (name: string) => store.openModal({ name })
</script>

<template>
  <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600/50">
    <tbody class="divide-y divide-gray-200 dark:divide-gray-900/20">
      <tr v-if="folders?.length" class="border-t border-gray-200 dark:border-gray-700/50">
        <th class="py-2 text-left text-xs font-semibold text-gray-500" colspan="2" scope="colgroup">
          {{ __('Folders') }}
        </th>
      </tr>
      <template v-for="folder in folders" :key="folder.id">
        <tr class="cursor-pointer">
          <td class="whitespace-nowrap py-4 text-sm" @click="setPath(folder.path)">
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
            class="relative whitespace-nowrap py-4 text-right text-xs font-medium"
            v-if="showRenameFolder || showDeleteFolder"
          >
            <div class="inline-flex items-center">
              <div class="relative flex-1 flex items-center justify-end">
                <Menu as="div" class="relative inline-block text-left">
                  <MenuButton
                    class="flex items-center text-gray-500 hover:text-blue-500 hover:bg-gray-50 dark:hover:bg-gray-600/50 rounded-full focus:outline-none mr-2 p-0.5"
                  >
                    <EllipsisHorizontalIcon class="h-3 w-3" />
                  </MenuButton>

                  <MenuItems
                    class="z-50 origin-top-right absolute right-0 mt-2 w-36 select-none overflow-hidden bg-white dark:bg-gray-900 rounded-md border border-gray-200 dark:border-gray-700 px-1"
                  >
                    <div class="py-1">
                      <MenuItem v-if="showRenameFolder">
                        <button
                          class="hover:bg-gray-50 dark:hover:bg-gray-800 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring rounded truncate whitespace-nowrap text-gray-500 active:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600"
                          @click="openModal(`rename-folder-${folder.id}`)"
                        >
                          {{ __('Rename') }}
                        </button>
                      </MenuItem>
                      <MenuItem v-if="showDeleteFolder">
                        <button
                          class="hover:bg-red-50 dark:hover:bg-red-600/20 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring rounded truncate whitespace-nowrap text-red-500 dark:text-red-500 dark:hover:text-red-700"
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
        <th class="py-2 text-left text-xs font-semibold text-gray-500" colspan="2" scope="colgroup">
          {{ __('Files') }}
        </th>
      </tr>
      <template v-for="file in files" :key="file.id">
        <tr class="cursor-pointer" @click="toggleSelection(file)" @dblclick="openPreview(file)">
          <td class="whitespace-nowrap py-4 text-sm w-full" colspan="2">
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

          <PreviewModal :file="file" />
        </tr>
      </template>
    </tbody>
  </table>
</template>
