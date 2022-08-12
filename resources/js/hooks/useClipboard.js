export function useClipboard() {
  const copyToClipboard = (value) => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(value)
    } else if (window.clipboardData) {
      window.clipboardData.setData('Text', value)
    } else {
      let input = document.createElement('input')
      let [scrollTop, scrollLeft] = [
        document.documentElement.scrollTop,
        document.documentElement.scrollLeft,
      ]
      document.body.appendChild(input)
      input.value = value
      input.focus()
      input.select()
      document.documentElement.scrollTop = scrollTop
      document.documentElement.scrollLeft = scrollLeft
      document.execCommand('copy')
      input.remove()
    }
  }

  return { copyToClipboard }
}
