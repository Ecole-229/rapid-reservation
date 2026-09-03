<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import { useSallesStore } from '@/store/salles'
import { useEquipementsStore } from '@/store/equipements'
import { useReservationsStore } from '@/store/reservations'
import {
  ArrowLeft,
  Calendar,
  Clock,
  MapPin,
  Users,
  Package,
  CheckCircle2,
  AlertCircle,
  Loader2,
  Building2,
  Plus,
  Minus,
  Trash2,
  Pencil,
  Save,
  Info,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

const sallesStore = useSallesStore()
const equipementsStore = useEquipementsStore()
const reservationsStore = useReservationsStore()

const reservationId = route.params.id

const isFetching = ref(true)
const fetchError = ref(null)
const reservation = ref(null)

// Données du formulaire
const selectedSalleId = ref(null)
const debutDateTime = ref('')
const finDateTime = ref('')
const nombrePersonnes = ref(1)
const selectedEquipements = ref([]) // [{ equipement_id, nom, quantity, stock_total }]

// États de vérification & soumission
const checkingDispo = ref(false)
const dispoResult = ref(null)
const dispoError = ref(null)
const submitError = ref(null)
const submitting = computed(() => reservationsStore.submitting)

const defaultImage =
  'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80'

// Convertir une date ISO en format input datetime-local (YYYY-MM-DDTHH:mm)
const toInputDateTime = (dtStr) => {
  if (!dtStr) return ''
  try {
    const d = new Date(dtStr)
    const yyyy = d.getFullYear()
    const mm = String(d.getMonth() + 1).padStart(2, '0')
    const dd = String(d.getDate()).padStart(2, '0')
    const hh = String(d.getHours()).padStart(2, '0')
    const min = String(d.getMinutes()).padStart(2, '0')
    return `${yyyy}-${mm}-${dd}T${hh}:${min}`
  } catch {
    return ''
  }
}

// Convertir un input datetime-local en YYYY-MM-DD HH:mm:ss
const toApiDateTime = (dtLocal) => {
  if (!dtLocal) return ''
  return dtLocal.replace('T', ' ') + ':00'
}

onMounted(async () => {
  try {
    isFetching.value = true
    fetchError.value = null

    // Charger les listes de salles et équipements
    await Promise.all([
      sallesStore.fetchSalles(),
      equipementsStore.fetchEquipements(),
      reservationsStore.fetchReservation(reservationId),
    ])

    reservation.value = reservationsStore.currentReservation

    if (reservation.value) {
      selectedSalleId.value = reservation.value.salle_id
      debutDateTime.value = toInputDateTime(reservation.value.date_heure_debut)
      finDateTime.value = toInputDateTime(reservation.value.date_heure_fin)
      nombrePersonnes.value = reservation.value.nombre_personnes || 1

      if (reservation.value.equipements && reservation.value.equipements.length > 0) {
        selectedEquipements.value = reservation.value.equipements.map((eq) => ({
          equipement_id: eq.id,
          nom: eq.nom,
          quantity: eq.pivot?.quantity || eq.quantity || 1,
          stock_total: eq.stock_total || 99,
        }))
      }
    }
  } catch (err) {
    fetchError.value = reservationsStore.errorMessage || 'Impossible de charger la réservation à modifier.'
  } finally {
    isFetching.value = false
  }
})

// Stores computed
const salles = computed(() => sallesStore.salles || [])
const allEquipements = computed(() => equipementsStore.equipements || [])

const selectedSalle = computed(() => {
  return salles.value.find((s) => s.id === selectedSalleId.value) || reservation.value?.salle || null
})

// Équipements disponibles non encore ajoutés
const availableEquipementsToAdd = computed(() => {
  const selectedIds = selectedEquipements.value.map((e) => e.equipement_id)
  return allEquipements.value.filter((eq) => !selectedIds.includes(eq.id))
})

const isModifiable = computed(() => {
  return reservation.value && (reservation.value.status === 'en_attente' || reservation.value.status === 'confirmee')
})

// Gestion des équipements
const addEquipement = (eq) => {
  if (selectedEquipements.value.some((e) => e.equipement_id === eq.id)) return
  selectedEquipements.value.push({
    equipement_id: eq.id,
    nom: eq.nom,
    quantity: 1,
    stock_total: eq.stock_total || 99,
  })
}

const removeEquipement = (eqId) => {
  selectedEquipements.value = selectedEquipements.value.filter((e) => e.equipement_id !== eqId)
}

const incrementQty = (item) => {
  if (item.quantity < item.stock_total) {
    item.quantity++
  }
}

const decrementQty = (item) => {
  if (item.quantity > 1) {
    item.quantity--
  }
}

// Vérifier la disponibilité de la salle sur ce créneau
const checkCreneau = async () => {
  dispoError.value = null
  dispoResult.value = null

  if (!selectedSalleId.value) {
    dispoError.value = 'Veuillez sélectionner une salle.'
    return
  }
  if (!debutDateTime.value || !finDateTime.value) {
    dispoError.value = 'Veuillez renseigner les dates de début et de fin.'
    return
  }
  if (new Date(debutDateTime.value) >= new Date(finDateTime.value)) {
    dispoError.value = 'La date de fin doit être postérieure à la date de début.'
    return
  }

  checkingDispo.value = true
  try {
    const res = await sallesStore.checkDisponibilite(
      selectedSalleId.value,
      toApiDateTime(debutDateTime.value),
      toApiDateTime(finDateTime.value)
    )
    dispoResult.value = res
  } catch (err) {
    dispoError.value = err.message || 'Erreur lors de la vérification de disponibilité.'
  } finally {
    checkingDispo.value = false
  }
}

// Soumission de la mise à jour
const handleUpdate = async () => {
  submitError.value = null

  if (!selectedSalleId.value) {
    submitError.value = 'Veuillez sélectionner une salle.'
    return
  }
  if (!debutDateTime.value || !finDateTime.value) {
    submitError.value = 'Veuillez indiquer les dates et heures de début et de fin.'
    return
  }
  if (new Date(debutDateTime.value) >= new Date(finDateTime.value)) {
    submitError.value = 'La date de fin doit être postérieure à la date de début.'
    return
  }
  if (!nombrePersonnes.value || nombrePersonnes.value < 1) {
    submitError.value = 'Le nombre de personnes doit être d’au moins 1.'
    return
  }
  if (selectedSalle.value?.capacite && nombrePersonnes.value > selectedSalle.value.capacite) {
    submitError.value = `Cette salle a une capacité maximale de ${selectedSalle.value.capacite} personnes.`
    return
  }

  const payload = {
    salle_id: selectedSalleId.value,
    date_heure_debut: toApiDateTime(debutDateTime.value),
    date_heure_fin: toApiDateTime(finDateTime.value),
    nombre_personnes: Number(nombrePersonnes.value),
    equipements: selectedEquipements.value.map((e) => ({
      equipement_id: e.equipement_id,
      quantity: Number(e.quantity),
    })),
  }

  try {
    await reservationsStore.updateReservation(reservationId, payload)
    router.push({ name: 'user-reservation-details', params: { id: reservationId } })
  } catch (err) {
    submitError.value =
      reservationsStore.errorMessage ||
      Object.values(reservationsStore.validationErrors).flat().join(' — ') ||
      'Une erreur est survenue lors de la mise à jour.'
  }
}
</script>

<template>
  <div class="min-h-screen bg-[#F8FAFC] flex flex-col justify-between">
    <NavBar />

    <main class="flex-1 pt-28 pb-16 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-4xl">
        <!-- RETOUR -->
        <div class="mb-6">
          <RouterLink
            :to="{ name: 'user-reservation-details', params: { id: reservationId } }"
            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50"
          >
            <ArrowLeft :size="15" />
            <span>Retour aux détails de la réservation</span>
          </RouterLink>
        </div>

        <!-- CHARGEMENT INITIAL -->
        <div
          v-if="isFetching"
          class="flex flex-col items-center justify-center rounded-3xl border border-slate-200/80 bg-white p-16 shadow-xs"
        >
          <Loader2 :size="36" class="animate-spin text-[#4F46E5]" />
          <p class="mt-4 text-sm font-semibold text-slate-600">Chargement de la réservation...</p>
        </div>

        <!-- ERREUR DE CHARGEMENT -->
        <div
          v-else-if="fetchError"
          class="rounded-3xl border border-rose-200 bg-rose-50 p-8 text-center"
        >
          <AlertCircle :size="36" class="mx-auto mb-3 text-rose-500" />
          <h3 class="text-base font-bold text-rose-900">Impossible de charger la réservation</h3>
          <p class="mt-1 text-xs text-rose-600">{{ fetchError }}</p>
          <div class="mt-5">
            <RouterLink
              :to="{ name: 'user-reservations' }"
              class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-[#4F46E5] transition"
            >
              Retour à la liste des réservations
            </RouterLink>
          </div>
        </div>

        <!-- RESERVATION NON MODIFIABLE -->
        <div
          v-else-if="!isModifiable"
          class="rounded-3xl border border-amber-200 bg-amber-50/80 p-8 text-center"
        >
          <AlertCircle :size="36" class="mx-auto mb-3 text-amber-600" />
          <h3 class="text-base font-bold text-amber-900">Réservation non modifiable</h3>
          <p class="mt-1.5 text-xs text-amber-700 max-w-md mx-auto leading-relaxed">
            Cette réservation a le statut <strong>« {{ reservation?.status }} »</strong> et ne peut plus être modifiée. Seules les réservations en attente ou confirmées sont éditables.
          </p>
          <div class="mt-5">
            <RouterLink
              :to="{ name: 'user-reservation-details', params: { id: reservationId } }"
              class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-[#4F46E5] transition"
            >
              Consulter la réservation
            </RouterLink>
          </div>
        </div>

        <!-- FORMULAIRE DE MODIFICATION -->
        <form v-else @submit.prevent="handleUpdate" class="space-y-6">
          <!-- TITRE & AVERTISSEMENT -->
          <div>
            <div class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3.5 py-1 text-xs font-semibold text-[#4F46E5] mb-3">
              <Pencil :size="13" />
              <span>Modification de réservation</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#0F172A]">
              Modifier la réservation #{{ reservationId }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
              Ajustez vos dates, la salle souhaitée ou le nombre d'équipements pour cet événement.
            </p>
          </div>

          <div
            v-if="reservation.status === 'confirmee'"
            class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-xs text-amber-900"
          >
            <Info :size="18" class="text-amber-600 shrink-0 mt-0.5" />
            <div>
              <p class="font-bold">Attention</p>
              <p class="mt-0.5 text-amber-800 leading-relaxed">
                Cette réservation était confirmée. Toute modification des dates ou de la salle la repositionnera sous le statut <strong>« En attente »</strong> afin d'être validée à nouveau par notre équipe.
              </p>
            </div>
          </div>

          <!-- SECTION 1 : CHOIX OU CHANGEMENT DE SALLE -->
          <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs">
            <h2 class="text-base font-bold text-[#0F172A] flex items-center gap-2 mb-4">
              <Building2 :size="18" class="text-[#4F46E5]" />
              <span>Salle sélectionnée</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-80 overflow-y-auto pr-1">
              <label
                v-for="salle in salles"
                :key="salle.id"
                class="flex cursor-pointer items-start gap-3 rounded-2xl border-2 p-3.5 transition-all"
                :class="
                  selectedSalleId === salle.id
                    ? 'border-[#4F46E5] bg-indigo-50/50 shadow-xs'
                    : 'border-slate-200 hover:border-slate-300'
                "
              >
                <input
                  type="radio"
                  :value="salle.id"
                  v-model="selectedSalleId"
                  class="sr-only"
                />

                <div class="h-14 w-14 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                  <img
                    :src="salle.images?.[0]?.url || salle.images?.[0]?.path || defaultImage"
                    :alt="salle.nom"
                    class="h-full w-full object-cover"
                  />
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ salle.nom }}</p>
                    <span
                      v-if="selectedSalleId === salle.id"
                      class="rounded-full bg-[#4F46E5] p-0.5 text-white"
                    >
                      <CheckCircle2 :size="14" />
                    </span>
                  </div>
                  <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                    <MapPin :size="11" />
                    <span class="truncate">{{ salle.localisation }}</span>
                  </p>
                  <p class="text-[11px] font-semibold text-indigo-600 mt-1">
                    Capacité : {{ salle.capacite }} pers.
                  </p>
                </div>
              </label>
            </div>
          </div>

          <!-- SECTION 2 : CRÉNEAUX & NOMBRE DE PERSONNES -->
          <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs">
            <h2 class="text-base font-bold text-[#0F172A] flex items-center gap-2 mb-4">
              <Calendar :size="18" class="text-[#4F46E5]" />
              <span>Date, horaire & capacité</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Début -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                  Date et heure de début
                </label>
                <input
                  v-model="debutDateTime"
                  type="datetime-local"
                  required
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-xs font-medium text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-hidden"
                />
              </div>

              <!-- Fin -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                  Date et heure de fin
                </label>
                <input
                  v-model="finDateTime"
                  type="datetime-local"
                  required
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-xs font-medium text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-hidden"
                />
              </div>
            </div>

            <!-- Bouton test disponibilité -->
            <div class="mt-3 flex items-center justify-between">
              <button
                type="button"
                @click="checkCreneau"
                :disabled="checkingDispo"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4F46E5] hover:text-[#4338CA] hover:underline cursor-pointer disabled:opacity-50"
              >
                <Loader2 v-if="checkingDispo" :size="13" class="animate-spin" />
                <span>Tester la disponibilité de ce créneau</span>
              </button>

              <span
                v-if="dispoResult"
                class="text-xs font-bold"
                :class="dispoResult.disponible ? 'text-emerald-600' : 'text-rose-600'"
              >
                {{ dispoResult.disponible ? '✓ Créneau disponible' : '✗ Créneau indisponible' }}
              </span>
            </div>

            <div v-if="dispoError" class="mt-2 text-xs text-rose-600">
              {{ dispoError }}
            </div>

            <!-- Nombre de personnes -->
            <div class="mt-5 pt-5 border-t border-slate-100">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <label class="block text-xs font-bold text-slate-700">
                    Nombre de personnes
                  </label>
                  <p class="text-[11px] text-slate-400">
                    Capacité maximale de la salle : {{ selectedSalle?.capacite || '—' }} places.
                  </p>
                </div>
                <div class="flex items-center gap-3">
                  <input
                    v-model.number="nombrePersonnes"
                    type="number"
                    min="1"
                    :max="selectedSalle?.capacite || 500"
                    class="w-28 rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-center text-sm font-bold text-slate-900 focus:border-[#4F46E5] focus:bg-white focus:outline-hidden"
                  />
                  <span class="text-xs text-slate-500 font-medium">personnes</span>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 3 : ÉQUIPEMENTS -->
          <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs">
            <h2 class="text-base font-bold text-[#0F172A] flex items-center gap-2 mb-4">
              <Package :size="18" class="text-[#4F46E5]" />
              <span>Équipements réservés</span>
            </h2>

            <!-- Liste des équipements déjà sélectionnés -->
            <div v-if="selectedEquipements.length > 0" class="space-y-2.5 mb-6">
              <div
                v-for="item in selectedEquipements"
                :key="item.equipement_id"
                class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50/60 p-3.5"
              >
                <div class="flex items-center gap-3">
                  <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100/60 text-[#4F46E5] shrink-0">
                    <Package :size="16" />
                  </div>
                  <div>
                    <p class="text-xs font-bold text-slate-900">{{ item.nom }}</p>
                    <p class="text-[10px] text-slate-400">Stock max : {{ item.stock_total }}</p>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <!-- Contrôle quantité -->
                  <div class="flex items-center rounded-xl border border-slate-200 bg-white p-1">
                    <button
                      type="button"
                      @click="decrementQty(item)"
                      class="flex h-6 w-6 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 cursor-pointer"
                    >
                      <Minus :size="12" />
                    </button>
                    <span class="w-8 text-center text-xs font-bold text-slate-900">
                      {{ item.quantity }}
                    </span>
                    <button
                      type="button"
                      @click="incrementQty(item)"
                      class="flex h-6 w-6 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 cursor-pointer"
                    >
                      <Plus :size="12" />
                    </button>
                  </div>

                  <!-- Supprimer -->
                  <button
                    type="button"
                    @click="removeEquipement(item.equipement_id)"
                    class="p-1.5 text-slate-400 hover:text-rose-600 transition cursor-pointer"
                  >
                    <Trash2 :size="15" />
                  </button>
                </div>
              </div>
            </div>

            <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-5 text-center text-xs text-slate-400 mb-6">
              Aucun équipement supplémentaire n'est sélectionné.
            </div>

            <!-- Ajouter d'autres équipements -->
            <div v-if="availableEquipementsToAdd.length > 0">
              <label class="block text-xs font-bold text-slate-700 mb-2">
                Ajouter un équipement additionnel
              </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="eq in availableEquipementsToAdd"
                  :key="eq.id"
                  type="button"
                  @click="addEquipement(eq)"
                  class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50/80 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:border-indigo-200 hover:text-[#4F46E5] transition cursor-pointer"
                >
                  <Plus :size="13" />
                  <span>{{ eq.nom }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- ERREUR DE SOUMISSION -->
          <div
            v-if="submitError"
            class="flex items-start gap-2.5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-700"
          >
            <AlertCircle :size="17" class="shrink-0 mt-0.5" />
            <span>{{ submitError }}</span>
          </div>

          <!-- ACTIONS : ENREGISTRER & ANNULER -->
          <div class="flex items-center justify-between gap-4 pt-4">
            <RouterLink
              :to="{ name: 'user-reservation-details', params: { id: reservationId } }"
              class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 hover:bg-slate-50 transition"
            >
              Annuler les modifications
            </RouterLink>

            <button
              type="submit"
              :disabled="submitting"
              class="inline-flex items-center gap-2 rounded-2xl bg-[#4F46E5] px-7 py-3 text-sm font-bold text-white shadow-md shadow-indigo-300/40 hover:bg-[#4338CA] active:scale-[0.98] transition cursor-pointer disabled:opacity-60"
            >
              <Loader2 v-if="submitting" :size="16" class="animate-spin" />
              <Save v-else :size="16" />
              <span>{{ submitting ? 'Enregistrement...' : 'Enregistrer les modifications' }}</span>
            </button>
          </div>
        </form>
      </div>
    </main>

    <Footer />
  </div>
</template>
