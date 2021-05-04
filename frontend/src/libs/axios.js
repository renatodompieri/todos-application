import Vue from 'vue'

// axios
import axios from 'axios'

const axiosIns = axios.create({
  // You can add your headers here
  // ================================
  baseURL: 'http://localhost:8000',
  // timeout: 1000,
  // headers: {'X-Custom-Header': 'foobar'}
})

axiosIns.get('/sanctum/csrf-cookie')
axiosIns.defaults.baseURL += '/api'
axiosIns.defaults.withCredentials = false
Vue.prototype.$http = axiosIns

export default axiosIns
