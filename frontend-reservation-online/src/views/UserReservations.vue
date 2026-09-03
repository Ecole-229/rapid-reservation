<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import { useReservationsStore } from '@/store/reservations'
import {
  Calendar,
  Clock,
  MapPin,
  Users,
  Package,
  ArrowRight,
  Plus,
  Search,
  CheckCircle2,
  Clock3,
  XCircle,
  Ban,
  Check,
  AlertCircle,
  Loader2,
  Sparkles,
  RefreshCw,
  Building2,
} from 'lucide-vue-next'

const reservationsStore = useReservationsStore()

const searchQuery = ref('')
const activeTab = ref('all') // 'all', 'en_attente', 'confirmee', 'terminee', 'annulee'

// État du modal d'annulation
const isCancelModalOpen = ref(false)
const reservationToCancel = ref(null)
const isCancelling = ref(false)
const cancelError = ref(null)

const defaultImage =
  'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80'

onMounted(async () => {
  await loadReservations()
})

const loadReservations = async () => {
  try {
    await reservationsStore.fetchMyReservations()
  } catch (err) {
    console.error('Erreur chargement des réservations :', err)
  }
}

const reservations = computed(() => reservationsStore.reservations || [])
const isLoading = computed(() => reservationsStore.loading)
const errorMessage = computed(() => reservationsStore.errorMessage)

// Compteurs par statut
const counts = computed(() => {
  const list = reservations.value
  return {
    all: list.length,
    en_attente: list.filter((r) => r.status === 'en_attente').length,
    confirmee: list.filter((r) => r.status === 'confirmee').length,
    terminee: list.filter((r) => r.status === 'terminee').length,
    annulee: list.filter((r) => r.status === 'annulee' || r.status === 'rejetee').length,
  }
})

// Liste filtrée selon l'onglet et la recherche
const filteredReservations = computed(() => {
  let list = reservations.value

  // Filtre par onglet
  if (activeTab.value === 'en_attente') {
    list = list.filter((r) => r.status === 'en_attente')
  } else if (activeTab.value === 'confirmee') {
    list = list.filter((r) => r.status === 'confirmee')
  } else if (activeTab.value === 'terminee') {
    list = list.filter((r) => r.status === 'terminee')
  } else if (activeTab.value === 'annulee') {
    list = list.filter((r) => r.status === 'annulee' || r.status === 'rejetee')
  }

  // Filtre par recherche textuelle (nom de la salle ou ID)
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter((r) => {
      const salleNom = r.salle?.nom ? r.salle.nom.toLowerCase() : ''
      const salleLoc = r.salle?.localisation ? r.salle.localisation.toLowerCase() : ''
      const idMatch = String(r.id).includes(q)
      return salleNom.includes(q) || salleLoc.includes(q) || idMatch
    })
  }

  return list
})

// Utilitaires de formatage
const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A'
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    }).format(new Date(dateStr))
  } catch {
    return dateStr
  }
}

