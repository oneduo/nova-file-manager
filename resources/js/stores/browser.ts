import {
  ApiError,
  ApiResponse,
  Breadcrumb,
  BrowserConfig,
  Entity,
  Folder,
  Pagination,
  PermissionsCollection,
  PinturaOptions,
  QueueEntry,
  QueueEntryStatus,
  ToolProps,
  View,
} from '__types__'
import { AxiosResponse } from 'axios'
// @ts-ignore todo: fix this
import { Errors } from 'form-backend-validation'
import escape from 'lodash/escape'
import range from 'lodash/range'
import { acceptHMRUpdate, defineStore } from 'pinia'
import Resumable from 'resumablejs'
import { PREVIEW_MODAL_NAME, QUEUE_MODAL_NAME, UPLOAD_MODAL_NAME } from '@/constants'
import { client } from '@/helpers/client'
import { csrf } from '@/helpers/csrf'
import errors from '@/helpers/errors'

const useBrowserStore = defineStore('nova-file-manager', {
  state: () => ({
    // browser state
    path: null as string | null,
    disk: null as string | null,
    disks: null as string[] | null,
    page: null as number | null,
    search: null as string | null,
    perPage: 15 as number | null,
    perPageOptions: range(10, 50, 10),
    view: 'grid' as View,
    modals: [] as string[],
    callback: undefined as ((...params: any[]) => any) | undefined,

    // files, folders and other data
    files: null as Entity[] | null,
    folders: null as Folder[] | null,
    breadcrumbs: null as Breadcrumb[] | null,
    pagination: null as Pagination | null,
    errors: null as Errors | null,
    selection: undefined as Entity[] | undefined,
    preview: null as Entity | null,
    limit: 1 as number | null,
    queue: [] as QueueEntry[],
    multiple: false as boolean | null,

    // status
    ready: false,
    isField: false,
    isFetchingDisks: false,
    isFetchingData: false,
    isUploading: false,

    // common
    dark: undefined as boolean | undefined,
    tour: false,

    // field specific state
    resource: null as string | null,
    resourceId: null as string | number | null,
    attribute: null as string | number | null,
    singleDisk: false,
    flexibleGroup: [] as any[],
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
    } as PermissionsCollection | undefined,

    // config
    chunkSize: 50 * 1024 * 1024,

    // pintura
    usePintura: false as boolean | undefined,
    pinturaOptions: {} as PinturaOptions | undefined,
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

    saveToLocalStorage({ values }: { values: Record<string, string | number | null> }) {
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
        this.preview = null
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
    setQueryString({ parameters }: { parameters: Record<string, string | number | null> }) {
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
     *
     * @param path
     * @returns {Promise<void>}
     */
    async setPath({ path }: { path: string | null }) {
      this.reset()

      this.path = path

      this.setQueryString({ parameters: { page: null, search: null, path } })
    },

    /**
     * Set the current disk
     *
     * @param {string|null} disk
     * @returns {Promise<void>}
     */
    async setDisk({ disk }: { disk: string | null }) {
      this.reset()

      this.disk = disk

      this.setQueryString({ parameters: { disk } })

      this.saveToLocalStorage({ values: { disk, page: null, search: null, path: null } })
    },

    /**
     * Set the current per page
     *
     * @param {number|null} perPage
     * @returns {Promise<void>}
     */
    async setPerPage({ perPage }: { perPage: number | null }) {
      this.perPage = perPage

      this.page = 1

      this.setQueryString({ parameters: { perPage } })

      this.saveToLocalStorage({ values: { perPage } })
    },

    /**
     * Set the current page
     *
     * @param {number|null} page
     * @returns {Promise<void>}
     */
    async setPage({ page }: { page: number | null }) {
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
     *
     * @param {search|null} search
     * @returns {Promise<void>}
     */
    setSearch({ search }: { search: string | null }) {
      this.search = search

      this.setQueryString({ parameters: { search } })
    },

    setPreview({ preview }: { preview: Entity | null }) {
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

        this.preview = null

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

        this.preview = null

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

        this.preview = null

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
     *
     * @param {string|null|undefined} path
     * @param {Object|null|undefined} params
     * @param {Object|null|undefined} options
     * @returns {Promise<*>}
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
      let data = {
        ...params,
        attribute: this.attribute,
        resource: this.resource,
        fieldMode: this.isField,
        resourceId: null as string | number | null,
        disk: null as string | null,
        flexible: null as string | null,
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
      this.errors = null

      this.setSelection({ files: [...initialFiles] })

      this.permissions = permissions

      this.disk = null

      this.openModal({ name: 'browser' })
    },

    closeBrowser() {
      this.isField = false
      this.multiple = null
      this.limit = null
      this.resource = null
      this.resourceId = null
      this.attribute = null
      this.singleDisk = false
      this.flexibleGroup = []
      this.callback = undefined
      this.usePintura = false
      this.pinturaOptions = {}
      this.errors = null

      this.setSelection({ files: [] })

      this.permissions = undefined

      this.disk = null

      this.closeModal({ name: 'browser' })
    },

    confirm() {
      this.callback && this.callback(this.selection)

      this.closeBrowser()
    },

    prepareTool({ singleDisk, permissions, tour, usePintura, pinturaOptions }: ToolProps) {
      this.init()
      this.clearSelection()

      this.limit = null
      this.isField = false
      this.multiple = true
      this.singleDisk = singleDisk
      this.permissions = permissions
      this.tour = tour
      this.usePintura = usePintura
      this.pinturaOptions = pinturaOptions
      this.errors = null
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
