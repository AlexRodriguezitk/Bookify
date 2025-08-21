import axios from 'axios'
import Cookies from 'js-cookie'
import router from '@/router'
import { useUserStore } from '@/stores/user'

const API_BASE_URL = './api/auth'

const cookieOptions = {
  // SOLO PARA DESARROLLO:
  secure: false,
  sameSite: 'lax', // PARA PRODUCCIÓN (cuando tengas HTTPS):
  // secure: true,
  // sameSite: 'strict',
}

// Cooldown entre llamadas a renewToken (en ms)
const RENEW_COOLDOWN_MS = 30 * 1000 // 30 segundos
let lastRenewTime = 0

const AuthService = {
  // ✅ CAMBIADO: Función de login ahora solo para el primer paso (contraseña)
  login: async (username, password) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/login/password`, { username, password })
      // Si la respuesta indica 2FA requerido, el frontend maneja el siguiente paso.
      if (response.data.data.two_fa_required) {
        return response.data
      } // Si no se requiere 2FA, se procede como antes

      const { token, user } = response.data.data
      if (token) {
        Cookies.set('jwt', token, cookieOptions)
        const userStore = useUserStore()
        userStore.setUser({
          name: user.name,
          username: user.username,
          rol: user.rol.name,
          profile_image: user.profile_image,
        })
      }
      return response.data
    } catch (error) {
      console.error('[Auth] Login fallido:', error)
      throw error
    }
  }, // ✅ NUEVO: Función para el segundo paso del login (verificación del código 2FA)

  verify2fa: async (username, otp_code) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/login/verify-2fa`, { username, otp_code })
      const { token, user } = response.data.data
      if (token) {
        Cookies.set('jwt', token, cookieOptions)
        const userStore = useUserStore()
        userStore.setUser({
          name: user.name,
          username: user.username,
          rol: user.rol.name,
          profile_image: user.profile_image,
        })
      }
      return response.data
    } catch (error) {
      console.error('[Auth] Verificación 2FA fallida:', error)
      throw error
    }
  }, // ✅ CAMBIADO: Función de registro ya no hace login automático

  register: async (name, username, password, phone) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/register`, {
        name,
        username,
        password,
        phone,
      }) // El backend ahora devuelve el 'totp_uri' y el token para la configuración inicial
      return response.data
    } catch (error) {
      console.error('[Auth] Registro fallido:', error)
      throw error
    }
  },

  renewToken: async () => {
    const now = Date.now()
    if (now - lastRenewTime < RENEW_COOLDOWN_MS) {
      return null
    }

    try {
      lastRenewTime = now
      const token = Cookies.get('jwt')
      const response = await axios.get(`${API_BASE_URL}/renew`, {
        headers: token ? { Authorization: `Bearer ${token}` } : {},
      })
      const { token: newToken, user } = response.data.data
      if (newToken) {
        Cookies.set('jwt', newToken, cookieOptions)
      }
      if (user) {
        const userStore = useUserStore()
        userStore.setUser({
          name: user.name,
          username: user.username,
          rol: user.rol.name,
          profile_image: user.profile_image,
        })
      }
      return response.data
    } catch (error) {
      lastRenewTime = 0
      if (
        error.response &&
        error.response.data &&
        (error.response.data.message === 'Token inválido' ||
          error.response.data.message === 'Token no proporcionado')
      ) {
        AuthService.logout()
        router.push('/login')
      }
      console.error('[Auth] Falló la renovación de token:', error)
      throw error
    }
  },

  getToken: () => {
    return Cookies.get('jwt')
  },

  logout: () => {
    Cookies.remove('jwt')
    const userStore = useUserStore()
    userStore.clearUser()
  },

  isAuthenticated: () => {
    const tokenExists = !!Cookies.get('jwt')
    return tokenExists
  },
}

export default AuthService
