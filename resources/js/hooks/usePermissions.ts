import useBrowserStore from '@/stores/browser';
import { computed } from 'vue';

export function usePermissions() {
  const store = useBrowserStore();

  const showCreateFolder = computed(() => store.permissions?.folder.create);
  const showCropImage = computed(() => store.permissions?.file.edit);
  const showDeleteFile = computed(() => store.permissions?.file.delete);
  const showDeleteFolder = computed(() => store.permissions?.folder.delete);
  const showDownloadFile = computed(() => store.permissions?.file.download);
  const showRenameFile = computed(() => store.permissions?.file.rename);
  const showRenameFolder = computed(() => store.permissions?.folder.rename);
  const showUnzipFile = computed(() => store.permissions?.file.unzip);
  const showUploadFile = computed(() => store.permissions?.file.upload);

  return {
    showCreateFolder,
    showCropImage,
    showDeleteFile,
    showDeleteFolder,
    showDownloadFile,
    showRenameFile,
    showRenameFolder,
    showUnzipFile,
    showUploadFile,
  };
}
