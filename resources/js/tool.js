import Tool from '@/pages/Tool'
import IndexField from '@/fields/IndexField'
import DetailField from '@/fields/DetailField'
import FormField from '@/fields/FormField'
import { store as toolStore } from '@/store/store'

Nova.booting((app, store) => {
    store.registerModule('nova-file-manager', toolStore)

    Nova.inertia('NovaFileManager', Tool)

    app.component('index-nova-file-manager-field', IndexField)
    app.component('detail-nova-file-manager-field', DetailField)
    app.component('form-nova-file-manager-field', FormField)
})
