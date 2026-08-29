<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminReservationsStore } from '@/store/adminReservations'
import {
  ArrowLeft,
  Pencil,
  DoorOpen,
  User,
  Users as UsersIcon,
  Phone,
  Mail,
  Calendar,
  Clock,
  Coins,
  CheckCircle2,
  XCircle,
  Flag,
  Check,
  X,
  Server,
  Loader2,
  Info,
} from 'lucide-vue-next'

const route = useRoute()
const adminReservationsStore = useAdminReservationsStore()

const reservationId = route.params.id
const reservation = ref(null)
const isFetching = ref(true)

const loadDetails = async () => {
  isFetching.value = true
  try {
    reservation.value = await adminReservationsStore.fetchReservation(reservationId)
  } catch (error) {
    console.error('Erreur chargement détails réservation :', error)
  } finally {
    isFetching.value = false
  }
}

onMounted(() => {
  loadDetails()
})

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(dateString))
  } catch {
    return dateString
  }
}

const handleConfirm = async () => {
  try {
    await adminReservationsStore.confirmReservation(reservationId)
    await loadDetails()
  } catch (e) {
    console.error('Erreur confirmation :', e)
  }
}

const handleReject = async () => {
  try {
    await adminReservationsStore.rejectReservation(reservationId)
    await loadDetails()
  } catch (e) {
    console.error('Erreur rejet :', e)
  }
}

