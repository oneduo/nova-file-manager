import copyToClipboard from 'copy-to-clipboard';

export function useClipboard() {
  const copy = (value: string) => {
    copyToClipboard(value);
  };

  return { copy };
}
