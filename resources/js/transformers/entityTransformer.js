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
