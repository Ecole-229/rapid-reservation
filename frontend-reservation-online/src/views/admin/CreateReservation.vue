<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminReservationsStore } from '@/store/adminReservations'
import { useAdminSallesStore } from '@/store/adminSalles'
import { useAdminUsersStore } from '@/store/adminUsers'
import { useAdminEquipementsStore } from '@/store/adminEquipements'
import {
  ArrowLeft,
  Plus,
  Calendar,
  Clock,
  DoorOpen,
  User,
  Users as UsersIcon,
  Phone,
  Server,
  AlertCircle,
  Coins,
} from 'lucide-vue-next'

const router = useRouter()
const adminReservationsStore = useAdminReservationsStore()
const adminSallesStore = useAdminSallesStore()
const adminUsersStore = useAdminUsersStore()
const adminEquipementsStore = useAdminEquipementsStore()

// Mode de bénéficiaire : 'user' (compte existant) ou 'direct' (client physique)
const beneficiaryType = ref('user')

const form = reactive({
  user_id: '',
  nom_client: '',
  telephone: '',
  salle_id: '',
  date_heure_debut: '',
  date_heure_fin: '',
  nombre_personnes: 10,
  status: 'confirmee',
  selectedEquipements: {}, // { [equipementId]: quantity }
})

onMounted(async () => {
  adminReservationsStore.clearErrors()
  try {
    await Promise.all([
      adminSallesStore.fetchSalles({ all: 'true' }),
      adminUsersStore.fetchUsers({ all: 'true' }),
      adminEquipementsStore.fetchEquipements({ all: 'true' }),
    ])
  } catch (error) {
    console.error('Erreur chargement des données pour la réservation :', error)
  }
})

// Salle sélectionnée actuellement
const selectedSalleObject = computed(() => {
  if (!form.salle_id) return null
  return adminSallesStore.salles.find((s) => s.id === Number(form.salle_id)) || null
})

// Utilisateur sélectionné actuellement
const selectedUserObject = computed(() => {
  if (!form.user_id) return null
  return adminUsersStore.users.find((u) => u.id === Number(form.user_id)) || null
})

// Alerte de dépassement de capacité
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

