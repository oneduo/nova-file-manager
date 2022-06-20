<template>
  <RadioGroup v-model="selected">
    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <tr
          class="border-t border-gray-200 dark:border-gray-700"
          v-if="directories?.length"
        >
          <th
            colspan="2"
            scope="colgroup"
            class="py-2 text-left text-xs font-semibold text-gray-500"
          >
            {{ __('Folders') }}
          </th>
        </tr>
        <template
          v-for="directory in directories"
          :key="directory.id"
        >
          <tr class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900">
            <td
              class="whitespace-nowrap py-4 text-sm"
              @click="setPath(directory.path)"
            >
              <div class="flex items-center">
                <FolderIcon class="w-5 h-5" />
                <div class="ml-4">
                  <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ directory.name }}
                  </div>
                </div>
              </div>
            </td>
            <td class="relative whitespace-nowrap py-4 text-right text-xs font-medium">
              <div class="inline-flex items-center">
                <div class="relative flex-1 flex items-center justify-end">
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
                            @click="openModal(`renameFolder-${directory.id}`)"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring rounded truncate whitespace-nowrap text-gray-500 active:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600"
                          >
                            {{ __('Rename') }}
                          </button>
                        </MenuItem>
                        <MenuItem>
                          <button
                            @click="openModal(`deleteFolder-${directory.id}`)"
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
            </td>
          </tr>
          <DeleteFolderModal
            :name="`deleteFolder-${directory.id}`"
            :on-confirm="() => onFolderDelete(directory.id, directory.path)"
          />

          <RenameFolderModal
            :name="`renameFolder-${directory.id}`"
            :old-path="directory.name"
            :on-submit="(value) => onFolderRename(directory.id, directory.path, value)"
          />
        </template>
        <tr
          class="border-t border-gray-200 dark:border-gray-700"
          v-if="files?.length"
        >
          <th
            colspan="2"
            scope="colgroup"
            class="py-2 text-left text-xs font-semibold text-gray-500"
          >
            {{ __('Files') }}
          </th>
        </tr>
        <RadioGroupOption
          as="template"
          v-for="file in files"
          :value="file"
          v-slot="{ checked, active }"
          :key="file.id"
        >
          <tr class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900">
            <td
              class="whitespace-nowrap py-4 text-sm w-full"
              colspan="2"
            >
              <div class="flex items-center">
                <DocumentIcon class="w-5 h-5" />
                <div class="ml-4">
                  <div class="font-medium text-gray-900 dark:text-gray-100">{{ file.name }}</div>
                </div>
              </div>
            </td>
          </tr>
        </RadioGroupOption>
      </tbody>
    </table>
  </RadioGroup>
</template>

<script>
import { mapActions, mapMutations, mapState } from 'vuex'
import { DocumentIcon, ExclamationIcon, FolderIcon } from '@heroicons/vue/outline'
import DeleteFolderModal from '@/components/Modals/DeleteFolderModal'
import Button from '@/components/Elements/Button'
import ConfirmModal from '@/components/Modals/ConfirmModal'
import { DotsVerticalIcon } from '@heroicons/vue/solid'
import {
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  RadioGroup,
  RadioGroupOption,
} from '@headlessui/vue'
import RenameFolderModal from '@/components/Modals/RenameFolderModal'
import Sidebar from '@/components/Sidebar'

export default {
  name: 'List',
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
    DocumentIcon,
    RadioGroup,
    RadioGroupOption,
    Sidebar,
  },
  computed: {
    ...mapState('nova-file-manager', ['files', 'directories', 'selectedFile']),
    selected: {
      get() {
        return this.selectedFile
      },
      set(value) {
        this.setSelectedFile(value)
      },
    },
  },
  methods: {
    ...mapMutations('nova-file-manager', ['setSelectedFile']),
    ...mapActions('nova-file-manager', [
      'setPath',
      'openModal',
      'closeModal',
      'renameFolder',
      'deleteFolder',
    ]),

    onFolderRename(id, oldPath, value) {
      this.renameFolder({ id, oldPath, newPath: value })
    },

    onFolderDelete(id, path) {
      this.deleteFolder({ id, path })
    },
  },
}
</script>
<script setup>
import DeleteFileModal from '@/components/Modals/DeleteFileModal'
import RenameFileModal from '@/components/Modals/RenameFileModal'

const people = [
  {
    name: 'Lindsay Walton',
    title: 'Front-end Developer',
    department: 'Optimization',
    email: 'lindsay.walton@example.com',
    role: 'Member',
    image:
      'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
  },
  // More people...
]
</script>
