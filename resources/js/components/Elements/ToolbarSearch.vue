<template>
  <div class="relative w-1/3 mr-auto">
    <Icon
      type="search"
      width="20"
      class="absolute ml-2 text-gray-400"
      :style="{ top: '4px' }"
    />

    <RoundInput
      class="appearance-none rounded-full h-8 pl-10 w-full bg-gray-100 dark:bg-gray-900 dark:focus:bg-gray-800 focus:bg-white focus:outline-none focus:ring focus:ring-primary-200 dark:focus:ring-gray-600"
      :placeholder="__('Search')"
      type="search"
      :value="keyword"
      @input="handleChange"
      spellcheck="false"
      :aria-label="__('Search')"
    />
  </div>
</template>

<script>
export default {
  props: {
    keyword: {
      type: String,
    },
  },

  methods: {
    /**
     * Update the field's internal value
     */
    handleChange(event) {
      // TODO: call store
    },
  },
}
</script>
