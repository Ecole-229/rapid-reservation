import { handleError } from '@/helpers/errorHelper'
import axiosClient from '@/plugins/axios'
import { defineStore } from 'pinia'

export const useAdminImagesStore = defineStore('adminImages', {
  state: () => ({
    images: [],
    currentImage: null,
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
     * Récupère la liste des images avec filtres
     */
    async fetchImages(params = {}) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get('/admin/images', { params })
        const data = response.data.data

        if (Array.isArray(data)) {
          this.images = data
        } else {
          this.images = []
        }

        if (response.data.meta) {
          this.meta = {
            currentPage: response.data.meta.current_page || 1,
            lastPage: response.data.meta.last_page || 1,
            perPage: response.data.meta.per_page || 15,
            total: response.data.meta.total || this.images.length,
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
     * Récupère toutes les images d'une salle donnée
     */
    async fetchImagesBySalle(salleId) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/admin/images/salle/${salleId}`)
        return response.data.data || []
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
     * Récupère une image spécifique par son ID
     */
    async fetchImage(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.get(`/admin/images/${id}`)
        this.currentImage = response.data.data
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
     * Ajoute une nouvelle image / média
     */
    async createImage(payload) {
      this.loading = true
      this.clearErrors()

      try {
        const isFormData = payload instanceof FormData
        const headers = isFormData ? { 'Content-Type': 'multipart/form-data' } : {}

        const response = await axiosClient.post('/admin/images', payload, { headers })
        this.successMessage = response.data.message || 'Image ajoutée avec succès !'
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
     * Met à jour une image existante
     */
    async updateImage(id, payload) {
      this.loading = true
      this.clearErrors()

      try {
        let response
        if (payload instanceof FormData) {
          payload.append('_method', 'PUT')
          response = await axiosClient.post(`/admin/images/${id}`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' },
          })
        } else {
          response = await axiosClient.put(`/admin/images/${id}`, payload)
        }

        this.successMessage = response.data.message || 'Image mise à jour avec succès !'
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
     * Supprime une image
     */
    async deleteImage(id) {
      this.loading = true
      this.clearErrors()

      try {
        const response = await axiosClient.delete(`/admin/images/${id}`)
        this.successMessage = response.data.message || 'Image supprimée avec succès !'
        this.images = this.images.filter((img) => img.id !== id)
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
