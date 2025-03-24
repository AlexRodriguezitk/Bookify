import axios from 'axios'
import Cookies from 'js-cookie'

const APIURL = import.meta.env.PROD ? `${import.meta.env.BASE_URL}/api` : '/api'

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
    console.error('API query error:', error)
    throw error
  }
}

// Export
export { checkBackendStatus, makeQuery }
