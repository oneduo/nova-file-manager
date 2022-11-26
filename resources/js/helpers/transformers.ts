import { Entity } from '__types__'

export default function nativeFileToEntity(file: File) {
  return {
    id: file.name,
    name: file.name,
    path: file.name,
    size: file.size.toString(),
    extension: file.type.split('/')[1],
    mime: file.type,
    url: URL.createObjectURL(file),
    lastModifiedAt: new Date(file.lastModified).toString(),
    type: file.type.split('/')[0],
    exists: true,
    disk: '',
  } as Entity
}
