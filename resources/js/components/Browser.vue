<template>
  <div class="flex flex-1 items-stretch overflow-hidden rounded-md w-full min-h-[30vh]">
    <main class="relative flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-800">
      <div class="w-full px-4 space-y-4 mb-4">
        <Toolbar />

        <div
          class="w-full h-80 flex justify-center items-center"
          v-if="isFetchingData"
        >
          <Spinner class="w-16 h-16" />
        </div>

        <template v-else>
          <BrowserContent
            :view="view"
            :files="files"
            :directories="directories"
            :filled="filled"
          />
        </template>
      </div>

      <Pagination
        v-if="!isFetchingData && pagination && pagination.total > 0"
        :from="pagination.from"
        :to="pagination.to"
        :links="pagination.links"
        :total="pagination.total"
        :current-page="pagination.current_page"
        :last-page="pagination.last_page"
        class="mt-auto"
      />
    </main>
  </div>
  <div id="modals"></div>
</template>

<script>
import Toolbar from '@/components/Toolbar'
import Pagination from '@/components/Pagination'
import Spinner from '@/components/Elements/Spinner'
import { mapActions, mapMutations, mapState } from 'vuex'
import Sidebar from '@/components/Sidebar'
import BrowserContent from '@/components/BrowserContent'

export default {
  components: {
    BrowserContent,
    Toolbar,
    Pagination,
    Spinner,
    Sidebar,
  },

  mounted() {
    this.init()
    this.getDisks()
    this.getData()
  },

  beforeUnmount() {
    this.destroy()
  },
  computed: {
    ...mapState('nova-file-manager', [
      'disk',
      'disks',
      'path',
      'files',
      'directories',
      'breadcrumbs',
      'pagination',
      'page',
      'perPage',
      'perPageOptions',
      'selectedFile',
      'view',
      'isFetchingData',
      'isFetchingDisks',
      'isPreviewOpen',
      'isFieldMode',
    ]),
    filled() {
      return this.files?.length || this.directories?.length
    },
  },

  methods: {
    ...mapMutations('nova-file-manager', ['init', 'destroy', 'setSelectedFile']),
    ...mapActions('nova-file-manager', ['setPath', 'getData', 'getDisks']),
  },
}
</script>
