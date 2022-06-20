<template>
  <Select
    :current="disk"
    :options="disks"
    :on-click="setDisk"
  />
</template>

<script>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/outline'
import Select from '@/components/Elements/Select'

export default {
  name: 'DiskSelector',
  components: {
    Select,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    ChevronDownIcon,
    CheckIcon,
  },
  props: ['disk', 'disks', 'setDisk'],
}
</script>
