import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useAdminDashboardStore = defineStore('adminDashboard', {
  state: () => ({
    cartes: {
      total_utilisateurs: 0,
      total_responsables: 0,
      total_admins: 0,
      total_users_all: 0,
      total_salles: 0,
      salles_disponibles: 0,
      salles_indisponibles: 0,
      total_reservations: 0,
      total_equipements: 0,
      total_images: 0,
    },
    reservations_par_statut: {
      en_attente: 0,
      confirmee: 0,
      rejetee: 0,
      terminee: 0,
      annulee: 0,
    },
    reservations_par_mois: [],
    recent_reservations: [],
    salles: [],
    equipements: [],
    images: [],
    loading: false,
    errorMessage: null,
  }),

  actions: {
    clearErrors() {
      this.errorMessage = null
    },

    async fetchDashboard() {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/admin/dashboard')
        const data = response.data.data

        if (data) {
          this.cartes = data.cartes || this.cartes
          this.reservations_par_statut = data.reservations_par_statut || this.reservations_par_statut
          this.reservations_par_mois = data.reservations_par_mois || []
          this.recent_reservations = data.recent_reservations || []
          this.salles = data.salles || []
          this.equipements = data.equipements || []
          this.images = data.images || []
        }

        return data
      } catch (error) {
        const errorData = handleError(error)
        this.errorMessage = errorData.message || 'Erreur lors du chargement du tableau de bord.'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
