import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useAdminEquipementsStore = defineStore('adminEquipements', {
  state: () => ({
    equipements: [],
    currentEquipement: null,
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
     * Récupère la liste des équipements avec filtres
     */
    async fetchEquipements(params = {}) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/admin/equipements', { params })
        const data = response.data.data

        if (Array.isArray(data)) {
          this.equipements = data
        } else {
          this.equipements = []
        }

        if (response.data.meta) {
          this.meta = {
            currentPage: response.data.meta.current_page || 1,
            lastPage: response.data.meta.last_page || 1,
            perPage: response.data.meta.per_page || 15,
            total: response.data.meta.total || this.equipements.length,
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
     * Récupère un équipement spécifique par son ID
     */
    async fetchEquipement(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/admin/equipements/${id}`)
        this.currentEquipement = response.data.data
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
     * Crée un nouvel équipement (supporte FormData pour l'upload d'image)
     */
    async createEquipement(payload) {
      this.loading = true
      this.clearErrors()

      try {
        const isFormData = payload instanceof FormData
        const headers = isFormData ? { 'Content-Type': 'multipart/form-data' } : {}

        const response = await axiosClient.post('/admin/equipements', payload, { headers })
        this.successMessage = response.data.message || 'Équipement créé avec succès !'
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
     * Met à jour un équipement existant
     */
    async updateEquipement(id, payload) {
      this.loading = true
      this.clearErrors()

      try {
        let response
        if (payload instanceof FormData) {
          // Pour Laravel FormData en update, on peut utiliser POST avec _method = PUT ou direct POST si géré
          payload.append('_method', 'PUT')
          response = await axiosClient.post(`/admin/equipements/${id}`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' },
          })
        } else {
          response = await axiosClient.put(`/admin/equipements/${id}`, payload)
        }

        this.successMessage = response.data.message || 'Équipement mis à jour avec succès !'
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
     * Supprime un équipement
     */
    async deleteEquipement(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.delete(`/admin/equipements/${id}`)
        this.successMessage = response.data.message || 'Équipement supprimé avec succès !'
        this.equipements = this.equipements.filter((e) => e.id !== id)
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