const handleCreateReservation = async () => {
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
    } else {
      payload.nom_client = form.nom_client
      payload.telephone = form.telephone
    }

    // Préparer les équipements
    const equipementsArray = Object.entries(form.selectedEquipements).map(([id, quantity]) => ({
      id: Number(id),
      quantity: Number(quantity),
    }))

    if (equipementsArray.length > 0) {
      payload.equipements = equipementsArray
    }

    await adminReservationsStore.createReservation(payload)
    router.push({ name: 'admin-reservations' })
  } catch (error) {
    console.error('Erreur lors de la création de la réservation :', error)
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
          Créer une réservation
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Enregistrez et confirmez la réservation d'une salle pour un utilisateur ou un client direct.
        </p>
      </div>

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
        <form @submit.prevent="handleCreateReservation">
          <div class="space-y-8">
            <!-- 1. BÉNÉFICIAIRE / CLIENT -->
            <div>
              <h2 class="mb-4 text-base font-bold text-gray-900">
                1. Bénéficiaire de la réservation
              </h2>

              <!-- Bascule Type Client -->
              <div class="mb-5 flex flex-wrap items-center gap-3">
                <button
                  type="button"
                  class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                  :class="beneficiaryType === 'user' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                  @click="beneficiaryType = 'user'"
                >
                  <User :size="16" />
                  <span>Utilisateur inscrit (compte plateforme)</span>
                </button>

                <button
                  type="button"
                  class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                  :class="beneficiaryType === 'direct' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                  @click="beneficiaryType = 'direct'"
                >
                  <Phone :size="16" />
                  <span>Client physique / Externe (sans compte)</span>
                </button>
              </div>

              <!-- Choix Utilisateur -->
              <div v-if="beneficiaryType === 'user'" class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <label for="user_id" class="mb-2 block text-sm font-medium text-gray-700">
                    Sélectionner l'utilisateur <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="user_id"
                    v-model="form.user_id"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminReservationsStore.errors.user_id }"
                  >
                    <option value="" disabled>Choisir un compte utilisateur</option>
                    <option
                      v-for="user in adminUsersStore.users"
                      :key="user.id"
                      :value="user.id"
                    >
                      {{ user.nom }} — {{ user.email }} ({{ user.telephone || 'Sans tél' }})
                    </option>
                  </select>
                  <p v-if="adminReservationsStore.errors.user_id" class="mt-1.5 text-xs text-red-600">
                    {{ adminReservationsStore.errors.user_id[0] }}
                  </p>
                </div>

                <!-- Aperçu infos utilisateur -->
                <div v-if="selectedUserObject" class="flex items-center gap-4 rounded-xl border border-blue-100 bg-blue-50/50 p-4">
                  <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 font-bold text-white">
                    {{ selectedUserObject.nom.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900">{{ selectedUserObject.nom }}</p>
                    <p class="text-xs text-gray-500">{{ selectedUserObject.email }}</p>
                    <p class="text-xs text-blue-600 font-medium">Tél: {{ selectedUserObject.telephone || 'Non renseigné' }}</p>
                  </div>
                </div>
              </div>

              <!-- Saisie Client Direct -->
              <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <label for="nom_client" class="mb-2 block text-sm font-medium text-gray-700">
                    Nom complet du client <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="nom_client"
                    v-model="form.nom_client"
                    type="text"
                    required
                    placeholder="Ex: Jean Dupont"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminReservationsStore.errors.nom_client }"
                  />
                  <p v-if="adminReservationsStore.errors.nom_client" class="mt-1.5 text-xs text-red-600">
                    {{ adminReservationsStore.errors.nom_client[0] }}
                  </p>
                </div>

                <div>
                  <label for="telephone" class="mb-2 block text-sm font-medium text-gray-700">
                    Numéro de téléphone <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="telephone"
                    v-model="form.telephone"
                    type="tel"
                    required
                    placeholder="Ex: +229 97 00 00 00"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminReservationsStore.errors.telephone }"
                  />
                  <p v-if="adminReservationsStore.errors.telephone" class="mt-1.5 text-xs text-red-600">
                    {{ adminReservationsStore.errors.telephone[0] }}
                  </p>
                </div>
              </div>
            </div>

            <!-- SÉPARATEUR -->
            <div class="border-t border-gray-100"></div>

            <!-- 2. SALLE ET CAPACITÉ -->
            <div>
              <h2 class="mb-4 text-base font-bold text-gray-900">
                2. Salle & Capacité
              </h2>

              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Choix Salle -->
                <div>
                  <label for="salle_id" class="mb-2 block text-sm font-medium text-gray-700">
                    Salle à réserver <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="salle_id"
                    v-model="form.salle_id"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminReservationsStore.errors.salle_id }"
                  >
                    <option value="" disabled>Sélectionnez une salle</option>
                    <option
                      v-for="salle in adminSallesStore.salles"
                      :key="salle.id"
                      :value="salle.id"
                    >
                      {{ salle.nom }} (Max: {{ salle.capacite }} places — {{ salle.localisation }})
                    </option>
                  </select>
                  <p v-if="adminReservationsStore.errors.salle_id" class="mt-1.5 text-xs text-red-600">
                    {{ adminReservationsStore.errors.salle_id[0] }}
                  </p>
                </div>

                <!-- Nombre de Personnes -->
                <div>
                  <label for="nombre_personnes" class="mb-2 block text-sm font-medium text-gray-700">
                    Nombre de personnes attendues <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="nombre_personnes"
                    v-model="form.nombre_personnes"
                    type="number"
                    min="1"
                    required
                    placeholder="Ex: 25"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': isOverCapacity || adminReservationsStore.errors.nombre_personnes }"
                  />

                  <!-- Message d'alerte si dépassement -->
                  <p v-if="isOverCapacity" class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600">
                    <AlertCircle :size="14" />
                    Attention : la capacité max de cette salle est de {{ selectedSalleObject?.capacite }} personnes.
                  </p>
                  <p v-else-if="adminReservationsStore.errors.nombre_personnes" class="mt-1.5 text-xs text-red-600">
                    {{ adminReservationsStore.errors.nombre_personnes[0] }}
                  </p>
                </div>
              </div>
            </div>

            <!-- SÉPARATEUR -->
            <div class="border-t border-gray-100"></div>

            <!-- 3. DATE ET CRÉNEAU HORAIRE -->
            <div>
              <h2 class="mb-4 text-base font-bold text-gray-900">
                3. Date & Créneau Horaire
              </h2>

              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Date début -->
                <div>
                  <label for="date_heure_debut" class="mb-2 block text-sm font-medium text-gray-700">
                    Date et heure de début <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="date_heure_debut"
                    v-model="form.date_heure_debut"
                    type="datetime-local"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminReservationsStore.errors.date_heure_debut }"
                  />
                  <p v-if="adminReservationsStore.errors.date_heure_debut" class="mt-1.5 text-xs text-red-600">
                    {{ adminReservationsStore.errors.date_heure_debut[0] }}
                  </p>
                </div>

                <!-- Date fin -->
                <div>
                  <label for="date_heure_fin" class="mb-2 block text-sm font-medium text-gray-700">
                    Date et heure de fin <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="date_heure_fin"
                    v-model="form.date_heure_fin"
                    type="datetime-local"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminReservationsStore.errors.date_heure_fin }"
                  />
                  <p v-if="adminReservationsStore.errors.date_heure_fin" class="mt-1.5 text-xs text-red-600">
                    {{ adminReservationsStore.errors.date_heure_fin[0] }}
                  </p>
                </div>
              </div>

              <!-- Statut initial -->
              <div class="mt-5">
                <label for="status" class="mb-2 block text-sm font-medium text-gray-700">
                  Statut initial de la réservation <span class="text-red-500">*</span>
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  required
                  class="w-full sm:w-1/2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                >
                  <option value="confirmee">Confirmée immédiatement</option>
                  <option value="en_attente">En attente de confirmation</option>
                </select>
              </div>
            </div>

            <!-- SÉPARATEUR -->
            <div class="border-t border-gray-100"></div>

            <!-- 4. ÉQUIPEMENTS ADDITIONNELS -->
            <div>
              <h2 class="mb-4 text-base font-bold text-gray-900">
                4. Équipements inclus (Facultatif)
              </h2>

              <div
                v-if="adminEquipementsStore.equipements.length === 0"
                class="rounded-xl bg-gray-50 p-4 text-xs text-gray-500"
              >
                Aucun équipement disponible dans le catalogue pour le moment.
              </div>

              <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
                      <p class="text-xs text-gray-400">Dispo: {{ equipement.stock_total }}</p>
                    </div>
                  </div>

                  <!-- Contrôle quantité si sélectionné -->
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
              <Plus :size="18" />
              <span>{{ adminReservationsStore.loading ? 'Enregistrement...' : 'Enregistrer la réservation' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppAdmin>
</template>
