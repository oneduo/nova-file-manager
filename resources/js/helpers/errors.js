export default function errors(errors) {
  return Object.values(errors).reduce((items, carry) => [...items, ...carry])
}
