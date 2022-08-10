import Resumable from 'resumablejs'
import { Errors } from 'form-backend-validation'
import sanitize from '@/helpers/sanitize'
import errors from '@/helpers/errors'

const store = {
  namespaced: true,
  state() {
    return {
      // file browser state
      path: null,
      disk: null,
      disks: null,
      page: null,
      search: null,
      perPage: 15,
      perPageOptions: [5, 15, 30, 50],

      // files, directories and other data
      files: null,
      directories: null,
      breadcrumbs: null,
      pagination: null,
      selectedFile: null,
      fieldValue: null,
      errors: null,
      selection: [],
      preview: null,
      limit: 1,

      // status
      ready: false,
      isFieldMode: false,
      isFetchingDisks: false,
      isFetchingData: false,
      isUploading: false,
      isPreviewOpen: false,

      // misc
      darkMode: false,
      darkModeObserver: null,
      view: 'grid',
      toolModals: [],
      csrfToken: null,

      currentField: null,
      fields: {},
    }
  },
  mutations: {
    // This is the main mutation that is being evaluated when the browser is mounted
    init(state) {
      if (state.ready) {
        return
      }

      this.commit('nova-file-manager/detectDarkMode')
      this.commit('nova-file-manager/loadFromLocalStorage')
      this.commit('nova-file-manager/setFromQueryString')

      state.csrfToken = document.head.querySelector('meta[name="csrf-token"]').content
      state.ready = true
    },

    // This mutation will be evaluated when the browser is unmounted
    destroy(state) {
      state.darkModeObserver?.disconnect()
      state.darkModeObserver = null
    },

    // this cool function will allow us to listen to any color scheme change so as to sync the browser's dark mode with nova's
    detectDarkMode(state) {
      state.darkMode = document.documentElement.classList.contains('dark')

      if (state.darkModeObserver === null) {
        state.darkModeObserver = new MutationObserver((records) => {
          records.forEach((record) => {
            state.darkMode = record.target?.classList.contains('dark')
          })
        })

        state.darkModeObserver.observe(document.documentElement, {
          attributes: true,
          attributeFilter: ['class'],
          childList: false,
          characterData: false,
        })
      }
    },

    // we save the current state to localStorage, and this handy function allows us to retrieve it
    loadFromLocalStorage(state) {
      if (state.isFieldMode) {
        return
      }

      // we only remember a few parameters
      const keysToRetrieve = ['perPage', 'view', 'disk']

      // then we can loop on these keys
      keysToRetrieve.forEach((key) => {
        const value = window?.localStorage.getItem(`nova-file-manager::${key}`)

        if (value) {
          // and then trigger the corresponding setter mutation
          this.commit(`nova-file-manager/set${key.charAt(0).toUpperCase() + key.slice(1)}`, value)
        }
      })
    },

    // when accessing the browser through a url that contains a query string, we can use this mutation to set the state from the query string
    setFromQueryString(state) {
      if (state.isFieldMode) {
        return
      }

      // grab all the query strings in the current url
      const searchParams = Object.fromEntries(
        new URLSearchParams(window?.location.search).entries(),
      )

      // loop on each query string
      for (const [key, value] of Object.entries(searchParams)) {
        // if we match one of these keys, we trigger the setter mutation
        if (['path', 'disk', 'page', 'perPage'].includes(key)) {
          this.commit(`nova-file-manager/set${key.charAt(0).toUpperCase() + key.slice(1)}`, value)
        }
      }

      // a trick to set the path to the root if we go back a level
      if (!window.location.href.includes('?')) {
        this.commit('nova-file-manager/setPath', '/')
      }
    },

    selectFile(state, file) {
      if (state.isFieldMode) {
        if (state.currentField?.selection?.length === state.currentField?.limit) {
          return
        }

        state.currentField.selection = [file, ...state.currentField?.selection]
      } else {
        if (state.selection?.length === state.limit) {
          return
        }

        state.selection = [file, ...state.selection]
      }
    },

    deselectFile(state, file) {
      if (state.isFieldMode) {
        state.currentField.selection = state.currentField?.selection.filter((_file) => _file.id !== file.id)
      } else {
        state.selection = state.selection.filter((_file) => _file.id !== file.id)
      }
    },

    deselectFieldFile(state, { field, file }) {
      state.fields[field].selection = state.fields[field].selection.filter((_file) => _file.id !== file.id)
    },

    previewFile(state, file) {
      state.preview = file
    },

    // setters
    setIsFieldMode(state, value) {
      state.isFieldMode = value
    },
    setPath(state, path) {
      state.path = path
    },
    setDisk(state, disk) {
      state.disk = disk
    },
    setDisks(state, disks) {
      state.disks = disks
    },
    setView(state, view) {
      state.view = view
    },
    setPage(state, page) {
      state.page = page
    },
    setPerPage(state, perPage) {
      state.perPage = perPage
    },
    setSelectedFile(state, file) {
      state.selectedFile = file
    },
    setFiles(state, files) {
      state.files = files
    },
    setDirectories(state, directories) {
      state.directories = directories
    },
    setBreadcrumbs(state, breadcrumbs) {
      state.breadcrumbs = breadcrumbs
    },
    setPagination(state, pagination) {
      state.pagination = pagination
    },
    setIsFetchingData(state, isFetchingData) {
      state.isFetchingData = isFetchingData
    },
    setIsFetchingDisks(state, isFetchingDisks) {
      state.isFetchingDisks = isFetchingDisks
    },
    setIsUploading(state, isUploading) {
      state.isUploading = isUploading
    },
    setErrors(state, errors) {
      state.errors = new Errors(errors)
    },
    toggleIsPreviewOpen(state) {
      state.isPreviewOpen = !state.isPreviewOpen
    },
    setValue(state, value) {
      state.fieldValue = value
    },
    setSearch(state, value) {
      state.search = value?.length ? value : null
    },
    setLimit(state, limit) {
      state.limit = limit
    },
    setSelection(state, value) {
      state.selection = value
    },
    setFieldSelection(state, { attribute, value}) {
      state.fields[attribute].selection = value
    },
    openModal: (state, payload) => {
      state.toolModals.unshift(payload)
    },
    closeModal: (state, payload) => {
      state.toolModals = state.toolModals.filter((name) => name !== payload)
    },
    initField: (state, { attribute, limit, selection }) => {
      state.fields[attribute] = { limit, selection }
    },
    setCurrentField: (state, field) => {
      state.currentField = !!field ? state.fields[field] : null
    },
    fixPortal: (state) => {
      if (state.toolModals.length || !!state.preview) {
        return
      }

      setTimeout(() => {
        // temporary fix
        // @see https://github.com/tailwindlabs/headlessui/issues/1319
        document.documentElement.style.removeProperty('overflow')
        document.documentElement.style.removeProperty('padding-right')
      }, 250)
    }
  },
  actions: {
    setPath({ state, commit, dispatch }, path) {
      dispatch('reset')
      commit('setPath', path)
      dispatch('getData')
      dispatch('updateQueryString', { path })
    },
    setDisk({ commit, dispatch }, disk) {
      dispatch('reset')
      commit('setDisk', disk)
      dispatch('getData')
      dispatch('updateQueryString', { disk })
      dispatch('saveToLocalStorage', { disk })
    },
    setPerPage({ commit, dispatch }, perPage) {
      commit('setPerPage', perPage)
      dispatch('getData')
      dispatch('updateQueryString', { perPage })
      dispatch('saveToLocalStorage', { perPage })
    },
    setPage({ commit, dispatch }, page) {
      commit('setPage', page)
      dispatch('getData')
      dispatch('updateQueryString', { page })
    },
    setView({ commit, dispatch }, view) {
      commit('setView', view)
      dispatch('saveToLocalStorage', { view })
    },
    setSearch({ commit, dispatch }, search) {
      commit('setSearch', search)
      dispatch('getData')
      dispatch('updateQueryString', { search })
    },
    reset({ commit, dispatch }) {
      const keys = ['page', 'search']

      keys.forEach((key) => {
        commit(`set${key.charAt(0).toUpperCase() + key.slice(1)}`, null)
        dispatch(`updateQueryString`, { [key]: null })
      })
    },
    async getDisks({ commit, state }) {
      commit('setIsFetchingDisks', true)

      const { data } = await Nova.request().get('/nova-vendor/nova-file-manager/disks/available')

      commit('setDisks', data)

      commit('setIsFetchingDisks', false)
    },
    async getData({ state, commit }) {
      commit('setIsFetchingData', true)

      const { data } = await Nova.request().get('/nova-vendor/nova-file-manager', {
        params: {
          disk: state.disk,
          path: state.path,
          page: state.page,
          perPage: state.perPage,
          search: state.search,
        },
      })

      commit('setDisk', data.disk)
      commit('setDirectories', data.directories)
      commit('setBreadcrumbs', data.breadcrumbs)
      commit('setFiles', data.files)
      commit('setPagination', data.pagination)

      commit('setIsFetchingData', false)
    },
    async createFolder({ dispatch, state, commit }, path) {
      try {
        const response = await Nova.request().post(
          '/nova-vendor/nova-file-manager/folders/create',
          {
            path: sanitize(`${state.path ?? ''}/${path}`),
            disk: state.disk,
          },
        )

        Nova.success(response.data.message)

        dispatch('closeModal', 'createFolder')
        dispatch('getData')
      } catch (error) {
        commit('setErrors', { createFolder: errors(error.response?.data?.errors) })
      }
    },
    async renameFolder({ dispatch, state, commit }, { id, oldPath, newPath }) {
      try {
        const response = await Nova.request().post(
          '/nova-vendor/nova-file-manager/folders/rename',
          {
            path: state.path,
            disk: state.disk,
            oldPath: sanitize(`${state.path ?? ''}/${oldPath}`),
            newPath: sanitize(`${state.path ?? ''}/${newPath}`),
          },
        )

        Nova.success(response.data.message)

        dispatch('closeModal', `renameFolder-${id}`)
        dispatch('getData')
      } catch (error) {
        commit('setErrors', { renameFolder: errors(error.response?.data?.errors) })
      }
    },
    async deleteFolder({ dispatch, state, commit }, { id, path }) {
      try {
        const response = await Nova.request().post(
          '/nova-vendor/nova-file-manager/folders/delete',
          {
            path: path,
            disk: state.disk,
          },
        )

        Nova.success(response.data.message)

        dispatch('closeModal', `deleteFolder-${id}`)
        dispatch('getData')
      } catch (error) {
        commit('setErrors', { renameFolder: errors(error.response?.data?.errors) })
      }
    },

    async upload({ dispatch, state, commit }, file) {
      commit('setIsUploading', true)

      const uploader = new Resumable({
        chunkSize: 20 * 1024 * 1024,
        simultaneousUploads: 3,
        testChunks: false,
        throttleProgressCallbacks: 1,
        target: '/nova-vendor/nova-file-manager/files/upload',
        query: {
          disk: state.disk,
          path: state.path ?? '/',
        },
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': state.csrfToken,
        },
      })

      uploader.on('fileAdded', (_file, _event) => {
        uploader.upload()
      })

      uploader.on('fileSuccess', (file, response) => {
        const parsed = JSON.parse(response)

        Nova.success(parsed.message)

        dispatch('closeModal', 'upload')
        commit('setIsUploading', false)
        dispatch('getData')
      })

      uploader.on('fileError', (_file, message) => {
        Nova.error(message)
        dispatch('closeModal', 'upload')
        commit('setIsUploading', false)
      })

      uploader.addFile(file)
    },
    async renameFile({ dispatch, state, commit }, { id, oldPath, newPath }) {
      try {
        const response = await Nova.request().post('/nova-vendor/nova-file-manager/files/rename', {
          path: state.path,
          disk: state.disk,
          oldPath: sanitize(`${state.path ?? ''}/${oldPath}`),
          newPath: sanitize(`${state.path ?? ''}/${newPath}`),
        })

        Nova.success(response.data.message)

        dispatch('closeModal', `renameFile-${id}`)
        commit('setSelectedFile', null)
        dispatch('getData')
      } catch (error) {
        commit('setErrors', { renameFile: errors(error.response?.data?.errors) })
      }
    },
    async deleteFile({ dispatch, state, commit }, { id, path }) {
      try {
        const response = await Nova.request().post('/nova-vendor/nova-file-manager/files/delete', {
          path: path,
          disk: state.disk,
        })

        Nova.success(response.data.message)

        dispatch('closeModal', `deleteFile-${id}`)
        commit('previewFile', null)
        dispatch('getData')
      } catch (error) {
        commit('setErrors', { deleteFile: errors(error.response?.data?.errors) })
      }
    },
    updateQueryString({ state }, values) {
      if (state.isFieldMode) {
        return
      }

      const searchParams = new URLSearchParams(window.location.search)

      const page = Nova.app.config.globalProperties.$inertia.page

      for (const [key, value] of Object.entries(values)) {
        if (value?.length > 0) {
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

    saveToLocalStorage({ state }, values) {
      if (state.isFieldMode) {
        return
      }

      for (const [key, value] of Object.entries(values)) {
        window?.localStorage.setItem(`nova-file-manager::${key}`, value)
      }
    },

    openModal: ({ commit }, payload) => {
      commit('openModal', payload)
    },

    closeModal: ({ commit }, payload) => {
      commit('closeModal', payload)
      commit('fixPortal')
    },

    closeBrowser: ({ state, dispatch, commit }) => {
      commit('setValue', state.selectedFile)
      commit('setSelectedFile', null)
      dispatch('closeModal', 'browser')
    },
  },
  getters: {
    active: (state) => (state.toolModals.length > 0 ? state.toolModals[0] : null),
    allModals: (state) => state.toolModals,
    selection: (state) => state.selection,
    isFileSelected: (state) => (file) => {
      if (state.isFieldMode) {
        return state.currentField?.selection.find((item) => item.id === file.id)
      } else {
        return state.selection.find((item) => item.id === file.id)
      }
    },
    fieldByAttribute: (state) => (attribute) => state.fields[attribute]
  },
}

export { store }
