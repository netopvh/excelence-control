import axios from 'axios';

const BASE_URL = import.meta.env.APP_URL || '';

const axiosInstance = axios.create({
  baseURL: BASE_URL,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
  },
});

axiosInstance.interceptors.request.use(
  (config) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

axiosInstance.interceptors.response.use(
  (response) => response.data,
  (error) => {
    if (error.response) {
      console.error(`API error: ${error.response.status} - ${error.response.data}`);
    } else if (error.request) {
      console.error('API error: No response received');
    } else {
      console.error(`API error: ${error.message}`);
    }
    return Promise.reject(error);
  }
);

const get = (url, params = {}, headers = {}) => axiosInstance.get(url, { params, headers });

const post = (url, data, headers = {}) => axiosInstance.post(url, data, { headers });

const put = (url, data, headers = {}) => axiosInstance.put(url, data, { headers });

const del = (url, headers = {}) => axiosInstance.delete(url, { headers });

const patch = (url, data, headers = {}) => axiosInstance.patch(url, data, { headers });

export { get, post, put, del as delete, patch, BASE_URL };
