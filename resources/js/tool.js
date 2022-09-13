import Tool from '@/pages/Tool'
import IndexField from '@/fields/IndexField'
import DetailField from '@/fields/DetailField'
import FormField from '@/fields/FormField'
import { createPinia } from 'pinia'

Nova.booting(app => {
    app.use(createPinia())

    Nova.inertia('NovaFileManager', Tool)

    app.component('index-nova-file-manager-field', IndexField)
    app.component('detail-nova-file-manager-field', DetailField)
    app.component('form-nova-file-manager-field', FormField)
})