const formatTimeRange = (debutStr, finStr) => {
  if (!debutStr || !finStr) return ''
  try {
    const debut = new Intl.DateTimeFormat('fr-FR', {
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(debutStr))
    const fin = new Intl.DateTimeFormat('fr-FR', {
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(finStr))
    return `${debut} - ${fin}`
  } catch {
    return ''
  }
}

const getSalleImage = (reservation) => {
  if (reservation?.salle?.images && reservation.salle.images.length > 0) {
    const first = reservation.salle.images[0]
    return first.url || first.path || defaultImage
  }
  return defaultImage
}

// Modal d'annulation
const openCancelModal = (reservation) => {
  reservationToCancel.value = reservation
  cancelError.value = null
  isCancelModalOpen.value = true
}

const closeCancelModal = () => {
  if (isCancelling.value) return
  isCancelModalOpen.value = false
  reservationToCancel.value = null
  cancelError.value = null
}

const confirmCancelReservation = async () => {
  if (!reservationToCancel.value) return
  isCancelling.value = true
  cancelError.value = null
  try {
    await reservationsStore.cancelReservation(reservationToCancel.value.id)
    closeCancelModal()
    await loadReservations()
  } catch (err) {
    cancelError.value = reservationsStore.errorMessage || "Impossible d'annuler cette réservation."
  } finally {
    isCancelling.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-[#F8FAFC] flex flex-col justify-between">
    <NavBar />

    <main class="flex-1 pt-28 pb-16 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-6xl">
        <!-- EN-TÊTE DE LA PAGE -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
          <div>
            <div
              class="inline-flex items-center gap-1.5 rounded-full bg-[#EEF2FF] px-3.5 py-1 text-xs font-semibold text-[#4F46E5] mb-3"
            >
              <Sparkles :size="13" />
              <span>Espace Client</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#0F172A]">
              Mes Réservations
            </h1>
            <p class="mt-1 text-sm text-slate-500">
              Consultez l'historique et le statut en temps réel de vos réservations de salles.
            </p>
          </div>

          <RouterLink
            to="/reserver"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#4F46E5] px-5 py-3 text-sm font-bold text-white shadow-md shadow-indigo-300/40 hover:bg-[#4338CA] active:scale-[0.98] transition cursor-pointer self-start md:self-auto"
          >
            <Plus :size="16" />
            <span>Nouvelle réservation</span>
          </RouterLink>
        </div>

        <!-- STATISTIQUES RAPIDES -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-8">
          <div class="rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 shadow-xs">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total</p>
            <p class="mt-1 text-2xl font-black text-[#0F172A]">{{ counts.all }}</p>
          </div>
          <div class="rounded-2xl border border-amber-200/60 bg-amber-50/50 p-4 sm:p-5 shadow-xs">
            <p class="text-xs font-medium uppercase tracking-wider text-amber-700">En attente</p>
            <p class="mt-1 text-2xl font-black text-amber-600">{{ counts.en_attente }}</p>
          </div>
          <div class="rounded-2xl border border-emerald-200/60 bg-emerald-50/50 p-4 sm:p-5 shadow-xs">
            <p class="text-xs font-medium uppercase tracking-wider text-emerald-700">Confirmées</p>
            <p class="mt-1 text-2xl font-black text-emerald-600">{{ counts.confirmee }}</p>
          </div>
          <div class="rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4 sm:p-5 shadow-xs">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Terminées</p>
            <p class="mt-1 text-2xl font-black text-slate-700">{{ counts.terminee }}</p>
          </div>
        </div>

        <!-- BARRE D'ACTION : ONGLETS & RECHERCHE -->
        <div
          class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 mb-8 bg-white p-2 sm:p-3 rounded-2xl border border-slate-200/80 shadow-xs"
        >
          <!-- Onglets de filtre -->
          <div class="flex items-center gap-1 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
            <button
              type="button"
              @click="activeTab = 'all'"
              class="whitespace-nowrap px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
              :class="
                activeTab === 'all'
                  ? 'bg-[#4F46E5] text-white shadow-sm'
                  : 'text-slate-600 hover:bg-slate-100'
              "
            >
              Toutes ({{ counts.all }})
            </button>
            <button
              type="button"
              @click="activeTab = 'en_attente'"
              class="whitespace-nowrap px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
              :class="
                activeTab === 'en_attente'
                  ? 'bg-amber-500 text-white shadow-sm'
                  : 'text-slate-600 hover:bg-slate-100'
              "
            >
              En attente ({{ counts.en_attente }})
            </button>
            <button
              type="button"
              @click="activeTab = 'confirmee'"
              class="whitespace-nowrap px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
              :class="
                activeTab === 'confirmee'
                  ? 'bg-emerald-600 text-white shadow-sm'
                  : 'text-slate-600 hover:bg-slate-100'
              "
            >
              Confirmées ({{ counts.confirmee }})
            </button>
            <button
              type="button"
              @click="activeTab = 'terminee'"
              class="whitespace-nowrap px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
              :class="
                activeTab === 'terminee'
                  ? 'bg-slate-700 text-white shadow-sm'
                  : 'text-slate-600 hover:bg-slate-100'
              "
            >
              Terminées ({{ counts.terminee }})
            </button>
            <button
              type="button"
              @click="activeTab = 'annulee'"
              class="whitespace-nowrap px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
              :class="
                activeTab === 'annulee'
                  ? 'bg-rose-600 text-white shadow-sm'
                  : 'text-slate-600 hover:bg-slate-100'
              "
            >
              Annulées ({{ counts.annulee }})
            </button>
          </div>

          <!-- Champ recherche -->
          <div class="relative min-w-[240px]">
            <Search :size="15" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher une salle..."
              class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-9 pr-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-[#4F46E5] focus:bg-white focus:outline-hidden"
            />
          </div>
        </div>

        <!-- CHARGEMENT -->
        <div
          v-if="isLoading"
          class="flex flex-col items-center justify-center rounded-3xl border border-slate-200/80 bg-white p-16 shadow-xs"
        >
          <Loader2 :size="32" class="animate-spin text-[#4F46E5]" />
          <p class="mt-3 text-sm font-medium text-slate-500">Chargement de vos réservations...</p>
        </div>

        <!-- ERREUR -->
        <div
          v-else-if="errorMessage && reservations.length === 0"
          class="rounded-3xl border border-rose-200 bg-rose-50 p-8 text-center"
        >
          <AlertCircle :size="32" class="mx-auto mb-2 text-rose-500" />
          <p class="text-sm font-bold text-rose-800">Impossible de charger vos réservations</p>
          <p class="mt-1 text-xs text-rose-600">{{ errorMessage }}</p>
          <button
            type="button"
            @click="loadReservations"
            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 transition cursor-pointer"
          >
            <RefreshCw :size="14" />
            <span>Réessayer</span>
          </button>
        </div>

        <!-- ÉTAT VIDE : AUCUNE RÉSERVATION AU TOTAL -->
        <div
          v-else-if="reservations.length === 0"
          class="flex flex-col items-center justify-center rounded-3xl border border-slate-200/80 bg-white p-12 sm:p-16 text-center shadow-xs"
        >
          <div
            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-[#4F46E5] mb-4"
          >
            <Building2 :size="32" />
          </div>
          <h3 class="text-lg font-bold text-[#0F172A]">Vous n'avez aucune réservation</h3>
          <p class="mt-1.5 max-w-md text-sm text-slate-500">
            Découvrez nos espaces modulables et modernes pour vos réunions, séminaires ou
            conférences.
          </p>
          <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <RouterLink
              to="/salles"
              class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition"
            >
              <span>Explorer les salles</span>
            </RouterLink>
            <RouterLink
              to="/reserver"
              class="inline-flex items-center gap-2 rounded-2xl bg-[#4F46E5] px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-[#4338CA] transition"
            >
              <Plus :size="14" />
              <span>Créer une réservation</span>
            </RouterLink>
          </div>
        </div>

        <!-- ÉTAT VIDE : AUCUN RÉSULTAT POUR LE FILTRE -->
        <div
          v-else-if="filteredReservations.length === 0"
          class="flex flex-col items-center justify-center rounded-3xl border border-slate-200/80 bg-white p-12 text-center shadow-xs"
        >
          <Search :size="32" class="text-slate-300 mb-3" />
          <h3 class="text-base font-bold text-[#0F172A]">Aucune réservation correspondante</h3>
          <p class="mt-1 text-xs text-slate-500">
            Aucun événement ne correspond à vos filtres actuels.
          </p>
          <button
            type="button"
            @click=";(activeTab = 'all'), (searchQuery = '')"
            class="mt-4 rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition cursor-pointer"
          >
            Réinitialiser les filtres
          </button>
        </div>

        <!-- LISTE DES RÉSERVATIONS -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div
            v-for="item in filteredReservations"
            :key="item.id"
            class="group flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white overflow-hidden shadow-xs hover:shadow-md hover:border-indigo-200 transition-all duration-200"
          >
            <!-- Partie supérieure : Image & Badge statut -->
            <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
              <img
                :src="getSalleImage(item)"
                :alt="item.salle?.nom || 'Salle'"
                class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

              <!-- Badge statut -->
              <div class="absolute top-3 left-3">
                <span
                  v-if="item.status === 'confirmee'"
                  class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/90 backdrop-blur-md px-3 py-1 text-xs font-bold text-white shadow-xs"
                >
                  <CheckCircle2 :size="13" />
                  <span>Confirmée</span>
                </span>
                <span
                  v-else-if="item.status === 'en_attente'"
                  class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/90 backdrop-blur-md px-3 py-1 text-xs font-bold text-white shadow-xs"
                >
                  <Clock3 :size="13" />
                  <span>En attente</span>
                </span>
                <span
                  v-else-if="item.status === 'terminee'"
                  class="inline-flex items-center gap-1.5 rounded-full bg-slate-800/90 backdrop-blur-md px-3 py-1 text-xs font-bold text-white shadow-xs"
                >
                  <Check :size="13" />
                  <span>Terminée</span>
                </span>
                <span
                  v-else-if="item.status === 'rejetee'"
                  class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/90 backdrop-blur-md px-3 py-1 text-xs font-bold text-white shadow-xs"
                >
                  <XCircle :size="13" />
                  <span>Rejetée</span>
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 rounded-full bg-slate-600/90 backdrop-blur-md px-3 py-1 text-xs font-bold text-white shadow-xs"
                >
                  <Ban :size="13" />
                  <span>Annulée</span>
                </span>
              </div>

              <!-- Numéro dossier -->
              <div class="absolute top-3 right-3">
                <span
                  class="rounded-full bg-black/40 backdrop-blur-md px-2.5 py-1 text-[11px] font-bold text-white tracking-wider"
                >
                  #{{ item.id }}
                </span>
              </div>

              <!-- Nom salle sur l'image -->
              <div class="absolute bottom-3 left-4 right-4 text-white">
                <h3 class="text-lg font-bold drop-shadow-xs line-clamp-1">
                  {{ item.salle?.nom || 'Salle #' + item.salle_id }}
                </h3>
                <p class="text-xs text-white/80 flex items-center gap-1 line-clamp-1">
                  <MapPin :size="12" />
                  <span>{{ item.salle?.localisation || 'Localisation standard' }}</span>
                </p>
              </div>
            </div>

            <!-- Corps de la carte -->
            <div class="p-5 flex-1 flex flex-col justify-between">
              <!-- Détails du créneau -->
              <div class="space-y-2.5 text-xs text-slate-600">
                <div class="flex items-center gap-2">
                  <Calendar :size="15" class="text-[#4F46E5] shrink-0" />
                  <span class="font-semibold text-slate-800 capitalize">
                    {{ formatDate(item.date_heure_debut) }}
                  </span>
                </div>

                <div class="flex items-center gap-2">
                  <Clock :size="15" class="text-slate-400 shrink-0" />
                  <span>{{ formatTimeRange(item.date_heure_debut, item.date_heure_fin) }}</span>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                  <div class="flex items-center gap-1.5 text-slate-500">
                    <Users :size="14" class="text-slate-400" />
                    <span>{{ item.nombre_personnes }} personne(s)</span>
                  </div>

                  <div
                    v-if="item.equipements && item.equipements.length > 0"
                    class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-[#4F46E5]"
                  >
                    <Package :size="12" />
                    <span>{{ item.equipements.length }} équipement(s)</span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <!-- Actions gauche : Annuler et Modifier si possible -->
                <div class="flex items-center gap-3">
                  <button
                    v-if="item.status === 'en_attente' || item.status === 'confirmee'"
                    type="button"
                    @click="openCancelModal(item)"
                    class="text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer"
                  >
                    Annuler
                  </button>

                  <RouterLink
                    v-if="item.status === 'en_attente' || item.status === 'confirmee'"
                    :to="{ name: 'user-update-reservation', params: { id: item.id } }"
                    class="text-xs font-semibold text-[#4F46E5] hover:text-[#4338CA] hover:underline cursor-pointer"
                  >
                    Modifier
                  </RouterLink>
                </div>

                <!-- Bouton détails -->
                <RouterLink
                  :to="{ name: 'user-reservation-details', params: { id: item.id } }"
                  class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-[#4F46E5] transition cursor-pointer"
                >
                  <span>Détails</span>
                  <ArrowRight :size="14" />
                </RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- MODAL DE CONFIRMATION D'ANNULATION -->
    <div
      v-if="isCancelModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
    >
      <div
        class="w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200"
      >
        <div
          class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 mb-4"
        >
          <AlertCircle :size="24" />
        </div>

        <h3 class="text-lg font-black text-[#0F172A]">Annuler cette réservation ?</h3>
        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
          Êtes-vous sûr de vouloir annuler la réservation #{{ reservationToCancel?.id }} pour
          l'événement prévu à la salle
          <strong>{{ reservationToCancel?.salle?.nom }}</strong> ? Cette action est irréversible.
        </p>

        <!-- Message d'erreur éventuel -->
        <div
          v-if="cancelError"
          class="mt-3 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700"
        >
          {{ cancelError }}
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            type="button"
            @click="closeCancelModal"
            :disabled="isCancelling"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer disabled:opacity-50"
          >
            Non, conserver
          </button>

          <button
            type="button"
            @click="confirmCancelReservation"
            :disabled="isCancelling"
            class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-rose-700 active:scale-95 transition cursor-pointer disabled:opacity-60"
          >
            <Loader2 v-if="isCancelling" :size="14" class="animate-spin" />
            <span>{{ isCancelling ? 'Annulation...' : 'Oui, annuler' }}</span>
          </button>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>
