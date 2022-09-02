import sanitize from '../helpers/sanitize'
import errors from '@/helpers/errors'
import Resumable from 'resumablejs'

const actions = {
    setPath({ commit, dispatch }, path) {
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

        keys.forEach(key => {
            commit(`set${key.charAt(0).toUpperCase() + key.slice(1)}`, null)
            dispatch('updateQueryString', { [key]: null })
        })
    },
    async getDisks({ commit }) {
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
            const response = await Nova.request().post('/nova-vendor/nova-file-manager/folders/create', {
                path: sanitize(`${state.path ?? ''}/${path}`),
                disk: state.disk,
            })

            Nova.success(response.data.message)

            dispatch('closeModal', 'createFolder')
            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                createFolder: errors(error.response?.data?.errors),
            })
        }
    },
    async renameFolder({ dispatch, state, commit }, { id, oldPath, newPath }) {
        try {
            const response = await Nova.request().post('/nova-vendor/nova-file-manager/folders/rename', {
                path: state.path,
                disk: state.disk,
                oldPath: sanitize(`${state.path ?? ''}/${oldPath}`),
                newPath: sanitize(`${state.path ?? ''}/${newPath}`),
            })

            Nova.success(response.data.message)

            dispatch('closeModal', `rename-folder-${id}`)
            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                renameFolder: errors(error.response?.data?.errors),
            })
        }
    },
    async deleteFolder({ dispatch, state, commit }, { id, path }) {
        try {
            const response = await Nova.request().post('/nova-vendor/nova-file-manager/folders/delete', {
                path: path,
                disk: state.disk,
            })

            Nova.success(response.data.message)

            dispatch('closeModal', `delete-folder-${id}`)
            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                renameFolder: errors(error.response?.data?.errors),
            })
        }
    },

    updateUploadQueue: ({ dispatch, state, commit }, { id, ratio = 100, status = null }) => {
        state.uploadQueue = state.uploadQueue.map(item => {
            if (item.id === id) {
                return {
                    ...item,
                    status,
                    ratio,
                }
            }

            return item
        })

        const done = state.uploadQueue.reduce((carry, item) => carry && item.ratio === 100, true)

        if (done && state.uploadQueue.length) {
            setTimeout(() => {
                dispatch('closeModal', 'upload')
                dispatch('closeModal', 'upload-queue')
                commit('clearUploadQueue')
                commit('setIsUploading', false)
                dispatch('getData')
            }, 1000)
        }
    },

    async upload({ dispatch, state, commit }, files) {
        commit('setIsUploading', true)

        const uploader = new Resumable({
            chunkSize: 50 * 1024 * 1024,
            simultaneousUploads: 1,
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

        files.forEach(file => {
            uploader.addFile(file)

            commit('addFileToUploadQueue', file)
        })

        uploader.on('fileAdded', () => uploader.upload())

        uploader.on('fileSuccess', file => {
            dispatch('updateUploadQueue', {
                id: file.fileName,
                status: true,
            })
        })

        uploader.on('fileProgress', file => {
            dispatch('updateUploadQueue', { id: file.fileName, ratio: Math.floor(file.progress() * 100) })
        })

        uploader.on('fileError', (file, message) => {
            dispatch('updateUploadQueue', {
                id: file.fileName,
                status: false,
            })

            Nova.error(message)
        })
    },

    async renameFile({ dispatch, state, commit }, { id, oldPath, newPath }) {
        try {
            const response = await Nova.request().post('/nova-vendor/nova-file-manager/files/rename', {
                path: state.path,
                disk: state.disk,
                oldPath: sanitize(oldPath),
                newPath: sanitize(`${state.path ?? ''}/${newPath}`),
            })

            Nova.success(response.data.message)

            commit('previewFile', null)
            dispatch('closeModal', `rename-file-${id}`)
            commit('setSelectedFile', null)
            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                renameFile: errors(error.response?.data?.errors),
            })
        }
    },
    async deleteFile({ dispatch, state, commit }, { id, path }) {
        try {
            const response = await Nova.request().post('/nova-vendor/nova-file-manager/files/delete', {
                path: path,
                disk: state.disk,
            })

            Nova.success(response.data.message)

            dispatch('closeModal', `delete-file-${id}`)
            commit('previewFile', null)
            commit('setToolSelection', [])
            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                deleteFile: errors(error.response?.data?.errors),
            })
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
        commit('setSelectionForField', state.currentFieldAttribute)
        commit('setSelectedFile', null)
        dispatch('closeModal', 'browser')
    },
}

export default actions
