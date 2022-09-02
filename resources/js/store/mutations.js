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

    /*
    |--------------------------------------------------------------------------
    | File selection handlers
    |--------------------------------------------------------------------------
    |
    | These mutations handle the selection of the tool
    |
    | mutations:
    |   - selectFile
    |   - deselectFile
    |   - setSelection
    |   - clearSelection
    |   - toggleSelection
    */

    /**
   * Select a file
   *
   * @param state
   * @param {Entity} file
   */
    selectFile(state, file) {
        if (state.selection === null) {
            state.selection = []
        }

        state.selection.push(file)
    },

    /**
   * Deselect a file
   *
   * @param state
   * @param {Entity} file
   */
    deselectFile(state, file) {
        state.selection = state.selection.filter(item => item.id !== file.id)
    },

    /**
   * Set selection
   *
   * @param state
   * @param {Entity[]|null} files
   */
    setSelection(state, files) {
        state.selection = files
    },

    /**
   * Clear selection
   *
   * @param state
   */
    clearSelection(state) {
        state.selection = []
    },

    /**
   * Toggle selection status for a file
   *
   * @param state
   * @param {Entity} file
   */
    toggleSelection(state, file) {
        const exists = !!state.selection?.find(item => item.id === file.id)

        if (exists) {
            this.commit('nova-file-manager/deselectFile', file)

            return
        }
        console.log(state.multiple)
        if (!state.multiple) {
            this.commit('nova-file-manager/setSelection', [])
        }

        this.commit('nova-file-manager/selectFile', file)
    },

    /*
    |--------------------------------------------------------------------------
    | Field mutations
    |--------------------------------------------------------------------------
    */

    /**
   * Add a new field entry to the state
   *
   * @param state
   * @param {string} attribute
   * @param {number|null} limit
   * @param {Entity[]|null}selection
   */
    initField: (state, { attribute, limit, selection }) => {
        state.fields[attribute] = { limit, selection }
    },

    /**
   * Set the current field
   *
   * @param state
   * @param {string|null}attribute
   */
    setCurrent: (state, attribute) => {
        state.attribute = attribute
        state.current = attribute !== null ? state.fields[attribute] ?? null : null
    },

    /**
   * Set the field's value
   *
   * @param state
   * @param {string|null} attribute
   * @param {Entity[]|null} files
   */
    setFieldSelection(state, { attribute, files }) {
        if (attribute === null) {
            return
        }

        state.fields[attribute].selection = files
    },

    deselectFieldFile(state, { field, file }) {
        state.fields[field].selection = state.fields[field].selection.filter(
            _file => _file.id !== file.id
        )
    },

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */
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
    setSearch(state, value) {
        state.search = value?.length ? value : null
    },
    setMultiple(state, multiple) {
        state.multiple = multiple
    },
    setLimit(state, limit) {
        state.limit = limit
    },
    setCallback(state, callback) {
        state.callback = callback
    },

    openModal: (state, payload) => {
        state.toolModals.unshift(payload)
    },
    closeModal: (state, payload) => {
        state.toolModals = state.toolModals.filter(name => name !== payload)
    },

    /**
   * Add a file to upload queue
   * @param state
   * @param {File} file
   */
    addToQueue: (state, file) => {
        state.queue.push({
            id: file.name,
            isImage: file.type.includes('image') ?? false,
            ratio: 0,
            status: null,
            file,
        })
    },

    /**
   * Clear the upload queue
   *
   * @param state
   */
    clearQueue: state => {
        state.queue = []
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
