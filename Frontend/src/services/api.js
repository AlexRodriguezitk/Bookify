import axios from 'axios'
import Cookies from 'js-cookie'
import AuthService from './auth'
import router from '@/router'

const APIURL = import.meta.env.PROD ? `${import.meta.env.BASE_URL}api` : '/api'

// 🧠 Registro de controladores por endpoint
const activeRequests = {}

const checkBackendStatus = async () => {
  try {
    const response = await axios.get(APIURL + '/')
    return response.data.data.installed === true
  } catch {
    return false
  }
}

const makeQuery = async (endpoint, method = 'GET', data = null) => {
  // 🛑 Cancelar petición anterior si existe
  if (activeRequests[endpoint] && endpoint != '/permissions/check') {
    activeRequests[endpoint].abort()
  }

  const controller = new AbortController()
  activeRequests[endpoint] = controller

  try {
    const token = Cookies.get('jwt')
    const config = {
      method,
      url: APIURL + endpoint,
      data,
      signal: controller.signal,
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    }

    const response = await axios(config)

    // ✅ Limpiar registro si se completa
    delete activeRequests[endpoint]

    return response.data
  } catch (error) {
    // 🧹 Limpiar también en caso de error
    delete activeRequests[endpoint]

    if (
      error.response &&
      error.response.data &&
      (error.response.data.error === 'Token inválido' ||
        error.response.data.error === 'Token no proporcionado')
    ) {
      AuthService.logout()
      router.push('/login')
    }

    // Ignora errores por aborto
    if (axios.isCancel(error) || error.name === 'CanceledError' || error.name === 'AbortError') {
      console.warn(`Petición a ${endpoint} abortada`)
      return
    }

    console.error('API query error:', error)
    throw error
  }
}

export { checkBackendStatus, makeQuery }
