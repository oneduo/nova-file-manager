import {
  ApiError,
  ApiResponse,
  Breadcrumb,
  BrowserConfig,
  Config,
  Entity,
  Folder,
  Pagination,
  PermissionsCollection,
  PinturaOptions,
  QueueEntry,
  QueueEntryStatus,
  View,
} from '__types__'
import { AxiosResponse } from 'axios'
// @ts-ignore todo: fix this
import { Errors } from 'form-backend-validation'
import escape from 'lodash/escape'
import range from 'lodash/range'
import { acceptHMRUpdate, defineStore } from 'pinia'
import Resumable from 'resumablejs'
import { BROWSER_MODAL_NAME, PREVIEW_MODAL_NAME, QUEUE_MODAL_NAME, UPLOAD_MODAL_NAME } from '@/constants'
import { client } from '@/helpers/client'
import { csrf } from '@/helpers/csrf'
import errors from '@/helpers/errors'

interface State {
  path?: string
  disk?: string
  disks?: string[]
  page?: number
  search?: string
  perPage?: number
  perPageOptions: number[]
  view: View
  modals: string[]
  callback?: (selection: Entity[] | undefined) => any

  files?: Entity[]
  folders?: Folder[]
  breadcrumbs?: Breadcrumb[]
  pagination?: Pagination
  errors?: Errors
  selection?: Entity[]
  preview?: Entity
  limit?: number
  queue: QueueEntry[]
  multiple?: boolean

  ready: boolean
  isField: boolean
  isFetchingDisks: boolean
  isFetchingData: boolean
  isUploading: boolean

  dark?: boolean
  tour: boolean

  resource?: string
  resourceId?: string | number
  attribute?: string
  singleDisk: boolean
  flexibleGroup: string[]
  fieldInit?: () => void
  permissions?: PermissionsCollection
  chunkSize: number
  usePintura: boolean
  pinturaOptions?: PinturaOptions
}

