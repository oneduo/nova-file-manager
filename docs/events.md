# Events

The file manager dispatches events when certain actions are performed.  
You may subscribe to these events by adding a listener to the event bus.

::: tip NOTE
You may prevent action by throwing a `ValidationException` in before hooks.
:::

## FileUploading

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |

## FileUploaded

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |

## FileRenaming

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `from`       | `string`                                      |
| `to`         | `string`                                      |

## FileRenamed

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `from`       | `string`                                      |
| `to`         | `string`                                      |

## FileDeleting

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |

## FileDeleted

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |

## FolderCreating

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |

## FolderCreated

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |

## FolderRenaming

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `from`       | `string`                                      |
| `to`         | `string`                                      |

## FolderRenamed

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `from`       | `string`                                      |
| `to`         | `string`                                      |

## FolderDeleting

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |

## FolderDeleted

| Parameter    | Type                                          |
|--------------|-----------------------------------------------|
| `filesystem` | `\Illuminate\Contracts\Filesystem\Filesystem` |
| `disk`       | `string`                                      |
| `path`       | `string`                                      |
