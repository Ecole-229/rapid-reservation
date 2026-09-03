import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useSallesStore = defineStore('salles', {
  state: () => ({
    salles: [],
    currentSalle: null,
    loading: false,
    errorMessage: null,
  }),

  getters: {
    disponibles: (state) =>
      state.salles.filter((s) => s.status?.toLowerCase() === 'disponible'),
  },

  actions: {
    clearErrors() {
      this.errorMessage = null
    },

    /**
     * Récupère toutes les salles publiques depuis l'API
     */
    async fetchSalles(params = {}) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/salles', { params })
        const data = response.data.data

        if (Array.isArray(data)) {
          this.salles = data
        } else {
          this.salles = []
        }

        return this.salles
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || 'Erreur lors du chargement des salles.'
        console.error('Erreur fetchSalles:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Récupère les détails d'une salle par son ID
     */
    async fetchSalle(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/salles/${id}`)
        this.currentSalle = response.data.data
        return this.currentSalle
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || 'Erreur lors du chargement de la salle.'
        console.error('Erreur fetchSalle:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Vérifie la disponibilité d'une salle pour un créneau donné
     */
    async checkDisponibilite(salleId, debut, fin) {
      try {
        const response = await axiosClient.get(`/salles/${salleId}/disponibilites`, {
          params: { debut, fin },
        })
        return response.data.data
      } catch (error) {
        const errorData = handleError(error)
        throw errorData
      }
    },
  },
})
