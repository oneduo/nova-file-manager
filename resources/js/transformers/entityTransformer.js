import Entity from '@/types/Entity'

export default function entityTransformer(file) {
  return new Entity(
    file.name,
    file.name,
    file.name,
    null,
    null,
    null,
    URL.createObjectURL(file),
    null,
    file.type.split('/')[0]
  )
}

export function entity(file) {
  return new Entity(
    file.id,
    file.name,
    file.path,
    file.size,
    file.extension,
    file.mime,
    file.url,
    file.lastModifiedAt,
    file.type,
    file.exists
  )
}
