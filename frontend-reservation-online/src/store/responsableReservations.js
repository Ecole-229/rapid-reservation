import { defineStore } from "pinia";
import reservationService from "@/services/reservationService";
import { handleError } from "@/helpers/errorHelper";

export const useResponsableReservationsStore = defineStore("responsableReservations", {
  state: () => ({
    reservations: [],
    loading: false,
    actionLoadingId: null, // évite le double-clic sur une ligne précise
    errorMessage: null,
    creationLoading: false,
    creationErrors: {},
  }),

  getters: {
    parStatut: (state) => (statut) =>
      statut === "toutes"
        ? state.reservations
        : state.reservations.filter((r) => r.status === statut),

    compteurs: (state) => ({
      en_attente: state.reservations.filter((r) => r.status === "en_attente").length,
      confirmee: state.reservations.filter((r) => r.status === "confirmee").length,
      rejetee: state.reservations.filter((r) => r.status === "rejetee").length,
      total: state.reservations.length,
    }),
  },

  actions: {
    async fetchAll() {
      this.loading = true;
      this.errorMessage = null;
      try {
        const response = await reservationService.fetchAll();
        this.reservations = response.data.data;
      } catch (error) {
        this.errorMessage = handleError(error).message;
      } finally {
        this.loading = false;
      }
    },

    async confirmer(id) {
      this.actionLoadingId = id;
      try {
        const response = await reservationService.confirmer(id);
        this.remplacerReservation(response.data.data);
      } catch (error) {
        this.errorMessage = handleError(error).message;
        throw error;
      } finally {
        this.actionLoadingId = null;
      }
    },

    async rejeter(id) {
      this.actionLoadingId = id;
      try {
        const response = await reservationService.rejeter(id);
        this.remplacerReservation(response.data.data);
      } catch (error) {
        this.errorMessage = handleError(error).message;
        throw error;
      } finally {
        this.actionLoadingId = null;
      }
    },

    async creerManuelle(payload) {
      this.creationLoading = true;
      this.creationErrors = {};
      try {
        const response = await reservationService.creerManuelle(payload);
        this.reservations.unshift(response.data.data);
        return true;
      } catch (error) {
        const errorData = handleError(error);
        this.creationErrors = errorData.errors;
        this.errorMessage = errorData.message;
        return false;
      } finally {
        this.creationLoading = false;
      }
    },

    remplacerReservation(updated) {
      const index = this.reservations.findIndex((r) => r.id === updated.id);
      if (index !== -1) this.reservations[index] = updated;
    },

    async annuler(id) {
      this.actionLoadingId = id;
      try {
        const response = await reservationService.annuler(id);
        this.remplacerReservation(response.data.data);
      } catch (error) {
        this.errorMessage = handleError(error).message;
        throw error;
      } finally {
        this.actionLoadingId = null;
      }
    },
  },
});
