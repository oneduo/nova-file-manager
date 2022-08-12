<template>
  <Menu as="div" class="relative inline-block text-left">
    <div class="group">
      <MenuButton
        class="inline-flex justify-center w-full rounded-md shadow-sm px-4 py-2 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-black dark:hover:text-white focus:outline-none focus:ring-1 focus:ring-blue-500"
      >
        {{ current }}
        <ChevronDownIcon
          aria-hidden="true"
          class="-mr-1 ml-2 h-5 w-5 dark:group-hover:text-white group-hover:text-black"
        />
      </MenuButton>
    </div>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <MenuItems
        class="z-10 origin-top-left absolute text-xs left-0 mt-2 w-36 rounded-md shadow-lg bg-white dark:bg-gray-900 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 px-1 focus:outline-none"
        tabindex="-1"
      >
        <div class="py-1">
          <MenuItem v-for="value in options">
            <div class="flex flex-row">
              <button
                class="flex flex-row justify-between hover:bg-gray-50 dark:hover:bg-gray-800 block w-full text-left cursor-pointer py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500 rounded truncate whitespace-nowrap text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600"
                tabindex="1"
                type="button"
                @click.prevent="onClick(value)"
              >
                <span>{{ value }}</span>
                <span v-if="current === value">
                  <CheckIcon aria-hidden="true" class="h-5 w-5 text-blue-500" />
                </span>
              </button>
            </div>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/solid'

const props = defineProps(['current', 'options', 'onClick'])
</script>
