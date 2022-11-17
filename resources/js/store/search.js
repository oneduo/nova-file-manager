import { defineStore } from 'pinia'
import { useStore } from './'
import axios from 'axios'

const modifiers = {
  folders: '#',
  files: '>',
  help: '?',
}

const useSearchStore = defineStore('nova-file-manager/search', {
  state: () => ({
    isOpen: false,
    isLoading: false,
    query: '',
    search: '',
    folders: undefined,
    files: undefined,
    isFolderOnly: false,
    isFileOnly: false,
    help: false,
    hasResults: false,
    request: undefined,
  }),

  actions: {
    /**
     * Open spotlight search
     */
    open() {
      this.isOpen = true
      this.files = []
      this.folders = []
    },

    /**
     * Close spotlight search
     */
    close() {
      this.isOpen = false
    },

    /**
     * Set the search query
     *
     * @param {string|null|undefined} search
     * @returns {Promise<void>}
     */
    async setSearch({ search }) {
      if (!search?.length) {
        this.reset()

        return
      }

      // dedup search if string didn't change
      if (this.search === search) {
        return
      }

      this.isLoading = true

      if (search?.startsWith(modifiers.folders)) {
        this.isFolderOnly = true
      }

      if (search?.startsWith(modifiers.files)) {
        this.isFileOnly = true
      }

      if (search?.startsWith(modifiers.help)) {
        this.help = true

        return
      }

      this.search = search

      this.query = this.search?.replace(/[#>?]/, '')

      await this.data()
    },

    async data() {
      if (this.help) {
        return
      }

      if (!this.query?.length) {
        this.isLoading = false

        this.folders = []
        this.files = []

        return
      }

      if (this.request) {
        this.request.cancel('[nova-file-manager] new search request triggered')
      }

      const source = axios.CancelToken.source()

      this.request = { cancel: source.cancel }

      const store = useStore()

      const response = await store
        .get({
          params: store.payload({
            path: store.path,
            search: this.query,
            disk: store.disk,
          }),
          options: {
            cancelToken: source.token,
          },
        })
        .then(response => response)
        .catch(error => error)

      if (axios.isCancel(response)) {
        return
      }

      if (!response || response.status !== 200) {
        this.isLoading = false

        Nova.error('An error occurred while searching')

        console.error(response)

        return
      }

      const { data } = response

      if (!this.isFileOnly) {
        this.folders = data?.folders
      }

      if (!this.isFolderOnly) {
        this.files = data?.files
      }

      this.hasResults = this.folders?.length || this.files?.length

      this.isLoading = false
    },
    async select({ item }) {
      this.close()

      const store = useStore()

      if (item.exists) {
        store.setPreview({ preview: item })
      } else {
        await store.setPath({ path: item.path })
      }
    },

    reset() {
      this.isLoading = false
      this.isFolderOnly = false
      this.isFileOnly = false
      this.help = false

      this.query = ''
      this.search = ''
    },
  },
})

export { useSearchStore }
