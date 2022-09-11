import { useStore } from '@/store'

export function usePermissions() {
  const store = useStore()

  const showCreateFolder = store.permissions.folder.create
  const showRenameFolder = store.permissions.folder.rename
  const showDeleteFolder = store.permissions.folder.delete
  const showUploadFile = store.permissions.file.upload
  const showRenameFile = store.permissions.file.rename
  const showDeleteFile = store.permissions.file.delete
  const showCropImage = store.permissions.file.edit

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