const useBrowserStore = defineStore('nova-file-manager', {
  state: (): State => ({
    // browser state
    path: undefined,
    disk: undefined,
    disks: undefined,
    page: undefined,
    search: undefined,
    perPage: 15,
    perPageOptions: range(10, 50, 10),
    view: 'grid',
    modals: [],
    callback: () => {},

    // files, folders and other data
    files: undefined,
    folders: undefined,
    breadcrumbs: undefined,
    pagination: undefined,
    errors: undefined,
    selection: undefined,
    preview: undefined,
    limit: undefined,
    queue: [],
    multiple: undefined,

    // status
    ready: false,
    isField: false,
    isFetchingDisks: false,
    isFetchingData: false,
    isUploading: false,

    // common
    dark: undefined,
    tour: false,

    // field specific state
    resource: undefined,
    resourceId: undefined,
    attribute: undefined,
    singleDisk: false,
    flexibleGroup: [],
    fieldInit: undefined,

    // permissions
    permissions: {
      folder: {
        create: true,
        rename: true,
        delete: true,
      },
      file: {
        upload: true,
        rename: true,
        edit: true,
        delete: true,
        unzip: true,
      },
    },

    // config
    chunkSize: 50 * 1024 * 1024,

    // pintura
    usePintura: false,
    pinturaOptions: {},
  }),

  actions: {
    /**
     * The main entry point for the file manager.
     */
    init() {
      // if we are already initialized, do nothing
      if (this.ready) {
        return
      }

      this.syncDarkMode()

      this.loadFromLocalStorage()

      this.loadFromQueryString()

      this.ready = true
    },

    /**
     * A hook to listen to theme switch to sync the dark mode
     */
    syncDarkMode() {
      if (this.dark === undefined) {
        this.dark = document.documentElement.classList.contains('dark')
      }

      window.Nova.$on('nova-theme-switched', ({ theme }: { theme: 'dark' | 'light' }) => {
        this.dark = theme === 'dark'
      })
    },

    /**
     * Action to retrieve parameters from localStorage
     */
    loadFromLocalStorage() {
      if (this.isField) {
        return
      }

      // we only remember a few parameters
      const parameters = ['perPage', 'view', 'disk']

      // then we can loop on these keys
      parameters.forEach(key => {
        const value = window?.localStorage.getItem(`nova-file-manager/${key}`)

        if (!!value && value.length) {
          this.$patch({ [key]: value })
        }
      })
    },

    /**
     * Action to retrieve parameters from query strings
     */
    loadFromQueryString() {
      if (this.isField) {
        return
      }

      // grab all the query strings in the current url
      const searchParams = Object.fromEntries(new URLSearchParams(window?.location.search).entries())

      // loop on each query string
      for (const [key, value] of Object.entries(searchParams)) {
        // if we match one of these keys, we trigger the setter mutation
        if (['path', 'disk', 'page', 'perPage'].includes(key)) {
          this.$patch({ [key]: value })
        }
      }

      // a trick to set the path to the root if we go back a level
      if (!window.location.href.includes('?')) {
        this.path = '/'
      }
    },

    saveToLocalStorage({ values }: { values: Record<string, string | number | null | undefined> }) {
      if (this.isField || !values) {
        return
      }

      for (const [key, value] of Object.entries(values)) {
        if (value) {
          window?.localStorage.setItem(`nova-file-manager/${key}`, value.toString())
        }
      }
    },

    /**
     * Add a file to current selection
     *
     * @param {Entity} file
     */
    selectFile({ file }: { file: Entity }) {
      if (!this.selection) {
        this.selection = [file]

        return
      }

      this.selection.push(file)
    },

    /**
     * Remove a file from current selection
     */
    deselectFile({ file }: { file: Entity }) {
      this.selection = this.selection?.filter(item => item.id !== file.id)
    },

    /**
     * Set current selection
     */
    setSelection({ files }: { files?: Entity[] }) {
      this.selection = files
    },

    /**
     * Clear current selection
     */
    clearSelection() {
      this.setSelection({ files: undefined })
    },

    /**
     * Toggle selection status for a file
     */
    toggleSelection({ file }: { file: Entity }) {
      if (this.isSelected(file)) {
        this.deselectFile({ file })

        return
      }

      if (!this.multiple) {
        this.clearSelection()
      }

      this.selectFile({ file })
    },

    /**
     * Action to open a modal
     */
    openModal({ name }: { name: string }) {
      this.modals.unshift(name)
    },

    /**
     * Action to close a modal
     */
    closeModal({ name }: { name: string }) {
      if (name === PREVIEW_MODAL_NAME) {
        this.preview = undefined
      }

      this.modals = this.modals.filter(_name => _name !== name)

      this.fixPortal()
    },

    setErrors({ errors }: { errors: any }) {
      this.errors = new Errors(errors)
    },

    resetErrors() {
      this.errors = null
    },

    /**
     * Add a file to the upload queue
     */
    queueFile({ file }: { file: File }) {
      this.queue.push({
        id: file.name,
        ratio: 0,
        status: null,
        file,
        isImage: file.type.includes('image') ?? false,
      })
    },

    /**
     * Clear the current upload queue
     */
    clearQueue() {
      this.queue = []
    },

    updateQueue({ id, ratio = 100, status = null }: { id: string; ratio?: number; status?: QueueEntryStatus }) {
      this.queue = this.queue.map(item => {
        if (item.id === id) {
          return {
            ...item,
            status,
            ratio,
          }
        }

        return item
      })

      const done = this.queue.reduce((carry, item) => carry && item.ratio === 100, true)

      if (done && this.queue.length) {
        setTimeout(async () => {
          this.closeModal({ name: UPLOAD_MODAL_NAME })
          this.closeModal({ name: QUEUE_MODAL_NAME })

          this.clearQueue()

          this.isUploading = false

          await this.data()
        }, 1000)
      }
    },

    /**
     * A temporary fix for broken portal overflow
     */
    fixPortal() {
      if (this.modals.length || !!this.preview) {
        return
      }

      setTimeout(() => {
        // temporary fix
        // @see https://github.com/tailwindlabs/headlessui/issues/1319
        document.documentElement.style.removeProperty('overflow')
        document.documentElement.style.removeProperty('padding-right')
      }, 200)
    },

    /**
     * Update the current query strings with new parameters
     *
     * @param {{[key:string]: string|null}} parameters
     * @returns
     */
    setQueryString({ parameters }: { parameters: Record<string, string | number | null | undefined> }) {
      if (this.isField) {
        return
      }

      const searchParams = new URLSearchParams(window.location.search)

      const page = window.Nova.app.config.globalProperties.$inertia.page

      for (const [key, value] of Object.entries(parameters)) {
        const content = value?.toString()

        if (!content) {
          searchParams.delete(key)

          continue
        }

        if (content?.length > 0) {
          searchParams.set(key, content)
        }
      }

      if (page.url !== `${window.location.pathname}?${searchParams}`) {
        page.url = `${window.location.pathname}?${searchParams}`

        const separator = searchParams.toString().length > 0 ? '?' : ''

        window.history.pushState(page, '', `${window.location.pathname}${separator}${searchParams}`)
      }
    },

    reset() {
      const keys = ['page', 'search', 'path']

      keys.forEach(key => {
        this.$patch({
          [key]: null,
        })
      })
    },

    /**
     * Set the current path
     */
    async setPath({ path }: { path: string | undefined }) {
      this.reset()

      this.path = path

      this.setQueryString({ parameters: { page: null, search: null, path } })
    },

    /**
     * Set the current disk
     */
    async setDisk({ disk }: { disk: string | undefined }) {
      this.reset()

      this.disk = disk

      this.setQueryString({ parameters: { disk } })

      this.saveToLocalStorage({ values: { disk, page: null, search: null, path: null } })
    },

    /**
     * Set the current per page
     */
    async setPerPage({ perPage }: { perPage: number | undefined }) {
      this.perPage = perPage

      this.page = 1

      this.setQueryString({ parameters: { perPage } })

      this.saveToLocalStorage({ values: { perPage } })
    },

    /**
     * Set the current page
     */
    async setPage({ page }: { page: number | undefined }) {
      this.page = page

      this.setQueryString({ parameters: { page } })
    },

    /**
     * Set the view mode
     *
     * @param {string|null} view
     */
    setView({ view }: { view: View }) {
      this.view = view

      this.saveToLocalStorage({ values: { view } })
    },

    /**
     * Set the search query
     */
    setSearch({ search }: { search: string | undefined }) {
      this.search = search

      this.setQueryString({ parameters: { search } })
    },

    setPreview({ preview }: { preview: Entity | undefined }) {
      this.preview = preview
    },

    /**
     * Request data from the API
     *
     * @returns {Promise<void>}
     */
    async data() {
      this.isFetchingData = true

      const { data } = await this.get({
        params: this.payload({
          path: this.path,
          page: this.page,
          perPage: this.perPage,
          search: this.search,
        }),
      })

      this.disk = data.disk
      this.folders = data.folders
      this.breadcrumbs = data.breadcrumbs
      this.files = data.files
      this.pagination = data.pagination

      this.isFetchingData = false
    },

    /**
     * Get the disks from the API
     *
     * @returns {Promise<void>}
     */
    async getDisks() {
      this.isFetchingDisks = true

      const { data } = await this.get({
        path: '/disks/available',
      })

      this.disks = data

      this.isFetchingDisks = false
    },

    async createFolder({ path }: { path: string }) {
      try {
        const response = await this.post<ApiResponse>({
          path: '/folders/create',
          data: this.payload({
            path: escape(`${this.path ?? ''}/${path}`),
          }),
        })

        this.resetErrors()

        window.Nova.success(response.data.message)

        this.closeModal({ name: 'create-folder' })
      } catch (error) {
        this.setErrors({
          errors: {
            createFolder: errors((error as ApiError).response?.data?.errors),
          },
        })
      }
    },

    async renameFolder({ id, from, to }: { id: string; from: string; to: string }) {
      try {
        const response = await this.post<ApiResponse>({
          path: '/folders/rename',
          data: this.payload({
            path: this.path,
            from: escape(from).replace('//', '/'),
            to: escape(`${this.path ?? ''}/${to}`).replace('//', '/'),
          }),
        })

        this.resetErrors()

        window.Nova.success(response.data.message)

        this.closeModal({ name: `rename-folder-${id}` })
      } catch (error) {
        this.setErrors({
          errors: {
            renameFolder: errors((error as ApiError).response?.data?.errors),
          },
        })
      }
    },

    async deleteFolder({ id, path }: { id: string; path: string }) {
      try {
        const response = await this.post<ApiResponse>({
          path: '/folders/delete',
          data: this.payload({
            path: path,
          }),
        })

        this.resetErrors()

        window.Nova.success(response.data.message)

        this.closeModal({ name: `delete-folder-${id}` })
      } catch (error) {
        this.setErrors({
          errors: {
            deleteFolder: errors((error as ApiError).response?.data?.errors),
          },
        })
      }
    },

    /**
     *
     * @param {File[]} files
     * @returns {Promise<void>}
     */
    upload({ files }: { files: File[] }) {
      this.isUploading = true

      const uploader = new Resumable({
        chunkSize: this.chunkSize,
        simultaneousUploads: 1,
        testChunks: false,
        target: this.url('/nova-vendor/nova-file-manager/files/upload'),
        query: this.payload({
          path: this.path ?? '/',
        }),
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrf(),
        },
      })

      files.forEach(file => {
        uploader.addFile(file)

        this.queueFile({ file })
      })

      uploader.on('fileAdded', () => uploader.upload())

      uploader.on('fileSuccess', file => {
        this.updateQueue({
          id: file.fileName,
          status: true,
        })
      })

      uploader.on('fileProgress', file => {
        this.updateQueue({
          id: file.fileName,
          ratio: Math.floor(file.progress(false) * 100),
        })
      })

      uploader.on('fileError', (file, message) => {
        this.updateQueue({
          id: file.fileName,
          status: false,
        })

        window.Nova.error(JSON.parse(message).message)
      })
    },

    async renameFile({ id, from, to }: { id: string; from: string; to: string }) {
      try {
        const response = await this.post<ApiResponse>({
          path: '/files/rename',
          data: this.payload({
            path: this.path,
            from: escape(from).replace('//', '/'),
            to: escape(`${this.path ?? ''}/${to}`).replace('//', '/'),
          }),
        })

        this.resetErrors()

        window.Nova.success(response.data.message)

        this.preview = undefined

        this.closeModal({ name: `rename-file-${id}` })
      } catch (error) {
        this.setErrors({
          errors: {
            renameFile: errors((error as ApiError).response?.data?.errors),
          },
        })
      }
    },

    async deleteFile({ id, path }: { id: string; path: string }) {
      try {
        const response = await this.post<ApiResponse>({
          path: '/files/delete',
          data: this.payload({
            path: path,
          }),
        })

        this.resetErrors()

        window.Nova.success(response.data.message)

        this.preview = undefined

        this.closeModal({ name: `delete-file-${id}` })

        this.clearSelection()
      } catch (error) {
        this.setErrors({
          errors: {
            deleteFile: errors((error as ApiError).response?.data?.errors),
          },
        })
      }
    },

    async unzipFile({ path }: { path: string }) {
      try {
        const response = await this.post<ApiResponse>({
          path: '/files/unzip',
          data: this.payload({
            path: path,
          }),
        })

        this.resetErrors()

        window.Nova.success(response.data.message)

        this.preview = undefined

        this.clearSelection()
      } catch (error) {
        this.setErrors({
          errors: {
            unzipFile: errors((error as ApiError).response?.data?.errors),
          },
        })
      }
    },

    /**
     * GET request wrapper
     */
    async get({ path, params, options = {} }: { path?: string; params?: object; options?: object }) {
      return await client().get(this.url(`/nova-vendor/nova-file-manager${path ?? '/'}`), {
        params,
        ...options,
      })
    },

    /**
     * POST request wrapper
     */
    async post<T>({ path, data }: { path?: string; data?: Record<string, any> }): Promise<AxiosResponse<T>> {
      return await client().post<T>(this.url(`/nova-vendor/nova-file-manager${path ?? '/'}`), data)
    },

    payload(params: object) {
      let data: {
        [key: string]: any
        attribute?: string
        resource?: string
        fieldMode: boolean
        resourceId?: string | number
        disk?: string
        flexible?: string
      } = {
        ...params,
        attribute: this.attribute,
        resource: this.resource,
        fieldMode: this.isField,
        resourceId: undefined,
        disk: undefined,
        flexible: undefined,
      }

      if (this.resourceId) {
        data = {
          ...data,
          resourceId: this.resourceId,
        }
      }

      if (!this.singleDisk) {
        data = {
          ...data,
          disk: this.disk,
        }
      }

      if (this.isField && this.flexibleGroup?.length) {
        data = {
          ...data,
          flexible: this.flexibleGroup.join('.'),
        }
      }

      return data
    },

    url(url: string) {
      const suffix = this.isField ? `/${this.resource}` : ''

      return `${url}${suffix}`.replace('//', '/')
    },

    openBrowser({
      initialFiles,
      multiple,
      limit,
      resource,
      resourceId,
      attribute,
      singleDisk,
      permissions,
      flexibleGroup,
      callback,
      usePintura,
      pinturaOptions,
    }: BrowserConfig) {
      this.isField = true
      this.multiple = multiple
      this.limit = limit
      this.resource = resource
      this.resourceId = resourceId
      this.attribute = attribute
      this.singleDisk = singleDisk
      this.flexibleGroup = flexibleGroup
      this.callback = callback
      this.usePintura = usePintura
      this.pinturaOptions = pinturaOptions
      this.errors = undefined
      this.permissions = permissions
      this.disk = undefined

      this.openModal({ name: BROWSER_MODAL_NAME })
      this.setSelection({ files: [...initialFiles] })
    },

    closeBrowser() {
      this.isField = false
      this.multiple = undefined
      this.limit = undefined
      this.resource = undefined
      this.resourceId = undefined
      this.attribute = undefined
      this.singleDisk = false
      this.flexibleGroup = []
      this.callback = undefined
      this.usePintura = false
      this.pinturaOptions = {}
      this.errors = undefined
      this.permissions = undefined
      this.disk = undefined

      this.setSelection({ files: [] })
      this.closeModal({ name: BROWSER_MODAL_NAME })
    },

    confirm() {
      this.callback && this.callback(this.selection)

      this.closeBrowser()
    },

    prepareTool({ singleDisk, permissions, tour, usePintura, pinturaOptions }: Config) {
      this.init()
      this.clearSelection()

      this.limit = undefined
      this.isField = false
      this.multiple = true
      this.singleDisk = singleDisk
      this.permissions = permissions
      this.tour = tour
      this.usePintura = usePintura
      this.pinturaOptions = pinturaOptions
      this.errors = undefined
    },
  },
  getters: {
    isOpen() {
      return (name: string) => {
        if (name === PREVIEW_MODAL_NAME) {
          return !!this.preview
        }

        return this.modals.includes(name)
      }
    },
    isSelected() {
      return (file: Entity) => !!this.selection?.find(item => item.id === file.id)
    },
    isBrowserOpen() {
      // @ts-ignore false positive
      return this.isOpen('browser')
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useBrowserStore, import.meta.hot))
}

export default useBrowserStore
