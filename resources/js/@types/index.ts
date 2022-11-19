import { AxiosError } from 'axios'

export type ApiError = AxiosError<ErrorsBag>

export type ApiResponse = {
  message: string
}

export type Breadcrumb = {
  id: string
  path: string
  name: string
  current: boolean
}

export type BrowserConfig = {
  initialFiles: Entity[]
  multiple: boolean
  limit: number | null
  resource: string | null
  resourceId: string | number
  attribute: string
  singleDisk: boolean
  permissions: PermissionsCollection
  flexibleGroup: any[]
  callback?: (...params: any[]) => any
  usePintura?: boolean
  pinturaOptions?: PinturaOptions
}

export type Config = {
  outdated: boolean
  tour: boolean
  singleDisk: boolean
  permissions: PermissionsCollection
  usePintura: boolean
  pinturaOptions: PinturaOptions
}

export interface Folder {
  id: string
  disk?: string
  name: string
  path: string
}

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
  meta: Record<string, any>
}

export type Errors = {
  [key: string]: string[]
}

export type ErrorsBag = {
  message: string
  errors: Errors
}

export type Pagination = {
  current_page: number
  last_page: number
  from: number
  to: number
  total: number
  links: object
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

export type QueueEntryStatus = boolean | null

export type QueueEntry = {
  id: string
  file: File
  isImage?: boolean
  isVideo?: boolean
  isArchive?: boolean
  ratio: number
  status?: QueueEntryStatus
}

export type View = 'grid' | 'list'

export type ToolProps = {
  singleDisk: boolean
  permissions: PermissionsCollection
  tour: boolean
  usePintura: boolean
  pinturaOptions: PinturaOptions
  outdated?: boolean
}

export type Maybe<T> = T | null | undefined
