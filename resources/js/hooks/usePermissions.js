import { useStore } from '../store'
import { computed } from 'vue'

export function usePermissions() {
  const store = useStore()

  const showCreateFolder = computed(() => store.permissions.folder.create)
  const showRenameFolder = computed(() => store.permissions.folder.rename)
  const showDeleteFolder = computed(() => store.permissions.folder.delete)
  const showUploadFile = computed(() => store.permissions.file.upload)
  const showRenameFile = computed(() => store.permissions.file.rename)
  const showDeleteFile = computed(() => store.permissions.file.delete)
  const showCropImage = computed(() => store.permissions.file.edit)
  const showUnzipFile = computed(() => store.permissions.file.unzip)

  return {
    showCreateFolder,
    showRenameFolder,
    showDeleteFolder,
    showUploadFile,
    showRenameFile,
    showDeleteFile,
    showCropImage,
    showUnzipFile,
  }
}
