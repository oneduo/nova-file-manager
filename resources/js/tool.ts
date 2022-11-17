import Tool from './pages/Tool.vue'
import IndexField from './fields/IndexField.vue'
import DetailField from './fields/DetailField.vue'
import FormField from './fields/FormField.vue'
import { createPinia } from 'pinia'
import '../css/tool.css'

window.Nova.booting((app: any) => {
  app.use(createPinia())

  window.Nova.inertia('NovaFileManager', Tool)

  app.component('index-nova-file-manager-field', IndexField)
  app.component('detail-nova-file-manager-field', DetailField)
  app.component('form-nova-file-manager-field', FormField)
})