const handleTerminate = async () => {
  try {
    await adminReservationsStore.terminateReservation(reservationId)
    await loadDetails()
  } catch (e) {
    console.error('Erreur clôture :', e)
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-5xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-reservations' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des réservations</span>
        </RouterLink>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">
              Dossier Réservation #{{ reservationId }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Détails complets, créneaux, client, salle et gestion du statut.
            </p>
          </div>

          <!-- ACTIONS RAPIDES SUR LE STATUT -->
          <div v-if="reservation" class="flex flex-wrap items-center gap-2">
            <!-- Confirmer -->
            <button
              v-if="reservation.status === 'en_attente'"
              type="button"
              class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95"
              @click="handleConfirm"
            >
              <Check :size="16" />
              <span>Confirmer</span>
            </button>

            <!-- Terminer -->
            <button
              v-if="reservation.status === 'confirmee'"
              type="button"
              class="inline-flex items-center gap-1.5 rounded-xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-95"
              @click="handleTerminate"
            >
              <Flag :size="16" />
              <span>Marquer comme terminée</span>
            </button>

            <!-- Rejeter -->
            <button
              v-if="reservation.status === 'en_attente' || reservation.status === 'confirmee'"
              type="button"
              class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 active:scale-95"
              @click="handleReject"
            >
              <X :size="16" />
              <span>Rejeter / Annuler</span>
            </button>

            <!-- Modifier -->
            <RouterLink
              :to="{ name: 'update-reservation', params: { id: reservationId } }"
              class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
            >
              <Pencil :size="16" />
              <span>Modifier</span>
            </RouterLink>
          </div>
        </div>
      </div>

      <!-- CHARGEMENT -->
      <div
        v-if="isFetching"
        class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-16 shadow-sm"
      >
        <Loader2 :size="32" class="animate-spin text-blue-600" />
        <p class="mt-3 text-sm text-gray-500">Chargement de la réservation...</p>
      </div>

      <!-- ERREUR -->
      <div
        v-else-if="adminReservationsStore.errorMessage && !reservation"
        class="rounded-2xl border border-red-200 bg-red-50 p-8 text-center text-sm text-red-700"
      >
        <XCircle :size="32" class="mx-auto mb-3 text-red-400" />
        <p class="font-semibold">Réservation introuvable</p>
        <p class="mt-1">{{ adminReservationsStore.errorMessage }}</p>
      </div>

      <!-- CONTENU DOSSIER -->
      <div v-else-if="reservation" class="space-y-6">
        <!-- BANNIÈRE STATUT & SALLE -->
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6"
            :class="{
              'bg-gradient-to-r from-emerald-600 to-teal-600 text-white': reservation.status === 'confirmee',
              'bg-gradient-to-r from-amber-500 to-orange-500 text-white': reservation.status === 'en_attente',
              'bg-gradient-to-r from-slate-700 to-slate-900 text-white': reservation.status === 'terminee',
              'bg-gradient-to-r from-rose-600 to-red-600 text-white': reservation.status === 'rejetee',
            }"
          >
            <div class="flex items-center gap-4">
              <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20">
                <DoorOpen :size="28" />
              </div>
              <div>
                <p class="text-xs uppercase tracking-wider text-white/80">Salle réservée</p>
                <h2 class="text-xl font-bold">
                  {{ reservation.salle?.nom || 'Salle #' + reservation.salle_id }}
                </h2>
                <p class="text-xs text-white/80">
                  {{ reservation.salle?.localisation }}
                </p>
              </div>
            </div>

            <!-- BADGE STATUT -->
            <div class="flex items-center gap-2 self-start sm:self-center">
              <span class="rounded-full bg-white/20 px-4 py-1.5 text-sm font-bold uppercase tracking-wide backdrop-blur-md">
                Statut : {{ reservation.status }}
              </span>
            </div>
          </div>

          <!-- GRILLE STATISTIQUES RAPIDES -->
          <div class="grid grid-cols-1 gap-px bg-gray-100 sm:grid-cols-3">
            <!-- Début -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                  <Clock :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Début</p>
                  <p class="mt-0.5 text-sm font-bold text-gray-900">{{ formatDateTime(reservation.date_heure_debut) }}</p>
                </div>
              </div>
            </div>

            <!-- Fin -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                  <Clock :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Fin</p>
                  <p class="mt-0.5 text-sm font-bold text-gray-900">{{ formatDateTime(reservation.date_heure_fin) }}</p>
                </div>
              </div>
            </div>

            <!-- Invités -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                  <UsersIcon :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Nombre de personnes</p>
                  <p class="mt-0.5 text-sm font-bold text-gray-900">{{ reservation.nombre_personnes }} personnes</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2 COLONNES : CLIENT & ÉQUIPEMENTS -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <!-- CARTE CLIENT -->
          <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-base font-bold text-gray-800 flex items-center gap-2">
              <User :size="18" class="text-blue-600" />
              <span>Informations du Bénéficiaire</span>
            </h3>

            <div class="space-y-3">
              <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3.5">
                <span class="text-xs text-gray-400">Nom du client</span>
                <span class="text-sm font-bold text-gray-900">{{ reservation.nom_affiche }}</span>
              </div>

              <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3.5">
                <span class="text-xs text-gray-400">Téléphone</span>
                <span class="text-sm font-semibold text-gray-800">{{ reservation.telephone_affiche || 'Non renseigné' }}</span>
              </div>

              <div v-if="reservation.user" class="flex items-center justify-between rounded-xl bg-gray-50 p-3.5">
                <span class="text-xs text-gray-400">Email du compte</span>
                <span class="text-sm font-semibold text-blue-600">{{ reservation.user.email }}</span>
              </div>

              <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3.5">
                <span class="text-xs text-gray-400">Type de réservation</span>
                <span
                  class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                  :class="reservation.user_id ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'"
                >
                  {{ reservation.user_id ? 'Utilisateur plateforme' : 'Client direct / physique' }}
                </span>
              </div>
            </div>
          </div>

          <!-- CARTE ÉQUIPEMENTS -->
          <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-base font-bold text-gray-800 flex items-center gap-2">
              <Server :size="18" class="text-indigo-600" />
              <span>Équipements inclus</span>
            </h3>

            <div
              v-if="!reservation.equipements || reservation.equipements.length === 0"
              class="rounded-xl bg-gray-50 p-6 text-center text-xs text-gray-500"
            >
              Aucun équipement additionnel réservé pour cet événement.
            </div>

            <div v-else class="space-y-2.5">
              <div
                v-for="eq in reservation.equipements"
                :key="eq.id"
                class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50/50 p-3"
              >
                <div class="flex items-center gap-3">
                  <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <Server :size="16" />
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900">{{ eq.nom }}</p>
                    <p class="text-xs text-gray-400">{{ eq.description || 'Équipement standard' }}</p>
                  </div>
                </div>
                <span class="rounded-lg bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">
                  Qté : {{ eq.quantity }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- MÉTADONNÉES & CRÉATEUR -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
          <h3 class="mb-4 text-base font-bold text-gray-800">Historique & Traçabilité</h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-gray-50 p-3.5">
              <p class="text-xs font-semibold uppercase text-gray-400">Créée par</p>
              <p class="mt-1 text-xs font-bold text-gray-800">
                {{ reservation.createur?.nom || (reservation.creer_par ? 'Admin #' + reservation.creer_par : 'Client / Système') }}
              </p>
            </div>

            <div class="rounded-xl bg-gray-50 p-3.5">
              <p class="text-xs font-semibold uppercase text-gray-400">Date d'enregistrement</p>
              <p class="mt-1 text-xs font-medium text-gray-700">{{ formatDateTime(reservation.created_at) }}</p>
            </div>

            <div class="rounded-xl bg-gray-50 p-3.5">
              <p class="text-xs font-semibold uppercase text-gray-400">Clôturée le</p>
              <p class="mt-1 text-xs font-medium text-gray-700">
                {{ reservation.terminee_at ? formatDateTime(reservation.terminee_at) : 'En cours / Non clôturée' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
