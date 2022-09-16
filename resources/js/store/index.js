import { acceptHMRUpdate, defineStore } from 'pinia'
import { Errors } from 'form-backend-validation'
import errors from '@/helpers/errors'
import escape from 'lodash/escape'
import Resumable from 'resumablejs'
import { range } from 'lodash/util'
import { client } from '@/helpers/client'

const useStore = defineStore('nova-file-manager', {
  state: () => ({
    // browser state
    path: null,
    disk: null,
    disks: null,
    page: null,
    search: null,
    perPage: 15,
    perPageOptions: range(10, 50, 10),
    view: 'grid',
    modals: [],

    // files, folders and other data
    files: null,
    folders: null,
    breadcrumbs: null,
    pagination: null,
    errors: null,
    selection: undefined,
    preview: null,
    limit: 1,
    queue: [],
    multiple: false,

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
    resource: null,
    resourceId: null,
    attribute: null,
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

      Nova.$on('nova-theme-switched', ({ theme }) => {
        this.dark = theme !== 'dark'
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
          this[key] = value
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
      const searchParams = Object.fromEntries(
        new URLSearchParams(window?.location.search).entries()
      )

      // loop on each query string
      for (const [key, value] of Object.entries(searchParams)) {
        // if we match one of these keys, we trigger the setter mutation
        if (['path', 'disk', 'page', 'perPage'].includes(key)) {
          this[key] = value
        }
      }

      // a trick to set the path to the root if we go back a level
      if (!window.location.href.includes('?')) {
        this.path = '/'
      }
    },

    saveToLocalStorage({ values }) {
      if (this.isField || !values) {
        return
      }

      for (const [key, value] of Object.entries(values)) {
        window?.localStorage.setItem(`nova-file-manager/${key}`, value)
      }
    },

    /**
     * Add a file to current selection
     *
     * @param {Entity} file
     */
    selectFile({ file }) {
      if (!this.selection) {
        this.selection = [file]

        return
      }

      this.selection.push(file)
    },

    /**
     * Remove a file from current selection
     *
     * @param {Entity} file
     */
    deselectFile({ file }) {
      this.selection = this.selection.filter(item => item.id !== file.id)
    },

    /**
     * Set current selection
     *
     * @param {Entity[]|null|undefined} files
     */
    setSelection({ files }) {
      this.selection = files
    },

    /**
     * Clear current selection
     */
    clearSelection() {
      this.selection = []
    },

    /**
     * Toggle selection status for a file
     *
     * @param {Entity} file
     */
    toggleSelection({ file }) {
      const alreadySelected = !!this.isSelected(file)

      if (alreadySelected) {
        this.deselectFile({ file })

        return
      }

      if (!this.multiple) {
        this.setSelection({ files: [] })
      }

      this.selectFile({ file })
    },

    /**
     * Action to open a modal
     *
     * @param {string} name
     */
    openModal({ name }) {
      if (!name) {
        return
      }

      this.modals.unshift(name)
    },

    /**
     * Action to close a modal
     *
     * @param {string} name
     */
    closeModal({ name }) {
      if (!name) {
        return
      }

      this.modals = this.modals.filter(_name => _name !== name)

      this.fixPortal()
    },

    setErrors({ errors }) {
      this.errors = new Errors(errors)
    },

    /**
     * Add a file to the upload queue
     *
     * @param {File} file
     */
    queueFile({ file }) {
      this.queue.push({
        id: file.name,
        isImage: file.type.includes('image') ?? false,
        ratio: 0,
        status: null,
        file,
      })
    },

    /**
     * Clear the current upload queue
     */
    clearQueue() {
      this.queue = []
    },

    updateQueue({ id, ratio = 100, status = null }) {
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
          this.closeModal({ name: 'upload' })
          this.closeModal({ name: 'queue' })

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
    setQueryString({ parameters }) {
      if (this.isField) {
        return
      }

      const searchParams = new URLSearchParams(window.location.search)

      const page = Nova.app.config.globalProperties.$inertia.page

      for (const [key, value] of Object.entries(parameters)) {
        if (value?.toString().length > 0) {
          searchParams.set(key, value)
        } else {
          searchParams.delete(key)
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
        this[key] = null
      })
    },

    /**
     * Set the current path
     *
     * @param path
     * @returns {Promise<void>}
     */
    async setPath({ path }) {
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
    async setDisk({ disk }) {
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
    async setPerPage({ perPage }) {
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
    async setPage({ page }) {
      this.page = page

      this.setQueryString({ parameters: { page } })
    },

    /**
     * Set the view mode
     *
     * @param {string|null} view
     */
    setView({ view }) {
      this.view = view

      this.saveToLocalStorage({ values: { view } })
    },

    /**
     * Set the search query
     *
     * @param {search|null} search
     * @returns {Promise<void>}
     */
    setSearch({ search }) {
      this.search = search

      this.setQueryString({ parameters: { search } })
    },

    setPreview({ preview }) {
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

    async createFolder({ path }) {
      try {
        const response = await this.post({
          path: '/folders/create',
          data: this.payload({
            path: escape(`${this.path ?? ''}/${path}`),
          }),
        })

        Nova.success(response.data.message)

        this.closeModal({ name: 'create-folder' })
      } catch (error) {
        this.setErrors({
          errors: {
            createFolder: errors(error.response?.data?.errors),
          },
        })
      }
    },

    async renameFolder({ id, from, to }) {
      try {
        const response = await this.post({
          path: '/folders/rename',
          data: this.payload({
            path: this.path,
            oldPath: escape(from),
            newPath: escape(`${this.path ?? ''}/${to}`),
          }),
        })

        Nova.success(response.data.message)

        this.closeModal({ name: `rename-folder-${id}` })
      } catch (error) {
        this.setErrors({
          errors: {
            renameFolder: errors(error.response?.data?.errors),
          },
        })
      }
    },

    async deleteFolder({ id, path }) {
      try {
        const response = await this.post({
          path: '/folders/delete',
          data: this.payload({
            path: path,
          }),
        })

        Nova.success(response.data.message)

        this.closeModal({ name: `delete-folder-${id}` })
      } catch (error) {
        this.setErrors({
          errors: {
            deleteFolder: errors(error.response?.data?.errors),
          },
        })
      }
    },

    /**
     *
     * @param {File[]} files
     * @returns {Promise<void>}
     */
    upload({ files }) {
      this.isUploading = true

      const uploader = new Resumable({
        chunkSize: this.chunkSize,
        simultaneousUploads: 1,
        testChunks: false,
        throttleProgressCallbacks: 1,
        target: this.url('/nova-vendor/nova-file-manager/files/upload'),
        query: this.payload({
          path: this.path ?? '/',
        }),
        minFileSize: 0,
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
        },
        permanentErrors: [400, 404, 409, 415, 422, 500, 501],
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
          ratio: Math.floor(file.progress() * 100),
        })
      })

      uploader.on('fileError', (file, message) => {
        this.updateQueue({
          id: file.fileName,
          status: false,
        })

        Nova.error(JSON.parse(message).message)
      })
    },

    async renameFile({ id, from, to }) {
      try {
        const response = await this.post({
          path: '/files/rename',
          data: this.payload({
            path: this.path,
            oldPath: escape(from),
            newPath: escape(`${this.path ?? ''}/${to}`),
          }),
        })

        Nova.success(response.data.message)

        this.preview = null

        this.closeModal({ name: `rename-file-${id}` })
      } catch (error) {
        this.setErrors({
          renameFile: errors(error.response?.data?.errors),
        })
      }
    },

    async deleteFile({ id, path }) {
      try {
        const response = await this.post({
          path: '/files/delete',
          data: this.payload({
            path: path,
          }),
        })

        Nova.success(response.data.message)

        this.preview = null

        this.closeModal({ name: `delete-file-${id}` })

        this.clearSelection()
      } catch (error) {
        this.setErrors({
          deleteFile: errors(error.response?.data?.errors),
        })
      }
    },

    async unzipFile({ path }) {
      try {
        const response = await this.post({
          path: '/files/unzip',
          data: this.payload({
            path: path,
          }),
        })

        Nova.success(response.data.message)

        this.preview = null

        this.clearSelection()
      } catch (error) {
        this.setErrors({
          unzipFile: errors(error.response?.data?.errors),
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
    async get({ path, params, options = {} }) {
      return await client().get(`/nova-vendor/nova-file-manager${path ?? '/'}`, {
        params,
        ...options,
      })
    },

    /**
     * POST request wrapper
     *
     * @param {string|null} path
     * @param {Object|null} params
     * @returns {Promise<*>}
     */
    async post({ path, data }) {
      return await client().post(`/nova-vendor/nova-file-manager${path ?? '/'}`, data)
    },

    payload(params) {
      return {
        ...params,
        attribute: this.attribute,
        resource: this.resource,
        ...(this.resourceId && {
          resourceId: this.resourceId,
        }),
        ...(!this.singleDisk && {
          disk: this.disk,
        }),
        fieldMode: this.isField,
        ...(this.isField &&
          this.flexibleGroup?.length && {
          flexible: this.flexibleGroup.join('.'),
        }),
      }
    },

    url(url) {
      const suffifx = this.isField ? `/${this.resource}` : ''

      return `${url}${suffifx}`.replace('//', '/')
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
    }) {
      this.isField = true
      this.multiple = multiple
      this.limit = limit
      this.resource = resource
      this.resourceId = resourceId
      this.attribute = attribute
      this.singleDisk = singleDisk
      this.flexibleGroup = flexibleGroup
      this.callback = callback

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
      this.flexibleGroup = null
      this.callback = null

      this.setSelection({ files: [] })

      this.permissions = null

      this.disk = null

      this.closeModal({ name: 'browser' })
    },

    confirm() {
      this.callback(this.selection)

      this.closeBrowser()
    },

    prepareTool({ singleDisk, permissions, tour }) {
      this.init()
      this.clearSelection()

      this.limit = null
      this.isField = false
      this.multiple = true
      this.singleDisk = singleDisk
      this.permissions = permissions
      this.tour = tour
    },
  },
  getters: {
    isOpen() {
      return name => this.modals.includes(name)
    },
    isSelected() {
      return file => !!this.selection?.find(item => item.id === file.id)
    },
    isBrowserOpen() {
      return this.isOpen('browser')
    },
  },
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useStore, import.meta.hot))
}

export { useStore }
