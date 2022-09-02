import mutations from '@/store/mutations'
import actions from '@/store/actions'
import getters from '@/store/getters'

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
            perPageOptions: [5, 10, 15, 30, 50],

            // files, directories and other data
            files: null,
            directories: null,
            breadcrumbs: null,
            pagination: null,
            errors: null,
            selection: null,
            preview: null,
            limit: 1,
            queue: [],

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

            // field specific state
            attribute: null,
            current: null,
            fields: {},

            callback: null,
        }
    },
    mutations: {
        ...mutations,
    },
    actions: {
        ...actions,
    },
    getters: {
        ...getters,
    },
}

export { store }
