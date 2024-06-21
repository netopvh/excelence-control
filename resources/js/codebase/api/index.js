import axios from 'axios'

const BASE_URL = import.meta.env.APP_URL || ''

const axiosInstance = axios.create({
  baseURL: BASE_URL,
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
  (error) => Promise.reject(error)
)

axiosInstance.interceptors.response.use(
  (response) => response.data,
  (error) => {
    return Promise.reject(error.response)
  }
)

const get = async (url, params = {}, headers = {}) => axiosInstance.get(url, { params, headers })

const post = async (url, data, headers = {}) => axiosInstance.post(url, data, { headers })

const put = async (url, data, headers = {}) => axiosInstance.put(url, data, { headers })

const del = async (url, headers = {}) => axiosInstance.delete(url, { headers })

const patch = async (url, data, headers = {}) => axiosInstance.patch(url, data, { headers })

export { get, post, put, del as delete, patch, BASE_URL }
