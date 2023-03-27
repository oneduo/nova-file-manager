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
  multiple?: boolean
  limit?: number
  wrapper?: string
  resource?: string
  resourceId?: string | number
  attribute?: string
  singleDisk: boolean
  permissions?: PermissionsCollection
  flexibleGroup: any[]
  callback?: (...params: any[]) => any
  usePintura: boolean
  pinturaOptions?: PinturaOptions
  cropperOptions?: CropperOptions
  component?: string
}

export type Config = {
  singleDisk: boolean
  permissions: PermissionsCollection
  tour: boolean
  usePintura: boolean
  pinturaOptions: PinturaOptions
  cropperOptions: CropperOptions
  outdated?: boolean
}

export type Entity = {
  id: string
  name: string
  path: string
  disk: string
  size: string
  type: EntityType
  extension: string
  mime: string
  url: string
  lastModifiedAt: string
  exists?: boolean
  meta?: Record<string, any>
}

export type EntityType = 'image' | 'video' | 'audio' | 'document' | 'archive' | 'other'

export type Errors = {
  [key: string]: string[]
}

export type ErrorsBag = {
  message: string
  errors: Errors
}

export type Folder = {
  id: string
  disk?: string
  name: string
  path: string
  type: string
}

export type NovaField = {
  asHtml: boolean
  attribute: string
  component: string
  dependentComponentKey: string
  dependsOn?: any
  displayedAs?: any
  fullWidth: boolean
  helpText?: any
  indexName: string
  limit: number
  multiple: boolean
  name: string
  nullable: boolean
  panel: string
  permissions: PermissionsCollection
  pinturaOptions?: PinturaOptions
  placeholder?: any
  prefixComponent: boolean
  readonly: boolean
  required: boolean
  singleDisk: boolean
  sortable: boolean
  sortableUriKey: string
  stacked: boolean
  textAlign: string
  uniqueKey: string
  usePintura: boolean
  usesCustomizedDisplay: boolean
  validationKey: string
  value: Entity[]
  visible: boolean
  wrapper: string
  wrapping: boolean
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

export type CropperDragMode = 'crop' | 'move' | 'none'
export type CropperViewMode = 0 | 1 | 2 | 3

export interface CropperSetDataOptions {
  x?: number
  y?: number
  width?: number
  height?: number
  rotate?: number
  scaleX?: number
  scaleY?: number
}

export interface CropperOptions {
  aspectRatio?: number
  autoCrop?: boolean
  autoCropArea?: number
  background?: boolean
  center?: boolean
  checkCrossOrigin?: boolean
  checkOrientation?: boolean
  cropBoxMovable?: boolean
  cropBoxResizable?: boolean
  data?: CropperSetDataOptions
  dragMode?: CropperDragMode
  guides?: boolean
  highlight?: boolean
  initialAspectRatio?: number
  minCanvasHeight?: number
  minCanvasWidth?: number
  minContainerHeight?: number
  minContainerWidth?: number
  minCropBoxHeight?: number
  minCropBoxWidth?: number
  modal?: boolean
  movable?: boolean
  responsive?: boolean
  restore?: boolean
  rotatable?: boolean
  scalable?: boolean
  toggleDragModeOnDblclick?: boolean
  viewMode?: CropperViewMode
  wheelZoomRatio?: number
  zoomOnTouch?: boolean
  zoomOnWheel?: boolean
  zoomable?: boolean
}
