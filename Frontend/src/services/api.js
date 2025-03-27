import axios from 'axios'
import Cookies from 'js-cookie'
import AuthService from './auth'
import router from '@/router'

const APIURL = import.meta.env.PROD ? `${import.meta.env.BASE_URL}api` : '/api'

const checkBackendStatus = async () => {
  try {
    const response = await axios.get(APIURL + '/')
    return response.data.data.installed === true
  } catch {
    return false
  }
}

const makeQuery = async (endpoint, method = 'GET', data = null) => {
  try {
    const token = Cookies.get('jwt')
    const config = {
      method,
      url: APIURL + endpoint,
      data,
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    }
    const response = await axios(config)
    return response.data
  } catch (error) {
    if (
      error.response &&
      error.response.data &&
      (error.response.data.error === 'Token inválido' ||
        error.response.data.error === 'Token no proporcionado')
    ) {
      AuthService.logout()
      router.push('/login')
    }
    console.error('API query error:', error)
    throw error
  }
}

// Export
export { checkBackendStatus, makeQuery }
