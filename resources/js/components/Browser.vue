<template>
  <div
    class="flex flex-1 items-stretch overflow-hidden rounded-md w-full min-h-[30vh]"
  >
    <main class="relative flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-800">
      <div class="w-full px-4 space-y-4 mb-4">
        <Toolbar />

        <div
          v-if="isFetchingData"
          class="w-full h-80 flex justify-center items-center"
        >
          <Spinner class="w-16 h-16" />
        </div>

        <template v-else>
          <BrowserContent
            :directories="directories"
            :files="files"
            :filled="filled"
            :view="view"
          />
        </template>
      </div>

      <Pagination
        v-if="!isFetchingData && pagination && pagination.total > 0"
        :current-page="pagination.current_page"
        :from="pagination.from"
        :last-page="pagination.last_page"
        :links="pagination.links"
        :to="pagination.to"
        :total="pagination.total"
        class="mt-auto"
      />
    </main>
  </div>
  <div id="modals"></div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted } from 'vue'
import { useStore } from 'vuex'
import Toolbar from '@/components/Toolbar'
import Pagination from '@/components/Pagination'
import Spinner from '@/components/Elements/Spinner'
import BrowserContent from '@/components/BrowserContent'

const store = useStore()
const files = computed(() => store.state['nova-file-manager'].files)
const directories = computed(() => store.state['nova-file-manager'].directories)
const filled = computed(() => files.value?.length || directories.value?.length)
const pagination = computed(() => store.state['nova-file-manager'].pagination)
const view = computed(() => store.state['nova-file-manager'].view)
const isFetchingData = computed(
    () => store.state['nova-file-manager'].isFetchingData
)

onMounted(() => {
    store.commit('nova-file-manager/init')
    store.dispatch('nova-file-manager/getDisks')
    store.dispatch('nova-file-manager/getData')
})

onBeforeUnmount(() => {
    store.commit('nova-file-manager/destroy')
})
</script>
