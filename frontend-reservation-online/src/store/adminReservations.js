import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useAdminReservationsStore = defineStore('adminReservations', {
  state: () => ({
    reservations: [],
    currentReservation: null,
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
     * Récupère la liste des réservations avec filtres
     */
    async fetchReservations(params = {}) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/admin/reservations', { params })
        const data = response.data.data

        if (Array.isArray(data)) {
          this.reservations = data
        } else {
          this.reservations = []
        }

        if (response.data.meta) {
          this.meta = {
            currentPage: response.data.meta.current_page || 1,
            lastPage: response.data.meta.last_page || 1,
            perPage: response.data.meta.per_page || 15,
            total: response.data.meta.total || this.reservations.length,
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
     * Récupère une réservation par son ID
     */
    async fetchReservation(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/admin/reservations/${id}`)
        this.currentReservation = response.data.data
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
     * Crée une nouvelle réservation
     */
    async createReservation(payload) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.post('/admin/reservations', payload)
        this.successMessage = response.data.message || 'Réservation enregistrée avec succès !'
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
     * Met à jour une réservation existante
     */
    async updateReservation(id, payload) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.put(`/admin/reservations/${id}`, payload)
        this.successMessage = response.data.message || 'Réservation mise à jour avec succès !'
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
     * Confirme une réservation
     */
    async confirmReservation(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.patch(`/admin/reservations/${id}/confirm`)
        this.successMessage = response.data.message || 'Réservation confirmée avec succès !'
        const updated = response.data.data
        if (updated) {
          const index = this.reservations.findIndex((r) => r.id === id)
          if (index !== -1) {
            this.reservations[index] = updated
          }
          if (this.currentReservation?.id === id) {
            this.currentReservation = updated
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
     * Rejette / Annule une réservation
     */
    async rejectReservation(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.patch(`/admin/reservations/${id}/reject`)
        this.successMessage = response.data.message || 'Réservation rejetée avec succès !'
        const updated = response.data.data
        if (updated) {
          const index = this.reservations.findIndex((r) => r.id === id)
          if (index !== -1) {
            this.reservations[index] = updated
          }
          if (this.currentReservation?.id === id) {
            this.currentReservation = updated
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
     * Clôture / Termine une réservation
     */
    async terminateReservation(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.patch(`/admin/reservations/${id}/terminate`)
        this.successMessage = response.data.message || 'Réservation terminée et clôturée avec succès !'
        const updated = response.data.data
        if (updated) {
          const index = this.reservations.findIndex((r) => r.id === id)
          if (index !== -1) {
            this.reservations[index] = updated
          }
          if (this.currentReservation?.id === id) {
            this.currentReservation = updated
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
     * Supprime une réservation
     */
    async deleteReservation(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.delete(`/admin/reservations/${id}`)
        this.successMessage = response.data.message || 'Réservation supprimée avec succès !'
        this.reservations = this.reservations.filter((r) => r.id !== id)
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
