import type { Nova } from 'laravel-nova-types'

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<Record<string, unknown>, Record<string, unknown>, unknown>
  export default component
}

declare global {
  interface Window {
    Nova: Nova & {
      config: {
        NovaFileManagerEditor: any
      }
    }
    LaravelNova: any
    confetti: any
  }
}

declare module 'form-backend-validation' {
  interface Errors {
    [key: any]: any
  }
}
