export default function sanitize(input) {
    return input.toString().trim().replaceAll(/\/\/+/g, '/')
}
