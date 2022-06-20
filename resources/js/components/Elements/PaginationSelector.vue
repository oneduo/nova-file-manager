<template>
  <Select
    :current="perPage"
    :options="perPageOptions"
    :on-click="setPerPage"
  />
</template>

<script>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/solid'
import Select from '@/components/Elements/Select'

export default {
  components: {
    Select,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    ChevronDownIcon,
    CheckIcon,
  },
  props: ['perPage', 'perPageOptions', 'setPerPage'],
}
</script>
