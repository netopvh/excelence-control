import axios from 'axios'

const metaBaseUrl = document.querySelector('meta[name="base-url"]') as HTMLMetaElement | null
const baseURL: string = metaBaseUrl ? metaBaseUrl.content : ''

const axiosInstance = axios.create({
  baseURL,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json'
  }
})

axiosInstance.interceptors.request.use(
  (config) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token
    }
    return config
  },
  async (error) => await Promise.reject(error)
)

axiosInstance.interceptors.response.use(
  (response) => response.data,
  async (error) => {
    if (error.response) {
      console.error(`API error: ${error.response.status} - ${error.response.data}`)
    } else if (error.request) {
      console.error('API error: No response received')
    } else {
      console.error(`API error: ${error.message}`)
    }
    return await Promise.reject(error)
  }
)

const get = async (url: string, params: object = {}, headers: object = {}): Promise<any> => axiosInstance.get(url, { params, headers })

const post = async (url: string, data: object, headers: object = {}): Promise<any> => axiosInstance.post(url, data, { headers })

const put = async (url: string, data: object, headers: object = {}): Promise<any> => axiosInstance.put(url, data, { headers })

const del = async (url: string, headers: object = {}): Promise<any> => axiosInstance.delete(url, { headers })

const patch = async (url: string, data: object, headers: object = {}): Promise<any> => axiosInstance.patch(url, data, { headers })

export { get, post, put, del as delete, patch }
