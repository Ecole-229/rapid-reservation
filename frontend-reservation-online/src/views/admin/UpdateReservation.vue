<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminReservationsStore } from '@/store/adminReservations'
import { useAdminSallesStore } from '@/store/adminSalles'
import { useAdminUsersStore } from '@/store/adminUsers'
import { useAdminEquipementsStore } from '@/store/adminEquipements'
import {
  ArrowLeft,
  Save,
  Loader2,
  Calendar,
  Clock,
  DoorOpen,
  User,
  Users as UsersIcon,
  Phone,
  Server,
  AlertCircle,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const adminReservationsStore = useAdminReservationsStore()
const adminSallesStore = useAdminSallesStore()
const adminUsersStore = useAdminUsersStore()
const adminEquipementsStore = useAdminEquipementsStore()

const reservationId = route.params.id
const isFetching = ref(true)
const beneficiaryType = ref('user')

const form = reactive({
  user_id: '',
  nom_client: '',
  telephone: '',
  salle_id: '',
  date_heure_debut: '',
  date_heure_fin: '',
  nombre_personnes: 1,
  status: 'en_attente',
  selectedEquipements: {}, // { [equipementId]: quantity }
})

// Format ISO date to YYYY-MM-DDTHH:mm for datetime-local input
const formatForInput = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const offset = date.getTimezoneOffset()
  const localDate = new Date(date.getTime() - offset * 60 * 1000)
  return localDate.toISOString().slice(0, 16)
}

onMounted(async () => {
  adminReservationsStore.clearErrors()
  try {
    const [res] = await Promise.all([
      adminReservationsStore.fetchReservation(reservationId),
      adminSallesStore.salles.length === 0 ? adminSallesStore.fetchSalles({ all: 'true' }) : Promise.resolve(),
      adminUsersStore.users.length === 0 ? adminUsersStore.fetchUsers({ all: 'true' }) : Promise.resolve(),
      adminEquipementsStore.equipements.length === 0 ? adminEquipementsStore.fetchEquipements({ all: 'true' }) : Promise.resolve(),
    ])

    if (res) {
      if (res.user_id) {
        beneficiaryType.value = 'user'
        form.user_id = res.user_id
      } else {
        beneficiaryType.value = 'direct'
        form.nom_client = res.nom_client || ''
        form.telephone = res.telephone || ''
      }

      form.salle_id = res.salle_id
      form.date_heure_debut = formatForInput(res.date_heure_debut)
      form.date_heure_fin = formatForInput(res.date_heure_fin)
      form.nombre_personnes = Number(res.nombre_personnes) || 1
      form.status = res.status || 'en_attente'

      if (res.equipements && Array.isArray(res.equipements)) {
        res.equipements.forEach((eq) => {
          form.selectedEquipements[eq.id] = eq.quantity || 1
        })
      }
    }
  } catch (error) {
    console.error('Erreur chargement réservation :', error)
  } finally {
    isFetching.value = false
  }
})

const selectedSalleObject = computed(() => {
  if (!form.salle_id) return null
  return adminSallesStore.salles.find((s) => s.id === Number(form.salle_id)) || null
})

const isOverCapacity = computed(() => {
  if (!selectedSalleObject.value) return false
  return Number(form.nombre_personnes) > selectedSalleObject.value.capacite
})

const toggleEquipement = (id) => {
  if (form.selectedEquipements[id]) {
    delete form.selectedEquipements[id]
  } else {
    form.selectedEquipements[id] = 1
  }
}

const updateEquipementQty = (id, delta) => {
  const current = form.selectedEquipements[id] || 1
  const next = current + delta
  if (next >= 1) {
    form.selectedEquipements[id] = next
  }
}

