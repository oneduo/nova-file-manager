import escape from 'lodash/escape'

interface Options {
  escape?: boolean
}

export default function sanitize(value: string | null | undefined, options?: Options) {
  if (value === null || value === undefined) {
    return value
  }

  let output = options?.escape ? escape(value) : value

  output = output
    .trim()
    .replace(/&nbsp;/g, ' ')
    .replace(/\/{2,}/g, '/')

  return output
}