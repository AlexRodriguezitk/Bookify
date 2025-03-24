import { makeQuery } from '@/services/api'

const Permissions = {
  /**
   * Verifica si el usuario tiene los permisos solicitados.
   * @param {Array} permissions - Lista de permisos a verificar.
   * @returns {Promise<Object>} - Objeto con permisos y su estado [{ permission: "USER.IND", access: true }, { permission: "CREATE.API", access: false }]
   */
  async checkPermissions(permissions) {
    try {
      const response = await makeQuery('/permissions/check', 'POST', { permissions })
      return response.data || [] // Devuelve los permisos evaluados o un array vacío si falla
    } catch (error) {
      console.error('Error checking permissions:', error)
      return [] // En caso de error, devuelve array vacío
    }
  },

  /**
   * Comprueba si un permiso específico está concedido.
   * @param {Array} permissionsList - Lista de permisos obtenida de checkPermissions.
   * @param {string} permission - Permiso a verificar (ejemplo: "USER.IND").
   * @returns {boolean} - True si tiene permiso, false si no.
   */
  hasPermission(permissionsList, permission) {
    return permissionsList.some((p) => p.permission === permission && p.access === true)
  },
}

export default Permissions
