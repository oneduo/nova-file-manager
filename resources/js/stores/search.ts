import { Entity, Folder } from '__types__'
import axios, { Canceler } from 'axios'
import { defineStore } from 'pinia'
import { is } from 'typescript-is'
import useBrowserStore from '@/stores/browser'

const modifiers = {
  folders: '#',
  files: '>',
  help: '?',
}

interface State {
  isOpen: boolean
  isLoading: boolean
  query: string
  search: string
  folders: Folder[] | undefined
  files: Entity[] | undefined
  isFolderOnly: boolean
  isFileOnly: boolean
  help: boolean
  hasResults?: boolean
  canceler: { cancel: Canceler } | undefined
}

const useSearchStore = defineStore('nova-file-manager/search', {
  state: (): State => ({
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
    canceler: undefined,
  }),

  actions: {
    open() {
      this.isOpen = true
      this.files = []
      this.folders = []
    },

    close() {
      this.isOpen = false
    },

    async setSearch({ search }: { search: string | null | undefined }) {
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

      if (this.canceler) {
        this.canceler.cancel('[nova-file-manager] new search request triggered')
      }

      const source = axios.CancelToken.source()

      this.canceler = { cancel: source.cancel }

      const store = useBrowserStore()

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

        window.Nova.error('An error occurred while searching')

        return
      }

      const { data } = response

      if (!this.isFileOnly) {
        this.folders = data?.folders
      }

      if (!this.isFolderOnly) {
        this.files = data?.files
      }

      this.hasResults = !!this.folders?.length || !!this.files?.length

      this.isLoading = false
    },

    async select({ item }: { item: Entity | Folder }) {
      this.close()

      const store = useBrowserStore()

      if (is<Entity>(item)) {
        store.setPreview({ preview: item })

        return
      }

      await store.setPath({ path: item.path })
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

export default useSearchStore
