import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useAdminUsersStore = defineStore('adminUsers', {
  state: () => ({
    users: [],
    currentUser: null,
    loading: false,
    errors: {},
    errorMessage: null,
    successMessage: null,
    meta: {
      currentPage: 1,
      lastPage: 1,
      perPage: 15,
      total: 0,
    },
  }),

  actions: {
    clearErrors() {
      this.errors = {}
      this.errorMessage = null
      this.successMessage = null
    },

    /**
     * Récupère la liste des utilisateurs avec filtres
     */
    async fetchUsers(params = {}) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/admin/users', { params })
        const data = response.data.data

        if (Array.isArray(data)) {
          this.users = data
        } else {
          this.users = []
        }

        if (response.data.meta) {
          this.meta = {
            currentPage: response.data.meta.current_page || 1,
            lastPage: response.data.meta.last_page || 1,
            perPage: response.data.meta.per_page || 15,
            total: response.data.meta.total || this.users.length,
          }
        }

        return response.data
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message
        this.errors = errorData.errors || {}
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Récupère un utilisateur spécifique par son ID
     */
    async fetchUser(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/admin/users/${id}`)
        this.currentUser = response.data.data
        return response.data.data
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message
        this.errors = errorData.errors || {}
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Crée un nouvel utilisateur
     */
    async createUser(payload) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.post('/admin/users', payload)
        this.successMessage = response.data.message || 'Utilisateur créé avec succès !'
        return response.data
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message
        this.errors = errorData.errors || {}
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Met à jour un utilisateur
     */
    async updateUser(id, payload) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.put(`/admin/users/${id}`, payload)
        this.successMessage = response.data.message || 'Utilisateur mis à jour avec succès !'
        return response.data
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message
        this.errors = errorData.errors || {}
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Supprime un utilisateur
     */
    async deleteUser(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.delete(`/admin/users/${id}`)
        this.successMessage = response.data.message || 'Utilisateur supprimé avec succès !'
        // Retirer l'utilisateur de la liste locale
        this.users = this.users.filter((user) => user.id !== id)
        return response.data
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message
        this.errors = errorData.errors || {}
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
