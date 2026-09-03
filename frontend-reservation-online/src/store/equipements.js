import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useEquipementsStore = defineStore('equipements', {
  state: () => ({
    equipements: [],
    currentEquipement: null,
    loading: false,
    errorMessage: null,
  }),

  getters: {
    disponibles: (state) =>
      state.equipements.filter((e) => e.status?.toLowerCase() === 'disponible'),
  },

  actions: {
    clearErrors() {
      this.errorMessage = null
    },

    /**
     * Récupère tous les équipements publics depuis l'API
     */
    async fetchEquipements(params = {}) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/equipements', { params })
        const data = response.data.data

        if (Array.isArray(data)) {
          this.equipements = data
        } else {
          this.equipements = []
        }

        return this.equipements
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || 'Erreur lors du chargement des équipements.'
        console.error('Erreur fetchEquipements:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Récupère les détails d'un équipement par son ID
     */
    async fetchEquipement(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/equipements/${id}`)
        this.currentEquipement = response.data.data
        return this.currentEquipement
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || "Erreur lors du chargement de l'équipement."
        console.error('Erreur fetchEquipement:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
