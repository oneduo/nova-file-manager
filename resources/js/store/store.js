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
            selectedFile: null,
            fieldValue: null,
            errors: null,
            selection: [],
            preview: null,
            limit: 1,

            // tool mode state
            isToolMode: true,
            toolSelection: null,

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

            uploadQueue: [],

            currentField: null,
            fields: {},
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
