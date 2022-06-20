export default function maperrors(errors) {
  return Object.values(errors).reduce((items, carry) => [...items, ...carry])
}
