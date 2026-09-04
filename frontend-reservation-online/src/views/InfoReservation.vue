<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import { useReservationsStore } from '@/store/reservations'
import {
  ArrowLeft,
  Calendar,
  Clock,
  MapPin,
  Users,
  Package,
  CheckCircle2,
  Clock3,
  XCircle,
  Ban,
  Check,
  AlertCircle,
  Loader2,
  ExternalLink,
  DoorOpen,
  User,
  Info,
  Building2,
  Sparkles,
  Pencil,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const reservationsStore = useReservationsStore()

const reservationId = route.params.id
const reservation = ref(null)
const isFetching = ref(true)
const fetchError = ref(null)

// Modal d'annulation
const isCancelModalOpen = ref(false)
const isCancelling = ref(false)
const cancelError = ref(null)

const defaultImage =
  'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80'

onMounted(async () => {
  await loadReservation()
})

const loadReservation = async () => {
  isFetching.value = true
  fetchError.value = null
  try {
    const data = await reservationsStore.fetchReservation(reservationId)
    reservation.value = data
  } catch (err) {
    fetchError.value =
      reservationsStore.errorMessage ||
      'Impossible de charger les détails de cette réservation.'
  } finally {
    isFetching.value = false
  }
}

// Utilitaires de formatage
const formatFullDateTime = (dateStr) => {
  if (!dateStr) return 'N/A'
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(dateStr))
  } catch {
    return dateStr
  }
}

const formatDateOnly = (dateStr) => {
  if (!dateStr) return 'N/A'
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    }).format(new Date(dateStr))
  } catch {
    return dateStr
  }
}

const formatTimeOnly = (dateStr) => {
  if (!dateStr) return ''
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(dateStr))
  } catch {
    return ''
  }
}

// Calcul de la durée en heures et minutes
const durationText = computed(() => {
  if (!reservation.value?.date_heure_debut || !reservation.value?.date_heure_fin) return ''
  const debut = new Date(reservation.value.date_heure_debut)
  const fin = new Date(reservation.value.date_heure_fin)
  const diffMs = fin - debut
  if (diffMs <= 0) return '0 h'
  const totalMinutes = Math.round(diffMs / 60000)
  const hours = Math.floor(totalMinutes / 60)
  const minutes = totalMinutes % 60
  if (minutes === 0) return `${hours} heure${hours > 1 ? 's' : ''}`
  return `${hours}h ${minutes}min`
})

// Image de la salle
const salleCoverImage = computed(() => {
  if (reservation.value?.salle?.images && reservation.value.salle.images.length > 0) {
    const img = reservation.value.salle.images[0]
    return img.url || img.path || defaultImage
  }
  return defaultImage
})

// Modal d'annulation
const openCancelModal = () => {
  cancelError.value = null
  isCancelModalOpen.value = true
}

const closeCancelModal = () => {
  if (isCancelling.value) return
  isCancelModalOpen.value = false
  cancelError.value = null
}

