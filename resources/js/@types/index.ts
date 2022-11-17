export type Config = {
  outdated: boolean
  tour: boolean
  singleDisk: boolean
  permissions: PermissionsCollection
  usePintura: boolean
  pinturaOptions: PinturaOptions
}

export type Permissions = {
  create?: boolean
  delete?: boolean
  edit?: boolean
  rename?: boolean
  unzip?: boolean
  upload?: boolean
}

export type PermissionsCollection = {
  folder: Permissions
  file: Permissions
}

export type PinturaOptions = Record<string, any>

export type Entity = {
  id: string
  name: string
  path: string
  disk: string
  size: string
  type: string
  extension: string
  mime: string
  url: string
  lastModifiedAt: string
  exists: boolean
}
