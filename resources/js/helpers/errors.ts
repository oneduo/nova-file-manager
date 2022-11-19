export default function errors(errors: any) {
  return Object.values(errors).reduce((items: any, carry: any) => [...items, ...carry])
}