const confirmCancel = async () => {
  isCancelling.value = true
  cancelError.value = null
  try {
    await reservationsStore.cancelReservation(reservationId)
    closeCancelModal()
    await loadReservation()
  } catch (err) {
    cancelError.value =
      reservationsStore.errorMessage ||
      "Impossible d'annuler cette réservation."
  } finally {
    isCancelling.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-[#080909] text-[#eee9dc] flex flex-col justify-between">
    <!-- NAVBAR COMMUNE EXISTANTE -->
    <NavBar />

    <main class="flex-1 pt-28 pb-16 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-5xl">
        <!-- NAVIGATION RETOUR -->
        <div class="mb-6">
          <RouterLink
            :to="{ name: 'user-reservations' }"
            class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-[#ddd6c8] backdrop-blur-md transition hover:bg-white/10 hover:border-white/30"
          >
            <ArrowLeft :size="15" />
            <span>Retour à mes réservations</span>
          </RouterLink>
        </div>

        <!-- CHARGEMENT -->
        <div
          v-if="isFetching"
          class="flex flex-col items-center justify-center rounded-3xl border border-white/10 bg-[#121314] p-16 shadow-2xl"
        >
          <Loader2 :size="36" class="animate-spin text-[#cdbf9f]" />
          <p class="mt-4 text-sm font-semibold text-[#a39e95]">
            Chargement des détails de votre réservation...
          </p>
        </div>

        <!-- ERREUR -->
        <div
          v-else-if="fetchError"
          class="rounded-3xl border border-rose-500/25 bg-rose-500/10 p-8 text-center"
        >
          <AlertCircle :size="36" class="mx-auto mb-3 text-rose-400" />
          <h3 class="text-base font-bold text-rose-200">Réservation introuvable</h3>
          <p class="mt-1 text-xs text-rose-300">{{ fetchError }}</p>
          <div class="mt-5 flex justify-center gap-3">
            <button
              type="button"
              @click="loadReservation"
              class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 transition cursor-pointer"
            >
              Réessayer
            </button>
            <RouterLink
              :to="{ name: 'user-reservations' }"
              class="rounded-xl border border-white/15 bg-[#18191a] px-4 py-2 text-xs font-semibold text-[#ddd6c8] hover:bg-white/10 transition"
            >
              Revenir à la liste
            </RouterLink>
          </div>
        </div>

        <!-- CONTENU PRINCIPAL DÉTAILS -->
        <div v-else-if="reservation" class="space-y-6">
          <!-- EN-TÊTE DE LA RÉSERVATION -->
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-3xl border border-white/10 bg-[#121314] p-6 sm:p-8 shadow-2xl"
          >
            <div>
              <div class="flex items-center gap-3">
                <span
                  class="rounded-lg bg-white/10 border border-white/10 px-2.5 py-1 text-xs font-black text-[#d8cbb0] tracking-wider"
                >
                  RÉSERVATION #{{ reservation.id }}
                </span>
                <span class="text-xs text-[#7d7971]">
                  Enregistrée le {{ formatDateOnly(reservation.created_at) }}
                </span>
              </div>
              <h1 class="mt-2.5 font-serif text-2xl sm:text-3xl text-[#f0e9da]">
                {{ reservation.salle?.nom || 'Salle #' + reservation.salle_id }}
              </h1>
              <p class="mt-1 text-xs sm:text-sm text-[#96938d] flex items-center gap-1.5">
                <MapPin :size="15" class="text-[#cdbf9f] shrink-0" />
                <span>{{ reservation.salle?.localisation || 'Adresse standard' }}</span>
              </p>
            </div>

            <!-- STATUT BADGE & ACTIONS -->
            <div class="flex flex-col sm:items-end gap-3">
              <!-- BADGE STATUT -->
              <span
                v-if="reservation.status === 'confirmee'"
                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-emerald-200 px-4 py-1.5 text-xs font-bold"
              >
                <CheckCircle2 :size="14" class="text-emerald-300" />
                <span>Réservation Confirmée</span>
              </span>
              <span
                v-else-if="reservation.status === 'en_attente'"
                class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 border border-amber-400/30 text-amber-200 px-4 py-1.5 text-xs font-bold"
              >
                <Clock3 :size="14" class="text-amber-300" />
                <span>En attente de validation</span>
              </span>
              <span
                v-else-if="reservation.status === 'terminee'"
                class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/15 text-[#cfc7b8] px-4 py-1.5 text-xs font-bold"
              >
                <Check :size="14" class="text-white/70" />
                <span>Événement Terminé</span>
              </span>
              <span
                v-else-if="reservation.status === 'rejetee'"
                class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/15 border border-rose-400/30 text-rose-200 px-4 py-1.5 text-xs font-bold"
              >
                <XCircle :size="14" class="text-rose-300" />
                <span>Réservation Rejetée</span>
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1.5 rounded-full bg-white/5 border border-white/10 text-white/60 px-4 py-1.5 text-xs font-bold"
              >
                <Ban :size="14" class="text-white/40" />
                <span>Réservation Annulée</span>
              </span>

              <!-- BOUTONS D'ACTION (MODIFIER & ANNULER) -->
              <div
                v-if="reservation.status === 'en_attente' || reservation.status === 'confirmee'"
                class="flex items-center gap-2"
              >
                <RouterLink
                  :to="{ name: 'user-update-reservation', params: { id: reservation.id } }"
                  class="inline-flex items-center gap-1.5 rounded-xl border border-[#cdbf9f]/40 bg-[#cdbf9f]/10 px-3.5 py-1.5 text-xs font-bold text-[#e4dac7] hover:bg-[#cdbf9f]/20 transition cursor-pointer"
                >
                  <Pencil :size="13" />
                  <span>Modifier</span>
                </RouterLink>

                <button
                  type="button"
                  @click="openCancelModal"
                  class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-3.5 py-1.5 text-xs font-bold text-rose-200 hover:bg-rose-500/20 transition cursor-pointer"
                >
                  Annuler la réservation
                </button>
              </div>
            </div>
          </div>

          <!-- BANNIÈRE D'INFORMATION CONTEXTUELLE SUR LE STATUT -->
          <div
            v-if="reservation.status === 'en_attente'"
            class="flex items-start gap-3 rounded-2xl border border-amber-500/25 bg-amber-500/10 p-4 text-xs text-amber-200"
          >
            <Clock3 :size="18" class="text-amber-300 shrink-0 mt-0.5" />
            <div>
              <p class="font-bold">Demande en cours d'examen</p>
              <p class="mt-0.5 text-amber-100/80 leading-relaxed">
                Votre réservation a été transmise à notre équipe. Nous vérifions la disponibilité
                du créneau et des équipements demandés. Vous serez notifié dès que le statut évolue.
              </p>
            </div>
          </div>

          <div
            v-else-if="reservation.status === 'confirmee'"
            class="flex items-start gap-3 rounded-2xl border border-emerald-500/25 bg-emerald-500/10 p-4 text-xs text-emerald-200"
          >
            <CheckCircle2 :size="18" class="text-emerald-300 shrink-0 mt-0.5" />
            <div>
              <p class="font-bold">Réservation validée</p>
              <p class="mt-0.5 text-emerald-100/80 leading-relaxed">
                Votre événement est officiellement confirmé ! La salle et les équipements
                sélectionnés vous seront réservés aux dates et horaires convenus.
              </p>
            </div>
          </div>

          <div
            v-else-if="reservation.status === 'terminee'"
            class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-xs text-[#cfc7b8]"
          >
            <Check :size="18" class="text-white/60 shrink-0 mt-0.5" />
            <div>
              <p class="font-bold">Réservation clôturée</p>
              <p class="mt-0.5 text-[#96938d] leading-relaxed">
                Cet événement s'est déroulé avec succès. Merci pour votre confiance !
              </p>
            </div>
          </div>

          <div
            v-else
            class="flex items-start gap-3 rounded-2xl border border-rose-500/25 bg-rose-500/10 p-4 text-xs text-rose-200"
          >
            <AlertCircle :size="18" class="text-rose-300 shrink-0 mt-0.5" />
            <div>
              <p class="font-bold">Réservation inactive</p>
              <p class="mt-0.5 text-rose-100/80 leading-relaxed">
                Cette réservation a été
                {{ reservation.status === 'rejetee' ? 'rejetée par l’administration' : 'annulée' }}.
                Le créneau horaire a été libéré.
              </p>
            </div>
          </div>

          <!-- GRILLE PRINCIPALE DE CONTENU -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- COLONNE GAUCHE (2/3) : SALLE & ÉQUIPEMENTS -->
            <div class="lg:col-span-2 space-y-6">
              <!-- CARTE SALLE -->
              <div
                class="overflow-hidden rounded-3xl border border-white/10 bg-[#121314] shadow-xl"
              >
                <!-- Image principale -->
                <div class="relative h-64 sm:h-72 w-full bg-[#18191a] overflow-hidden">
                  <img
                    :src="salleCoverImage"
                    :alt="reservation.salle?.nom || 'Salle'"
                    class="h-full w-full object-cover"
                  />
                  <div
                    class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"
                  ></div>
                  <div class="absolute bottom-4 left-6 right-6 text-white">
                    <h2 class="font-serif text-2xl sm:text-3xl text-[#f0e9da]">
                      {{ reservation.salle?.nom }}
                    </h2>
                    <p class="text-xs sm:text-sm text-white/70 flex items-center gap-1.5 mt-1">
                      <MapPin :size="14" class="text-[#cdbf9f]" />
                      <span>{{ reservation.salle?.localisation }}</span>
                    </p>
                  </div>
                </div>

                <!-- Détails salle & Lien fiche -->
                <div class="p-6">
                  <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pb-5 border-b border-white/10">
                    <div class="rounded-2xl border border-white/5 bg-[#171819] p-3.5">
                      <span class="text-xs text-[#7d7971]">Capacité max</span>
                      <p class="mt-0.5 text-sm font-bold text-[#e2dacd]">
                        {{ reservation.salle?.capacite || 'N/A' }} places
                      </p>
                    </div>

                    <div class="rounded-2xl border border-white/5 bg-[#171819] p-3.5">
                      <span class="text-xs text-[#7d7971]">Tarif</span>
                      <p class="mt-0.5 text-sm font-bold text-[#cdbf9f]">
                        {{
                          reservation.salle?.prix
                            ? new Intl.NumberFormat('fr-FR').format(reservation.salle.prix) + ' FCFA'
                            : 'Sur demande'
                        }}
                      </p>
                    </div>

                    <div class="col-span-2 sm:col-span-1 rounded-2xl border border-white/5 bg-[#171819] p-3.5">
                      <span class="text-xs text-[#7d7971]">Statut</span>
                      <p class="mt-0.5 text-sm font-bold capitalize text-[#e2dacd]">
                        {{ reservation.salle?.status || 'Disponible' }}
                      </p>
                    </div>
                  </div>

                  <p
                    v-if="reservation.salle?.description"
                    class="mt-4 text-xs sm:text-sm text-[#96938d] leading-relaxed"
                  >
                    {{ reservation.salle.description }}
                  </p>

                  <div class="mt-5 flex justify-end">
                    <RouterLink
                      v-if="reservation.salle_id"
                      :to="{ name: 'info-user-salle', params: { id: reservation.salle_id } }"
                      class="inline-flex items-center gap-1.5 text-xs font-bold text-[#cdbf9f] hover:underline"
                    >
                      <span>Voir la fiche détaillée de la salle</span>
                      <ExternalLink :size="13" />
                    </RouterLink>
                  </div>
                </div>
              </div>

              <!-- CARTE ÉQUIPEMENTS -->
              <div class="rounded-3xl border border-white/10 bg-[#121314] p-6 sm:p-7 shadow-xl">
                <div class="flex items-center justify-between mb-5">
                  <h3 class="text-base font-bold text-[#f0e9da] flex items-center gap-2">
                    <Package :size="18" class="text-[#cdbf9f]" />
                    <span>Équipements associés</span>
                  </h3>
                  <span
                    v-if="reservation.equipements && reservation.equipements.length > 0"
                    class="rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 text-xs font-bold text-[#d8cbb0]"
                  >
                    {{ reservation.equipements.length }} équipement(s)
                  </span>
                </div>

                <!-- État sans équipements -->
                <div
                  v-if="!reservation.equipements || reservation.equipements.length === 0"
                  class="rounded-2xl border border-dashed border-white/10 bg-white/[0.02] p-6 text-center text-xs text-[#7d7971]"
                >
                  Aucun équipement additionnel n'est associé à cette réservation.
                </div>

                <!-- Liste des équipements -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div
                    v-for="item in reservation.equipements"
                    :key="item.id"
                    class="flex items-center justify-between gap-3 rounded-2xl border border-white/5 bg-[#171819] p-3.5"
                  >
                    <div class="flex items-center gap-3 min-w-0">
                      <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-[#cdbf9f] shrink-0"
                      >
                        <Package :size="18" />
                      </div>
                      <div class="min-w-0">
                        <p class="text-xs font-bold text-[#e2dacd] truncate">{{ item.nom }}</p>
                        <p class="text-[11px] text-[#7d7971] truncate">
                          {{ item.description || 'Équipement réservé' }}
                        </p>
                      </div>
                    </div>

                    <span
                      class="shrink-0 rounded-lg bg-white/10 px-2.5 py-1 text-xs font-black text-[#cdbf9f] border border-white/10"
                    >
                      × {{ item.pivot?.quantity || item.quantity || 1 }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- COLONNE DROITE (1/3) : CRÉNEAU & INFOS -->
            <div class="space-y-6">
              <!-- CRÉNEAU HORAIRE -->
              <div class="rounded-3xl border border-white/10 bg-[#121314] p-6 sm:p-7 shadow-xl">
                <h3 class="text-base font-bold text-[#f0e9da] flex items-center gap-2 mb-4">
                  <Calendar :size="18" class="text-[#cdbf9f]" />
                  <span>Créneau de l'événement</span>
                </h3>

                <div class="space-y-3 text-xs">
                  <div class="rounded-2xl border border-white/5 bg-[#171819] p-3.5">
                    <span class="text-[#7d7971]">Date</span>
                    <p class="mt-0.5 text-xs sm:text-sm font-bold text-[#e2dacd] capitalize">
                      {{ formatDateOnly(reservation.date_heure_debut) }}
                    </p>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-white/5 bg-[#171819] p-3.5">
                      <span class="text-[#7d7971]">Début</span>
                      <p class="mt-0.5 text-sm font-bold text-[#e2dacd]">
                        {{ formatTimeOnly(reservation.date_heure_debut) }}
                      </p>
                    </div>
                    <div class="rounded-2xl border border-white/5 bg-[#171819] p-3.5">
                      <span class="text-[#7d7971]">Fin</span>
                      <p class="mt-0.5 text-sm font-bold text-[#e2dacd]">
                        {{ formatTimeOnly(reservation.date_heure_fin) }}
                      </p>
                    </div>
                  </div>

                  <div class="rounded-2xl border border-white/5 bg-[#171819] p-3.5 flex items-center justify-between">
                    <span class="text-[#7d7971]">Durée</span>
                    <span class="font-bold text-[#cdbf9f]">{{ durationText }}</span>
                  </div>

                  <div class="rounded-2xl border border-white/5 bg-[#171819] p-3.5 flex items-center justify-between">
                    <span class="text-[#7d7971]">Participants</span>
                    <span class="font-bold text-[#e2dacd] flex items-center gap-1">
                      <Users :size="14" class="text-[#cdbf9f]" />
                      {{ reservation.nombre_personnes }} personne(s)
                    </span>
                  </div>
                </div>
              </div>

              <!-- COORDONNÉES DE CONTACT -->
              <div class="rounded-3xl border border-white/10 bg-[#121314] p-6 sm:p-7 shadow-xl">
                <h3 class="text-base font-bold text-[#f0e9da] flex items-center gap-2 mb-4">
                  <User :size="18" class="text-[#cdbf9f]" />
                  <span>Client & Contact</span>
                </h3>

                <div class="space-y-3 text-xs">
                  <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#7d7971]">Nom</span>
                    <span class="font-bold text-[#e2dacd]">
                      {{ reservation.nom_affiche || reservation.user?.nom || 'Client' }}
                    </span>
                  </div>

                  <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#7d7971]">Téléphone</span>
                    <span class="font-semibold text-[#e2dacd]">
                      {{
                        reservation.telephone_affiche ||
                        reservation.telephone_client ||
                        reservation.user?.telephone ||
                        'Non renseigné'
                      }}
                    </span>
                  </div>

                  <div
                    v-if="reservation.user?.email"
                    class="flex items-center justify-between py-2"
                  >
                    <span class="text-[#7d7971]">Email</span>
                    <span class="font-semibold text-[#cdbf9f]">
                      {{ reservation.user.email }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- CARTE BESOIN D'AIDE -->
              <div class="rounded-3xl border border-white/10 bg-[#151718] p-6 text-white shadow-xl">
                <div class="flex items-center gap-2 mb-2 text-[#cdbf9f]">
                  <Info :size="17" />
                  <h4 class="text-xs font-bold uppercase tracking-[0.08em]">Besoin d'assistance ?</h4>
                </div>
                <p class="text-xs text-[#96938d] leading-relaxed">
                  Pour toute modification urgente d'horaire ou demande spécifique, veuillez
                  contacter directement notre équipe.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- MODAL DE CONFIRMATION D'ANNULATION -->
    <div
      v-if="isCancelModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
    >
      <div
        class="w-full max-w-md rounded-3xl bg-[#121314] p-6 sm:p-7 shadow-2xl border border-white/15 animate-in fade-in zoom-in-95 duration-200"
      >
        <div
          class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/15 text-rose-400 mb-4 border border-rose-500/30"
        >
          <AlertCircle :size="24" />
        </div>

        <h3 class="text-lg font-bold text-[#f0e9da]">Annuler cette réservation ?</h3>
        <p class="mt-2 text-xs text-[#96938d] leading-relaxed">
          Êtes-vous sûr de vouloir annuler la réservation #{{ reservation?.id }} prévue pour le
          <strong class="text-white">{{ formatDateOnly(reservation?.date_heure_debut) }}</strong> ? Cette action est irréversible.
        </p>

        <div
          v-if="cancelError"
          class="mt-3 rounded-xl border border-rose-500/30 bg-rose-500/10 p-3 text-xs text-rose-200"
        >
          {{ cancelError }}
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            type="button"
            @click="closeCancelModal"
            :disabled="isCancelling"
            class="rounded-xl border border-white/15 bg-transparent px-4 py-2.5 text-xs font-semibold text-[#ddd6c8] hover:bg-white/5 transition cursor-pointer disabled:opacity-50"
          >
            Non, conserver
          </button>

          <button
            type="button"
            @click="confirmCancel"
            :disabled="isCancelling"
            class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-rose-700 active:scale-95 transition cursor-pointer disabled:opacity-60"
          >
            <Loader2 v-if="isCancelling" :size="14" class="animate-spin" />
            <span>{{ isCancelling ? 'Annulation...' : 'Oui, annuler' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- FOOTER COMMUNE DE L'APPLICATION -->
    <Footer />
  </div>
</template>
