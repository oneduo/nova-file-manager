const QUEUE_MODAL_NAME = 'queue';
const PREVIEW_MODAL_NAME = 'preview';
const UPLOAD_MODAL_NAME = 'upload';
const UPLOAD_CROP_MODAL_NAME = 'upload-crop';
const CROP_MODAL_NAME = 'crop';
const BROWSER_MODAL_NAME = 'browser';
const CREATE_FOLDER_MODAL = 'create-folder';

const SPOTLIGHT_ENTRY_TYPE_FILE = 'file';
const SPOTLIGHT_ENTRY_TYPE_FOLDER = 'folder';
const SPOTLIGHT_SHORTCUT = 'KeyK';

const SPOTLIGHT_MODIFIERS = {
  folders: '#',
  files: '>',
  help: '?',
};

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
];

const OPERATIONS = {
  CREATE_FOLDER: 'createFolder',
  RENAME_FOLDER: 'renameFolder',
  DELETE_FOLDER: 'deleteFolder',
  RENAME_FILE: 'renameFile',
  DELETE_FILE: 'deleteFile',
  UNZIP_FILE: 'unzipFile',
};

const MODALS = {
  CREATE_FOLDER: 'createFolder',
  DELETE_FILES: 'deleteFiles',
  DELETE_FOLDER: 'deleteFolder',
  RENAME_FILE: 'renameFile',
  RENAME_FOLDER: 'renameFolder',
  UNZIP_FILE: 'unzipFile',
};

const ENDPOINTS = {
  CREATE_FOLDER: '/folders/create',
  DELETE_FILE: '/files/delete',
  DELETE_FOLDER: '/folders/delete',
  DISKS: '/disks',
  DOWNLOAD_FILE: '/files/download',
  RENAME_FILE: '/files/rename',
  RENAME_FOLDER: '/folders/rename',
  UNZIP_FILE: '/files/unzip',
  UPLOAD: '/nova-vendor/nova-file-manager/files/upload',
};

export {
  BROWSER_MODAL_NAME,
  CREATE_FOLDER_MODAL,
  CROP_MODAL_NAME,
  ENDPOINTS,
  MODALS,
  OPERATIONS,
  PREVIEW_MODAL_NAME,
  QUEUE_MODAL_NAME,
  SPOTLIGHT_ENTRY_TYPE_FILE,
  SPOTLIGHT_ENTRY_TYPE_FOLDER,
  SPOTLIGHT_MODIFIERS,
  SPOTLIGHT_SHORTCUT,
  UPLOAD_CROP_MODAL_NAME,
  UPLOAD_MODAL_NAME,
  WATCHABLE_ACTIONS,
};
