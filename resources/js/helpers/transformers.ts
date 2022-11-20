import { Entity } from '__types__'

export default function nativeFileToEntity(file: File) {
  return {
    id: file.name,
    name: file.name,
    path: file.name,
    size: file.size,
    extension: file.type.split('/')[1],
    mime: file.type,
    url: URL.createObjectURL(file),
    lastModifiedAt: file.lastModified,
    type: file.type.split('/')[0],
  } as Entity
}
