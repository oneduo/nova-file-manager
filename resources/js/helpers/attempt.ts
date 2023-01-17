import { AxiosResponse } from 'axios'
import { ApiError, ApiResponse } from '@/@types'
import useBrowserStore from '@/stores/browser'

type Params = {
  operation: string
  modal?: string
  endpoint: string
  data: object
  callback?: (response?: AxiosResponse<ApiResponse>) => void
}

export default async function attempt({ operation, endpoint, data, modal, callback }: Params) {
  const store = useBrowserStore()

  const post = store.post
  const setError = store.setError
  const resetError = store.resetError
  const closeModal = store.closeModal

  try {
    store.loadingOperation = operation

    const response = await post<ApiResponse>({
      path: endpoint,
      data,
    })

    resetError()

    window.Nova.success(response.data.message)

    if (callback) {
      callback(response)
    }

    if (modal) {
      closeModal({ name: modal })
    }
  } catch (error: unknown) {
    store.loadingOperation = undefined

    const bag = (error as ApiError).response?.data;

    window.Nova.error(bag?.message || 'An error occurred')

    setError({
      attribute: operation,
      bag: bag,
    })
  }
}