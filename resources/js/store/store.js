import sanitize from '@/helpers/sanitize'
import { Errors } from 'form-backend-validation'
import { darkMode } from '../../../tailwind.config'
import Resumable from 'resumablejs'
import maperrors from '@/helpers/maperrors'

const store = {
  namespaced: true,
  state() {
    return {
      // browser state
      path: null,
      disk: null,
      disks: null,
      page: null,
      perPage: 15,
      perPageOptions: [5, 15, 30, 50],

      // content
      files: null,
      directories: null,
      breadcrumbs: null,
      pagination: null,
      selectedFile: null,
      fieldValue: null,
      errors: null,

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
    }
  },
  mutations: {
    init(state) {
      this.commit('nova-file-manager/detectDarkMode')
      this.commit('nova-file-manager/loadFromLocalStorage')
      this.commit('nova-file-manager/setFromQueryString')

      state.csrfToken = document.head.querySelector('meta[name="csrf-token"]').content
      state.ready = true
    },
    destroy(state) {
      state.darkModeObserver?.disconnect()
      state.darkModeObserver = null
    },
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
    loadFromLocalStorage() {
      const keys = ['perPage', 'view', 'disk']

      keys.forEach((key) => {
        const value = window?.localStorage.getItem(`nova-file-manager::${key}`)

        if (value) {
          this.commit(`nova-file-manager/set${key.charAt(0).toUpperCase() + key.slice(1)}`, value)
        }
      })
    },
    setFromQueryString() {
      const searchParams = Object.fromEntries(new URLSearchParams(window.location.search).entries())

      for (const [key, value] of Object.entries(searchParams)) {
        this.commit(`nova-file-manager/set${key.charAt(0).toUpperCase() + key.slice(1)}`, value)
      }
    },
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
    OPEN: (state, payload) => state.toolModals.unshift(payload),
    CLOSE: (state, payload) => (state.toolModals = state.toolModals.filter((e) => e !== payload)),
  },
  actions: {
    setPath({ commit, dispatch }, path) {
      commit('setPath', path)
      dispatch('getData')
      dispatch('updateQueryString', { path })
    },
    setDisk({ commit, dispatch }, disk) {
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
        commit('setErrors', { createFolder: maperrors(error.response?.data?.errors) })
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
        commit('setErrors', { renameFolder: maperrors(error.response?.data?.errors) })
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
        commit('setErrors', { renameFolder: maperrors(error.response?.data?.errors) })
      }
    },
    async upload({ dispatch, state, commit }, file) {
      commit('setIsUploading', true)

      const uploader = new Resumable({
        chunkSize: 10 * 1024 * 1024,
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
        commit('setErrors', { renameFile: maperrors(error.response?.data?.errors) })
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
        commit('setSelectedFile', null)
        dispatch('getData')
      } catch (error) {
        commit('setErrors', { deleteFile: maperrors(error.response?.data?.errors) })
      }
    },
    updateQueryString({}, values) {
      let searchParams = new URLSearchParams(window.location.search)
      let page = Nova.app.config.globalProperties.$inertia.page

      for (const [key, value] of Object.entries(values)) {
        if (value !== null) {
          searchParams.set(key, value || '')
        } else {
          searchParams.delete(key)
        }
      }

      if (page.url !== `${window.location.pathname}?${searchParams}`) {
        page.url = `${window.location.pathname}?${searchParams}`

        window.history.pushState(page, '', `${window.location.pathname}?${searchParams}`)
      }
    },
    saveToLocalStorage({}, values) {
      for (const [key, value] of Object.entries(values)) {
        window?.localStorage.setItem(`nova-file-manager::${key}`, value)
      }
    },
    openModal: ({ commit }, payload) => commit('OPEN', payload),
    closeModal: ({ commit }, payload) => commit('CLOSE', payload),
    closeBrowser: ({ state, dispatch, commit }) => {
      commit('setValue', state.selectedFile)
      commit('setSelectedFile', null)
      dispatch('closeModal', 'browser')
    },
  },
  getters: {
    active: (state) => (state.toolModals.length > 0 ? state.toolModals[0] : null),
    allModals: (state) => state.toolModals,
  },
}

export { store }
