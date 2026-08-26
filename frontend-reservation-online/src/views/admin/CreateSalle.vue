<script setup>
import { reactive, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminSallesStore } from '@/store/adminSalles'
import { ArrowLeft, Plus } from 'lucide-vue-next'

const router = useRouter()
const adminSallesStore = useAdminSallesStore()

const form = reactive({
  nom: '',
  capacite: 10,
  localisation: '',
  prix: 10000,
  status: 'disponible',
  description: '',
})

onMounted(() => {
  adminSallesStore.clearErrors()
})

const handleCreateSalle = async () => {
  try {
    await adminSallesStore.createSalle({
      nom: form.nom,
      capacite: Number(form.capacite),
      localisation: form.localisation,
      prix: Number(form.prix),
      status: form.status,
      description: form.description || null,
    })

    // Redirection vers la liste des salles après création
    router.push({ name: 'admin-salles' })
  } catch (error) {
    console.error('Erreur lors de la création de la salle :', error)
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-7xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-salles' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des salles</span>
        </RouterLink>

        <h1 class="text-2xl font-bold text-gray-800">
          Créer une salle
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Ajoutez une nouvelle salle de réunion ou de conférence à la plateforme.
        </p>
      </div>

      <!-- ALERTE D'ERREUR GLOBALE -->
      <div
        v-if="adminSallesStore.errorMessage"
        class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
      >
        {{ adminSallesStore.errorMessage }}
      </div>

      <!-- FORMULAIRE HORIZONTAL -->
      <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
        <form @submit.prevent="handleCreateSalle">
          <!-- 5 champs principaux alignés horizontalement sur grand écran -->
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Nom -->
            <div>
              <label for="nom" class="mb-2 block text-sm font-medium text-gray-700">
                Nom de la salle <span class="text-red-500">*</span>
              </label>

              <input
                id="nom"
                v-model="form.nom"
                type="text"
                required
                placeholder="Ex: Salle Alpha"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminSallesStore.errors.nom }"
              />

              <p v-if="adminSallesStore.errors.nom" class="mt-1.5 text-xs text-red-600">
                {{ adminSallesStore.errors.nom[0] }}
              </p>
            </div>

            <!-- Capacité -->
            <div>
              <label for="capacite" class="mb-2 block text-sm font-medium text-gray-700">
                Capacité (places) <span class="text-red-500">*</span>
              </label>

              <input
                id="capacite"
                v-model="form.capacite"
                type="number"
                min="1"
                required
                placeholder="Ex: 50"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminSallesStore.errors.capacite }"
              />

              <p v-if="adminSallesStore.errors.capacite" class="mt-1.5 text-xs text-red-600">
                {{ adminSallesStore.errors.capacite[0] }}
              </p>
            </div>

            <!-- Localisation -->
            <div>
              <label for="localisation" class="mb-2 block text-sm font-medium text-gray-700">
                Localisation <span class="text-red-500">*</span>
              </label>

              <input
                id="localisation"
                v-model="form.localisation"
                type="text"
                required
                placeholder="Ex: Étage 2, Bâtiment B"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminSallesStore.errors.localisation }"
              />

              <p v-if="adminSallesStore.errors.localisation" class="mt-1.5 text-xs text-red-600">
                {{ adminSallesStore.errors.localisation[0] }}
              </p>
            </div>

            <!-- Prix -->
            <div>
              <label for="prix" class="mb-2 block text-sm font-medium text-gray-700">
                Tarif (FCFA) <span class="text-red-500">*</span>
              </label>

              <input
                id="prix"
                v-model="form.prix"
                type="number"
                min="0"
                step="500"
                required
                placeholder="Ex: 25000"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminSallesStore.errors.prix }"
              />

              <p v-if="adminSallesStore.errors.prix" class="mt-1.5 text-xs text-red-600">
                {{ adminSallesStore.errors.prix[0] }}
              </p>
            </div>

            <!-- Statut -->
            <div>
              <label for="status" class="mb-2 block text-sm font-medium text-gray-700">
                Statut initial <span class="text-red-500">*</span>
              </label>

              <select
                id="status"
                v-model="form.status"
                required
                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminSallesStore.errors.status }"
              >
                <option value="disponible">Disponible</option>
                <option value="indisponible">Indisponible</option>
              </select>

              <p v-if="adminSallesStore.errors.status" class="mt-1.5 text-xs text-red-600">
                {{ adminSallesStore.errors.status[0] }}
              </p>
            </div>
          </div>

          <!-- Description -->
          <div class="mt-6">
            <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
              Description de la salle <span class="text-xs font-normal text-gray-400">(Équipements, caractéristiques, etc.)</span>
            </label>

            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              placeholder="Description détaillée de la salle..."
              class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3.5 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
              :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminSallesStore.errors.description }"
            ></textarea>

            <p v-if="adminSallesStore.errors.description" class="mt-1.5 text-xs text-red-600">
              {{ adminSallesStore.errors.description[0] }}
            </p>
          </div>

          <!-- BOUTONS D'ACTION -->
          <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
            <RouterLink
              :to="{ name: 'admin-salles' }"
              class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
            >
              Annuler
            </RouterLink>

            <button
              type="submit"
              :disabled="adminSallesStore.loading"
              class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <Plus :size="18" />
              <span>{{ adminSallesStore.loading ? 'Création en cours...' : 'Créer la salle' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppAdmin>
</template>
