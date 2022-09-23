/* eslint-disable */
import Entity from '@/types/Entity'

/**
 * Transform a native File to an Entity
 *
 * @param {File} file
 */
const toEntity = file => {
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
