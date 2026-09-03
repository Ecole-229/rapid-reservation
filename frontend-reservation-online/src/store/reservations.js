import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useReservationsStore = defineStore('reservations', {
  state: () => ({
    reservations: [],
    currentReservation: null,
    loading: false,
    submitting: false,
    errorMessage: null,
    validationErrors: {},
  }),

  actions: {
    clearErrors() {
      this.errorMessage = null
      this.validationErrors = {}
    },

    /**
     * Récupère les réservations de l'utilisateur connecté
     * GET /api/reservations
     */
    async fetchMyReservations() {
      this.loading = true
      this.clearErrors()
      try {
        const response = await axiosClient.get('/reservations')
        this.reservations = response.data.data || []
        return this.reservations
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || 'Erreur lors du chargement de vos réservations.'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Crée une réservation
     * POST /api/reservations
     *
     * Payload attendu par le backend :
     *  {
     *    salle_id          : number       (obligatoire)
     *    date_heure_debut  : string       (YYYY-MM-DD HH:mm:ss, obligatoire)
     *    date_heure_fin    : string       (YYYY-MM-DD HH:mm:ss, obligatoire)
     *    nombre_personnes  : number       (obligatoire, min 1)
     *    equipements?      : Array<{ equipement_id: number, quantity: number }>
     *  }
     */
    async createReservation(payload) {
      this.submitting = true
      this.clearErrors()
      try {
        const response = await axiosClient.post('/reservations', payload)
        this.currentReservation = response.data.data
        return this.currentReservation
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || 'Erreur lors de la création de la réservation.'
        // Expose validation errors (422)
        if (error.response?.status === 422) {
          this.validationErrors = error.response.data.errors || {}
        }
        throw error
      } finally {
        this.submitting = false
      }
    },

    /**
     * Annule une réservation
     * DELETE /api/reservations/{id}
     */
    async cancelReservation(id) {
      this.loading = true
      this.clearErrors()
      try {
        await axiosClient.delete(`/reservations/${id}`)
        this.reservations = this.reservations.filter((r) => r.id !== id)
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || 'Erreur lors de l\'annulation de la réservation.'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
