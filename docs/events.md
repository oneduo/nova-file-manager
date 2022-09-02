# Events

The file manager dispatches events when certain actions are performed.  
You may subscribe to these events by adding a listener to the event bus.

## FileUploaded

| Parameter | Type     |
|-----------|----------|
| `disk`    | `string` |
| `path`    | `string` |

## FileRenamed

| Parameter | Type     |
|-----------|----------|
| `disk`    | `string` |
| `oldPath` | `string` |
| `newPath` | `string` |

## FileDeleted

| Parameter | Type     |
|-----------|----------|
| `disk`    | `string` |
| `path`    | `string` |

## FolderCreated

| Parameter | Type     |
|-----------|----------|
| `disk`    | `string` |
| `path`    | `string` |

## FolderRenamed

| Parameter | Type     |
|-----------|----------|
| `disk`    | `string` |
| `oldPath` | `string` |
| `newPath` | `string` |

## FolderDeleted

| Parameter | Type     |
|-----------|----------|
| `disk`    | `string` |
| `path`    | `string` |
