export default class Entity {
  constructor(
    id,
    name,
    path,
    size,
    extension,
    mime,
    url,
    lastModifiedAt,
    type,
    exists,
    disk,
    meta
  ) {
    this.id = id
    this.name = name
    this.path = path
    this.size = size
    this.extension = extension
    this.mime = mime
    this.url = url
    this.lastModifiedAt = lastModifiedAt
    this.type = type
    this.exists = exists
    this.disk = disk
    this.meta = meta
  }
}
