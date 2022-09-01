import { Errors } from 'form-backend-validation'

const mutations = {
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
            state.darkModeObserver = new MutationObserver(records => {
                records.forEach(record => {
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
        keysToRetrieve.forEach(key => {
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
        const searchParams = Object.fromEntries(new URLSearchParams(window?.location.search).entries())

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
    // if we are in tool mode, we push the file to the tool selection array
        if (!state.isFieldMode) {
            if (state.toolSelection === null) {
                state.toolSelection = []
            }

            state.toolSelection.push(file)

            return
        }

        // otherwise we push to the field's selection array
        state.currentField.selection = [file, ...state.currentField.selection]
    },

    deselectFile(state, file) {
        if (!state.isFieldMode) {
            state.toolSelection = state.toolSelection.filter(_file => _file.id !== file.id)

            return
        }

        state.currentField.selection = state.currentField?.selection.filter(
            _file => _file.id !== file.id
        )
    },

    deselectFieldFile(state, { field, file }) {
        state.fields[field].selection = state.fields[field].selection.filter(
            _file => _file.id !== file.id
        )
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
    setToolSelection(state, value) {
        state.toolSelection = value
    },
    setFieldSelection(state, { attribute, value }) {
        state.fields[attribute].selection = value
    },
    openModal: (state, payload) => {
        state.toolModals.unshift(payload)
    },
    closeModal: (state, payload) => {
        state.toolModals = state.toolModals.filter(name => name !== payload)
    },

    /**
   *
   * @param state
   * @param {File} file
   */
    addFileToUploadQueue: (state, file) => {
        state.uploadQueue.push({
            id: file.name,
            isImage: file.type.includes('image') ?? false,
            ratio: 0,
            status: null,
            file,
        })
    },

    clearUploadQueue: state => {
        state.uploadQueue = []
    },

    initField: (state, { attribute, limit, selection }) => {
        state.fields[attribute] = { limit, selection }
    },
    setCurrentField: (state, field) => {
        state.currentField = field ? state.fields[field] : null
    },
    fixPortal: state => {
        if (state.toolModals.length || !!state.preview) {
            return
        }

        setTimeout(() => {
            // temporary fix
            // @see https://github.com/tailwindlabs/headlessui/issues/1319
            document.documentElement.style.removeProperty('overflow')
            document.documentElement.style.removeProperty('padding-right')
        }, 350)
    },
}

export default mutations
