import axios from 'axios'
import Cookies from 'js-cookie'
import router from '@/router'

const API_BASE_URL = './api/auth'

const cookieOptions = {
  // SOLO PARA DESARROLLO:
  secure: false,
  sameSite: 'lax',

  // PARA PRODUCCIÓN (cuando tengas HTTPS):
  // secure: true,
  // sameSite: 'strict',
}

// Cooldown entre llamadas a renewToken (en ms)
const RENEW_COOLDOWN_MS = 30 * 1000 // 30 segundos
let lastRenewTime = 0

const AuthService = {
  login: async (username, password) => {
    try {
      //console.log('[Auth] Iniciando login con:', username)
      const response = await axios.post(`${API_BASE_URL}/login`, { username, password })
      const { token } = response.data.data
      if (token) {
        Cookies.set('jwt', token, cookieOptions)
        //console.log('[Auth] Token guardado en cookie:', token)
      } else {
        //console.warn('[Auth] No se recibió token en la respuesta.')
      }
      return response.data
    } catch (error) {
      console.error('[Auth] Login fallido:', error)
      throw error
    }
  },

  register: async (name, username, password, phone) => {
    try {
      //console.log('[Auth] Registrando nuevo usuario:', username)
      const response = await axios.post(`${API_BASE_URL}/register`, {
        name,
        username,
        password,
        phone,
      })
      if (response.data) {
        //console.log('[Auth] Registro exitoso, iniciando sesión automáticamente.')
        const loginResponse = await AuthService.login(username, password)
        return loginResponse
      }
      return response.data
    } catch (error) {
      console.error('[Auth] Registro fallido:', error)
      throw error
    }
  },

  renewToken: async () => {
    const now = Date.now()
    if (now - lastRenewTime < RENEW_COOLDOWN_MS) {
      //console.log('[Auth] Renovación de token ignorada por cooldown.')
      return null
    }

    try {
      lastRenewTime = now
      const token = Cookies.get('jwt')
      //console.log('[Auth] Intentando renovar token. Token actual:', token)

      const response = await axios.get(`${API_BASE_URL}/renew`, {
        headers: token ? { Authorization: `Bearer ${token}` } : {},
      })
      const { token: newToken } = response.data.data
      if (newToken) {
        Cookies.set('jwt', newToken, cookieOptions)
        //console.log('[Auth] Token renovado correctamente.')
      } else {
        //console.warn('[Auth] No se recibió nuevo token en la renovación.')
      }
      return response.data
    } catch (error) {
      lastRenewTime = 0 // permite reintentar en caso de error
      if (
        error.response &&
        error.response.data &&
        (error.response.data.message === 'Token inválido' ||
          error.response.data.message === 'Token no proporcionado')
      ) {
        //console.warn('[Auth] Token inválido o no proporcionado. Cerrando sesión.')
        AuthService.logout()
        router.push('/login')
      }
      console.error('[Auth] Falló la renovación de token:', error)
      throw error
    }
  },

  getToken: () => {
    const token = Cookies.get('jwt')
    //console.log('[Auth] Obteniendo token de cookie:', token)
    return token
  },

  logout: () => {
    //console.log('[Auth] Cerrando sesión, eliminando token.')
    Cookies.remove('jwt')
  },

  isAuthenticated: () => {
    const tokenExists = !!Cookies.get('jwt')
    //console.log('[Auth] ¿Está autenticado?', tokenExists)
    return tokenExists
  },
}

export default AuthService
