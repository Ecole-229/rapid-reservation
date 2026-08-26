import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useAdminSallesStore = defineStore('adminSalles', {
  state: () => ({
    salles: [],
    currentSalle: null,
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
     * Récupère la liste des salles avec filtres
     */
    async fetchSalles(params = {}) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/admin/salles', { params })
        const data = response.data.data

        if (Array.isArray(data)) {
          this.salles = data
        } else {
          this.salles = []
        }

        if (response.data.meta) {
          this.meta = {
            currentPage: response.data.meta.current_page || 1,
            lastPage: response.data.meta.last_page || 1,
            perPage: response.data.meta.per_page || 15,
            total: response.data.meta.total || this.salles.length,
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
     * Récupère une salle spécifique par son ID
     */
    async fetchSalle(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/admin/salles/${id}`)
        this.currentSalle = response.data.data
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
     * Crée une nouvelle salle
     */
    async createSalle(payload) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.post('/admin/salles', payload)
        this.successMessage = response.data.message || 'Salle créée avec succès !'
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
     * Met à jour une salle existante
     */
    async updateSalle(id, payload) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.put(`/admin/salles/${id}`, payload)
        this.successMessage = response.data.message || 'Salle mise à jour avec succès !'
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
     * Supprime une salle
     */
    async deleteSalle(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.delete(`/admin/salles/${id}`)
        this.successMessage = response.data.message || 'Salle supprimée avec succès !'
        // Retirer la salle de la liste locale
        this.salles = this.salles.filter((salle) => salle.id !== id)
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
