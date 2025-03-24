import axios from 'axios'
import Cookies from 'js-cookie'

const API_BASE_URL = './api/auth'

const AuthService = {
  login: async (username, password) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/login`, { username, password })
      const { token } = response.data.data
      if (token) {
        Cookies.set('jwt', token, { secure: true, sameSite: 'strict' }) // Guarda el token en una cookie
      }
      return response.data
    } catch (error) {
      console.error('Login failed:', error)
      throw error
    }
  },

  register: async (name, username, password, phone) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/register`, {
        name,
        username,
        password,
        phone,
      })
      if (response.data) {
        // Automatically log in after successful registration
        const loginResponse = await AuthService.login(username, password)
        return loginResponse
      }
      return response.data
    } catch (error) {
      console.error('Registration failed:', error)
      throw error
    }
  },

  renewToken: async () => {
    try {
      const response = await axios.post(`${API_BASE_URL}/renew`)
      const { token } = response.data
      if (token) {
        Cookies.set('jwt', token, { secure: true, sameSite: 'strict' }) // Actualiza el token en la cookie
      }
      return response.data
    } catch (error) {
      console.error('Token renewal failed:', error)
      throw error
    }
  },

  getToken: () => {
    return Cookies.get('jwt') // Obtiene el token de la cookie
  },

  logout: () => {
    Cookies.remove('jwt') // Elimina la cookie
  },

  isAuthenticated: () => {
    return !!Cookies.get('jwt') // Verifica si el token existe en la cookie
  },
}

export default AuthService
