export function csrf() {
  return (document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? false;
}
