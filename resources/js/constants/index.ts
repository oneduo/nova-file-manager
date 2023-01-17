const QUEUE_MODAL_NAME = 'queue'
const PREVIEW_MODAL_NAME = 'preview'
const UPLOAD_MODAL_NAME = 'upload'
const UPLOAD_CROP_MODAL_NAME = 'upload-crop'
const CROP_MODAL_NAME = 'crop'
const BROWSER_MODAL_NAME = 'browser'
const CREATE_FOLDER_MODAL = 'create-folder'

const SPOTLIGHT_SHORTCUT = 'KeyK'

const SPOTLIGHT_MODIFIERS = {
  folders: '#',
  files: '>',
  help: '?',
}

const WATCHABLE_ACTIONS = [
  'setDisk',
  'setPath',
  'setPerPage',
  'setPage',
  'setSearch',
  'upload',
  'renameFile',
  'deleteFiles',
  'unzipFile',
  'createFolder',
  'renameFolder',
  'deleteFolder',
]

const OPERATIONS = {
  CREATE_FOLDER: 'createFolder',
  RENAME_FOLDER: 'renameFolder',
  DELETE_FOLDER: 'deleteFolder',
  RENAME_FILE: 'renameFile',
  DELETE_FILE: 'deleteFile',
  UNZIP_FILE: 'unzipFile',
}

const MODALS = {
  CREATE_FOLDER: 'createFolder',
  RENAME_FOLDER: 'renameFolder',
  DELETE_FOLDER: 'deleteFolder',
  RENAME_FILE: 'renameFile',
  DELETE_FILES: 'deleteFiles',
  UNZIP_FILE: 'unzipFile',
}

const ENDPOINTS = {
  CREATE_FOLDER: '/folders/create',
  RENAME_FOLDER: '/folders/rename',
  DELETE_FOLDER: '/folders/delete',
  RENAME_FILE: '/files/rename',
  DELETE_FILE: '/files/delete',
  UNZIP_FILE: '/files/unzip',
  UPLOAD: '/nova-vendor/nova-file-manager/files/upload',
  DISKS: '/disks',
}

export {
  OPERATIONS,
  MODALS,
  ENDPOINTS,
  CREATE_FOLDER_MODAL,
  QUEUE_MODAL_NAME,
  PREVIEW_MODAL_NAME,
  UPLOAD_MODAL_NAME,
  UPLOAD_CROP_MODAL_NAME,
  CROP_MODAL_NAME,
  BROWSER_MODAL_NAME,
  SPOTLIGHT_SHORTCUT,
  SPOTLIGHT_MODIFIERS,
  WATCHABLE_ACTIONS,
}