const handleUpdateReservation = async () => {
  try {
    const payload = {
      salle_id: Number(form.salle_id),
      date_heure_debut: form.date_heure_debut,
      date_heure_fin: form.date_heure_fin,
      nombre_personnes: Number(form.nombre_personnes),
      status: form.status,
    }

    if (beneficiaryType.value === 'user') {
      payload.user_id = Number(form.user_id)
      payload.nom_client = null
      payload.telephone = null
    } else {
      payload.user_id = null
      payload.nom_client = form.nom_client
      payload.telephone = form.telephone
    }

    const equipementsArray = Object.entries(form.selectedEquipements).map(([id, quantity]) => ({
      id: Number(id),
      quantity: Number(quantity),
    }))

    payload.equipements = equipementsArray

    await adminReservationsStore.updateReservation(reservationId, payload)
    router.push({ name: 'admin-reservations' })
  } catch (error) {
    console.error('Erreur lors de la mise à jour de la réservation :', error)
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-7xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-reservations' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des réservations</span>
        </RouterLink>

        <h1 class="text-2xl font-bold text-gray-800">
          Modifier la réservation #{{ reservationId }}
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Ajustez les créneaux horaires, la salle, le statut ou les équipements inclus.
        </p>
      </div>

      <!-- CHARGEMENT INITIAL -->
      <div
        v-if="isFetching"
        class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-12 shadow-sm"
      >
        <Loader2 :size="32" class="animate-spin text-blue-600" />
        <p class="mt-3 text-sm text-gray-500">Chargement de la réservation...</p>
      </div>

      <div v-else>
        <!-- ALERTE D'ERREUR GLOBALE -->
        <div
          v-if="adminReservationsStore.errorMessage"
          class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
          <div class="flex items-center gap-2">
            <AlertCircle :size="18" class="text-red-500 shrink-0" />
            <span>{{ adminReservationsStore.errorMessage }}</span>
          </div>
        </div>

        <!-- FORMULAIRE -->
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
          <form @submit.prevent="handleUpdateReservation">
            <div class="space-y-8">
              <!-- 1. BÉNÉFICIAIRE -->
              <div>
                <h2 class="mb-4 text-base font-bold text-gray-900">
                  1. Bénéficiaire de la réservation
                </h2>

                <div class="mb-5 flex flex-wrap items-center gap-3">
                  <button
                    type="button"
                    class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    :class="beneficiaryType === 'user' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    @click="beneficiaryType = 'user'"
                  >
                    <User :size="16" />
                    <span>Utilisateur inscrit</span>
                  </button>

                  <button
                    type="button"
                    class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    :class="beneficiaryType === 'direct' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    @click="beneficiaryType = 'direct'"
                  >
                    <Phone :size="16" />
                    <span>Client direct / Externe</span>
                  </button>
                </div>

                <div v-if="beneficiaryType === 'user'" class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div>
                    <label for="user_id" class="mb-2 block text-sm font-medium text-gray-700">
                      Compte utilisateur <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="user_id"
                      v-model="form.user_id"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    >
                      <option value="" disabled>Sélectionnez un utilisateur</option>
                      <option
                        v-for="user in adminUsersStore.users"
                        :key="user.id"
                        :value="user.id"
                      >
                        {{ user.nom }} — {{ user.email }}
                      </option>
                    </select>
                  </div>
                </div>

                <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div>
                    <label for="nom_client" class="mb-2 block text-sm font-medium text-gray-700">
                      Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="nom_client"
                      v-model="form.nom_client"
                      type="text"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    />
                  </div>

                  <div>
                    <label for="telephone" class="mb-2 block text-sm font-medium text-gray-700">
                      Téléphone <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="telephone"
                      v-model="form.telephone"
                      type="tel"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    />
                  </div>
                </div>
              </div>

              <!-- SÉPARATEUR -->
              <div class="border-t border-gray-100"></div>

              <!-- 2. SALLE ET INVITES -->
              <div>
                <h2 class="mb-4 text-base font-bold text-gray-900">
                  2. Salle & Capacité
                </h2>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div>
                    <label for="salle_id" class="mb-2 block text-sm font-medium text-gray-700">
                      Salle <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="salle_id"
                      v-model="form.salle_id"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    >
                      <option
                        v-for="salle in adminSallesStore.salles"
                        :key="salle.id"
                        :value="salle.id"
                      >
                        {{ salle.nom }} (Max: {{ salle.capacite }} places)
                      </option>
                    </select>
                  </div>

                  <div>
                    <label for="nombre_personnes" class="mb-2 block text-sm font-medium text-gray-700">
                      Nombre de personnes <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="nombre_personnes"
                      v-model="form.nombre_personnes"
                      type="number"
                      min="1"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                      :class="{ 'border-red-500': isOverCapacity }"
                    />
                    <p v-if="isOverCapacity" class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600">
                      <AlertCircle :size="14" />
                      Attention : capacité max de cette salle = {{ selectedSalleObject?.capacite }} personnes.
                    </p>
                  </div>
                </div>
              </div>

              <!-- SÉPARATEUR -->
              <div class="border-t border-gray-100"></div>

              <!-- 3. DATE ET STATUT -->
              <div>
                <h2 class="mb-4 text-base font-bold text-gray-900">
                  3. Horaires & Statut
                </h2>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                  <div>
                    <label for="date_heure_debut" class="mb-2 block text-sm font-medium text-gray-700">
                      Début <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="date_heure_debut"
                      v-model="form.date_heure_debut"
                      type="datetime-local"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    />
                  </div>

                  <div>
                    <label for="date_heure_fin" class="mb-2 block text-sm font-medium text-gray-700">
                      Fin <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="date_heure_fin"
                      v-model="form.date_heure_fin"
                      type="datetime-local"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    />
                  </div>

                  <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-gray-700">
                      Statut de la réservation <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="status"
                      v-model="form.status"
                      required
                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    >
                      <option value="en_attente">En attente</option>
                      <option value="confirmee">Confirmée</option>
                      <option value="terminee">Terminée</option>
                      <option value="rejetee">Rejetée</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- SÉPARATEUR -->
              <div class="border-t border-gray-100"></div>

              <!-- 4. EQUIPEMENTS -->
              <div>
                <h2 class="mb-4 text-base font-bold text-gray-900">
                  4. Équipements inclus
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <div
                    v-for="equipement in adminEquipementsStore.equipements"
                    :key="equipement.id"
                    class="flex items-center justify-between rounded-xl border p-3.5 transition"
                    :class="form.selectedEquipements[equipement.id] ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200 bg-white'"
                  >
                    <div class="flex items-center gap-3">
                      <input
                        type="checkbox"
                        :checked="!!form.selectedEquipements[equipement.id]"
                        class="h-4 w-4 rounded text-blue-600 focus:ring-blue-500"
                        @change="toggleEquipement(equipement.id)"
                      />
                      <div>
                        <p class="text-sm font-semibold text-gray-900">{{ equipement.nom }}</p>
                        <p class="text-xs text-gray-400">Stock: {{ equipement.stock_total }}</p>
                      </div>
                    </div>

                    <div
                      v-if="form.selectedEquipements[equipement.id]"
                      class="flex items-center gap-2 rounded-lg bg-white border border-gray-200 px-2 py-1 shadow-sm"
                    >
                      <button
                        type="button"
                        class="h-6 w-6 rounded text-sm font-bold text-gray-500 hover:bg-gray-100"
                        @click="updateEquipementQty(equipement.id, -1)"
                      >
                        -
                      </button>
                      <span class="text-xs font-bold text-gray-800">
                        {{ form.selectedEquipements[equipement.id] }}
                      </span>
                      <button
                        type="button"
                        class="h-6 w-6 rounded text-sm font-bold text-gray-500 hover:bg-gray-100"
                        @click="updateEquipementQty(equipement.id, 1)"
                      >
                        +
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- BOUTONS D'ACTION -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
              <RouterLink
                :to="{ name: 'admin-reservations' }"
                class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
              >
                Annuler
              </RouterLink>

              <button
                type="submit"
                :disabled="adminReservationsStore.loading"
                class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
              >
                <Save :size="18" />
                <span>{{ adminReservationsStore.loading ? 'Enregistrement...' : 'Enregistrer les modifications' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
