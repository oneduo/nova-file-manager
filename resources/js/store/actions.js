import sanitize from '../helpers/sanitize'
import errors from '@/helpers/errors'
import Resumable from 'resumablejs'

const buildPayload = (state, params) => {
    return {
        ...params,
        attribute: state.attribute,
        resource: state.resource,
        resourceId: state.resourceId,
        fieldMode: state.isFieldMode ? 1 : 0,
        ...(!state.customDisk && {
            disk: state.disk,
        }),
    }
}

const actions = {
    /**
   * Set the current path
   *
   * @param commit
   * @param dispatch
   * @param {string|null} path
   */
    setPath({ commit, dispatch }, path) {
        dispatch('reset')

        commit('setPath', path)

        dispatch('getData')

        dispatch('updateQueryString', { path })
    },

    /**
   * Set the current disk
   *
   * @param commit
   * @param dispatch
   * @param {string|null} disk
   */
    setDisk({ commit, dispatch }, disk) {
        dispatch('reset')

        commit('setDisk', disk)

        dispatch('getData')

        dispatch('updateQueryString', { disk })

        dispatch('saveToLocalStorage', { disk })
    },

    /**
   * Set the pagination's per page value
   *
   * @param commit
   * @param dispatch
   * @param {number|null} perPage
   */
    setPerPage({ commit, dispatch }, perPage) {
        commit('setPerPage', perPage)

        dispatch('getData')

        dispatch('updateQueryString', { perPage })

        dispatch('saveToLocalStorage', { perPage })
    },

    /**
   * Set the pagination's page
   *
   * @param commit
   * @param dispatch
   * @param {number|null} page
   */
    setPage({ commit, dispatch }, page) {
        commit('setPage', page)

        dispatch('getData')

        dispatch('updateQueryString', { page })
    },

    /**
   * Set the tool view mode
   *
   * @param commit
   * @param dispatch
   * @param {string|null} view
   */
    setView({ commit, dispatch }, view) {
        commit('setView', view)
        dispatch('saveToLocalStorage', { view })
    },

    /**
   * Set the search query
   *
   * @param commit
   * @param dispatch
   * @param {search|null} search
   */
    setSearch({ commit, dispatch }, search) {
        commit('setSearch', search)

        dispatch('getData')

        dispatch('updateQueryString', { search })
    },

    /**
   * Reset the tool
   *
   * @param commit
   * @param dispatch
   */
    reset({ commit, dispatch }) {
        const keys = ['page', 'search', 'path']

        keys.forEach(key => {
            commit(`set${key.charAt(0).toUpperCase() + key.slice(1)}`, null)

            dispatch('updateQueryString', { [key]: null })
        })
    },

    /**
   * Get the disks from the API
   *
   * @param commit
   * @returns {Promise<void>}
   */
    async getDisks({ commit, state }) {
        commit('setIsFetchingDisks', true)

        const { data } = await Nova.request().get('/nova-vendor/nova-file-manager/disks/available', {
            params: buildPayload(state, {}),
        })

        commit('setDisks', data)

        commit('setIsFetchingDisks', false)
    },

    /**
   * Get the files and directories from the API
   *
   * @param state
   * @param commit
   * @returns {Promise<void>}
   */
    async getData({ state, commit }) {
        commit('setIsFetchingData', true)

        const { data } = await Nova.request().get('/nova-vendor/nova-file-manager', {
            params: buildPayload(state, {
                path: state.path,
                page: state.page,
                perPage: state.perPage,
                search: state.search,
            }),
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
                buildPayload(state, {
                    path: sanitize(`${state.path ?? ''}/${path}`),
                })
            )

            Nova.success(response.data.message)

            dispatch('closeModal', 'create-folder')
            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                createFolder: errors(error.response?.data?.errors),
            })
        }
    },
    async renameFolder({ dispatch, state, commit }, { id, oldPath, newPath }) {
        try {
            const response = await Nova.request().post(
                '/nova-vendor/nova-file-manager/folders/rename',
                buildPayload(state, {
                    path: state.path,
                    oldPath: sanitize(`${state.path ?? ''}/${oldPath}`),
                    newPath: sanitize(`${state.path ?? ''}/${newPath}`),
                })
            )

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
            const response = await Nova.request().post(
                '/nova-vendor/nova-file-manager/folders/delete',
                buildPayload(state, {
                    path: path,
                })
            )

            Nova.success(response.data.message)

            dispatch('closeModal', `delete-folder-${id}`)
            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                deleteFolder: errors(error.response?.data?.errors),
            })
        }
    },

    updateQueue: ({ dispatch, state, commit }, { id, ratio = 100, status = null }) => {
        state.queue = state.queue.map(item => {
            if (item.id === id) {
                return {
                    ...item,
                    status,
                    ratio,
                }
            }

            return item
        })

        const done = state.queue.reduce((carry, item) => carry && item.ratio === 100, true)

        if (done && state.queue.length) {
            setTimeout(() => {
                dispatch('closeModal', 'upload')

                dispatch('closeModal', 'upload-queue')

                commit('clearQueue')

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
            query: buildPayload(state, {
                path: state.path ?? '/',
            }),
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': state.csrfToken,
            },
            permanentErrors: [400, 404, 409, 415, 422, 500, 501],
        })

        files.forEach(file => {
            uploader.addFile(file)

            commit('addToQueue', file)
        })

        uploader.on('fileAdded', () => uploader.upload())

        uploader.on('fileSuccess', file => {
            dispatch('updateQueue', {
                id: file.fileName,
                status: true,
            })
        })

        uploader.on('fileProgress', file => {
            dispatch('updateQueue', { id: file.fileName, ratio: Math.floor(file.progress() * 100) })
        })

        uploader.on('fileError', (file, message) => {
            dispatch('updateQueue', {
                id: file.fileName,
                status: false,
            })

            Nova.error(JSON.parse(message).message)
        })
    },

    async renameFile({ dispatch, state, commit }, { id, oldPath, newPath }) {
        try {
            const response = await Nova.request().post(
                '/nova-vendor/nova-file-manager/files/rename',
                buildPayload(state, {
                    path: state.path,
                    oldPath: sanitize(oldPath),
                    newPath: sanitize(`${state.path ?? ''}/${newPath}`),
                })
            )

            Nova.success(response.data.message)

            commit('previewFile', null)

            dispatch('closeModal', `rename-file-${id}`)

            dispatch('getData')
        } catch (error) {
            commit('setErrors', {
                renameFile: errors(error.response?.data?.errors),
            })
        }
    },
    async deleteFile({ dispatch, state, commit }, { id, path }) {
        try {
            const response = await Nova.request().post(
                '/nova-vendor/nova-file-manager/files/delete',
                buildPayload(state, {
                    path: path,
                })
            )

            Nova.success(response.data.message)

            dispatch('closeModal', `delete-file-${id}`)

            commit('previewFile', null)

            commit('setSelection', [])

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

    openBrowser: (
        { commit, dispatch },
        {
            initialFiles,
            multiple,
            limit,
            resource,
            resourceId,
            attribute,
            customDisk,
            permissions,
            callback,
        }
    ) => {
        commit('setIsFieldMode', true)
        commit('setMultiple', multiple)
        commit('setLimit', limit)
        commit('setResource', resource)
        commit('setResourceId', resourceId)
        commit('setAttribute', attribute)
        commit('setCustomDisk', customDisk)
        commit('setCallback', callback)
        commit('setSelection', [...initialFiles])
        dispatch('setPermissions', permissions)

        dispatch('openModal', 'browser')
    },

    closeBrowser: ({ dispatch, commit }) => {
        commit('setCallback', null)

        commit('setPage', null)
        commit('setPath', null)

        commit('setIsFieldMode', false)

        commit('setMultiple', false)
        commit('setLimit', null)
        commit('setResource', null)
        commit('setResourceId', null)
        commit('setAttribute', null)
        commit('setCustomDisk', false)

        commit('setSelection', null)

        dispatch('closeModal', 'browser')
    },

    submitFieldSelection: ({ state, dispatch }) => {
        state.callback(state.selection)

        dispatch('closeBrowser')
    },

    setPermissions: (
        { commit },
        {
            showCreateFolder,
            showRenameFolder,
            showDeleteFolder,
            showUploadFile,
            showRenameFile,
            showDeleteFile,
        }
    ) => {
        commit('setShowCreateFolder', showCreateFolder)
        commit('setShowRenameFolder', showRenameFolder)
        commit('setShowDeleteFolder', showDeleteFolder)
        commit('setshowUploadFile', showUploadFile)
        commit('setshowRenameFile', showRenameFile)
        commit('setshowDeleteFile', showDeleteFile)
    },
}

export default actions
