import { useStore } from 'vuex'
import { computed } from 'vue'

export function usePermissions() {
    const store = useStore()

    const showCreateFolder = computed(() => store.state['nova-file-manager'].showCreateFolder)
    const showRenameFolder = computed(() => store.state['nova-file-manager'].showRenameFolder)
    const showDeleteFolder = computed(() => store.state['nova-file-manager'].showDeleteFolder)
    const showUploadFile = computed(() => store.state['nova-file-manager'].showUploadFile)
    const showRenameFile = computed(() => store.state['nova-file-manager'].showRenameFile)
    const showDeleteFile = computed(() => store.state['nova-file-manager'].showDeleteFile)
    const showCropImage = computed(() => store.state['nova-file-manager'].showCropImage)

    return {
        showCreateFolder,
        showRenameFolder,
        showDeleteFolder,
        showUploadFile,
        showRenameFile,
        showDeleteFile,
      showCropImage,
    }
}
