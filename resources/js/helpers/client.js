import axios from 'axios'
import isNil from 'lodash/isNil'

export function client() {
  const instance = axios.create()

  instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
  instance.defaults.headers.common['X-CSRF-TOKEN'] =
    document.head.querySelector('meta[name="csrf-token"]').content

  instance.interceptors.response.use(
    response => response,
    error => {
      if (axios.isCancel(error)) {
        return Promise.reject(error)
      }

      const response = error.response

      const {
        status,
        data: { redirect },
      } = response

      // Show the user a 500 error
      if (status >= 500) {
        Nova.$emit('error', error.response.data.message)
      }

      // Handle Session Timeouts (Unauthorized)
      if (status === 401) {
        // Use redirect if being specificed by the response
        if (!isNil(redirect)) {
          location.href = redirect
          return
        }

        Nova.redirectToLogin()
      }

      // Handle Forbidden
      if (status === 403) {
        Nova.visit('/403')
      }

      // Handle Token Timeouts
      if (status === 419) {
        Nova.$emit('token-expired')
      }

      return Promise.reject(error)
    }
  )

  return instance
}
