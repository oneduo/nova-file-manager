export default async function (dataTransferItems: DataTransferItemList) {
  const isDirectory = (entry: FileSystemEntry): entry is FileSystemDirectoryEntry => entry.isDirectory

  const isFile = (entry: FileSystemEntry): entry is FileSystemFileEntry => entry.isFile

  const readFile = (entry: FileSystemEntry, path = ''): Promise<File> => {
    return new Promise((resolve, reject) => {
      if (!isFile(entry)) {
        return
      }

      entry.file(
        file => resolve(new File([file], path + file.name, { type: file.type })),
        err => reject(err),
      )
    })
  }

  const dirReadEntries = (dirReader: FileSystemDirectoryReader, path: string): Promise<File[]> => {
    return new Promise((resolve, reject) => {
      dirReader.readEntries(
        async (entries: FileSystemEntry[]) => {
          let files: File[] = []

          for (const entry of entries) {
            const itemFiles = await getFilesFromEntry(entry, path)

            if (itemFiles !== undefined) {
              files = files.concat(itemFiles)
            }
          }

          resolve(files)
        },
        err => reject(err),
      )
    })
  }

  const readDir = async (entry: FileSystemEntry, path: string) => {
    if (!isDirectory(entry)) {
      return []
    }

    const dirReader = entry.createReader()

    const newPath = path + entry.name + '/'

    let files: File[] = []

    let newFiles: File[] = []

    do {
      newFiles = await dirReadEntries(dirReader, newPath)

      files = files.concat(newFiles)
    } while (newFiles.length > 0)

    return files
  }

  const getFilesFromEntry = async (entry: FileSystemEntry | null, path = '') => {
    if (!entry) {
      throw new Error('Entry not isFile and not isDirectory - unable to get files')
    }

    if (entry.isFile) {
      const file = await readFile(entry, path)

      return [file]
    }

    if (entry.isDirectory) {
      return await readDir(entry, path)
    }
  }

  let files: File[] = []
  const entries: (FileSystemEntry | null)[] = []

  const total = dataTransferItems.length

  for (let i = 0; i < total; i++) {
    entries.push(dataTransferItems[i].webkitGetAsEntry())
  }

  for (const entry of entries) {
    const newFiles = await getFilesFromEntry(entry)

    if (newFiles !== undefined) {
      files = files.concat(newFiles)
    }
  }

  return files
}
