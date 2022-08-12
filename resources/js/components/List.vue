<template>
  <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600/50">
    <tbody class="divide-y divide-gray-200 dark:divide-gray-900/20">
      <tr
        v-if="directories?.length"
        class="border-t border-gray-200 dark:border-gray-700/50"
      >
        <th
          class="py-2 text-left text-xs font-semibold text-gray-500"
          colspan="2"
          scope="colgroup"
        >
          {{ __('Folders') }}
        </th>
      </tr>
      <template v-for="directory in directories" :key="directory.id">
        <tr class="cursor-pointer">
          <td
            class="whitespace-nowrap py-4 text-sm"
            @click="setPath(directory.path)"
          >
            <div
              class="flex items-center text-gray-500 dark:text-gray-300 hover:text-blue-500"
            >
              <FolderIcon class="w-5 h-5" />
              <div class="ml-4">
                <div class="font-medium">
                  {{ directory.name }}
                </div>
              </div>
            </div>
          </td>
          <td
            class="relative whitespace-nowrap py-4 text-right text-xs font-medium"
          >
            <div class="inline-flex items-center">
              <div class="relative flex-1 flex items-center justify-end">
                <Menu as="div" class="relative inline-block text-left">
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
                          class="hover:bg-gray-50 dark:hover:bg-gray-800 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring rounded truncate whitespace-nowrap text-gray-500 active:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600"
                          @click="openModal(`renameFolder-${directory.id}`)"
                        >
                          {{ __('Rename') }}
                        </button>
                      </MenuItem>
                      <MenuItem>
                        <button
                          class="hover:bg-red-50 dark:hover:bg-red-600/20 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring rounded truncate whitespace-nowrap text-red-500 dark:text-red-500 dark:hover:text-red-700"
                          @click="openModal(`deleteFolder-${directory.id}`)"
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
          :name="`deleteFolder-${directory.id}`"
          :on-confirm="() => onFolderDelete(directory.id, directory.path)"
        />

        <RenameFolderModal
          :name="`renameFolder-${directory.id}`"
          :old-path="directory.name"
          :on-submit="
            value => onFolderRename(directory.id, directory.path, value)
          "
        />
      </template>
      <tr
        v-if="files?.length"
        class="border-t border-gray-200 dark:border-gray-700"
      >
        <th
          class="py-2 text-left text-xs font-semibold text-gray-500"
          colspan="2"
          scope="colgroup"
        >
          {{ __('Files') }}
        </th>
      </tr>
      <template v-for="file in files" :key="file.id">
        <tr
          class="cursor-pointer"
          @click="toggleSelection(file)"
          @dblclick="openPreview(file)"
        >
          <td class="whitespace-nowrap py-4 text-sm w-full" colspan="2">
            <div
              :class="[
                isFileSelected(file)
                  ? 'text-blue-500 hover:text-blue-300'
                  : 'text-gray-500 dark:text-gray-300 hover:text-blue-500',
              ]"
              class="flex items-center"
            >
              <DocumentIcon class="w-5 h-5" />
              <div class="ml-4">
                <div
                  :class="[isFileSelected(file) ? 'font-bold' : 'font-medium']"
                >
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

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import { DocumentIcon, FolderIcon } from '@heroicons/vue/outline'
import DeleteFolderModal from '@/components/Modals/DeleteFolderModal'
import Button from '@/components/Elements/Button'
import { DotsVerticalIcon } from '@heroicons/vue/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import RenameFolderModal from '@/components/Modals/RenameFolderModal'
import PreviewModal from '@/components/Modals/PreviewModal'

const store = useStore()
const files = computed(() => store.state['nova-file-manager'].files)
const directories = computed(() => store.state['nova-file-manager'].directories)
const isFileSelected = computed(
    () => store.getters['nova-file-manager/isFileSelected']
)

const onFolderRename = (id, oldPath, value) =>
    store.dispatch('nova-file-manager/renameFolder', {
        id,
        oldPath,
        newPath: value,
    })
const onFolderDelete = (id, path) =>
    store.dispatch('nova-file-manager/deleteFolder', { id, path })

const openPreview = file => store.commit('nova-file-manager/previewFile', file)
const toggleSelection = file =>
    store.getters['nova-file-manager/isFileSelected'](file)
        ? store.commit('nova-file-manager/deselectFile', file)
        : store.commit('nova-file-manager/selectFile', file)

const setPath = path => store.dispatch('nova-file-manager/setPath', path)
const openModal = name => store.dispatch('nova-file-manager/openModal', name)
</script>
