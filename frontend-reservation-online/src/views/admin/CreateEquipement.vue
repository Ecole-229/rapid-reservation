<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminEquipementsStore } from '@/store/adminEquipements'
import { ArrowLeft, Plus, UploadCloud, X, Image as ImageIcon } from 'lucide-vue-next'

const router = useRouter()
const adminEquipementsStore = useAdminEquipementsStore()

const form = reactive({
  nom: '',
  stock_total: 10,
  status: 'disponible',
  description: '',
})

const selectedFile = ref(null)
const previewUrl = ref(null)
const fileInput = ref(null)

onMounted(() => {
  adminEquipementsStore.clearErrors()
})

const handleFileChange = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    selectedFile.value = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const removeFile = () => {
  selectedFile.value = null
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
    previewUrl.value = null
  }
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const handleCreateEquipement = async () => {
  try {
    const formData = new FormData()
    formData.append('nom', form.nom)
    formData.append('stock_total', form.stock_total)
    formData.append('status', form.status)
    if (form.description) {
      formData.append('description', form.description)
    }
    if (selectedFile.value) {
      formData.append('image', selectedFile.value)
    }

    await adminEquipementsStore.createEquipement(formData)
    router.push({ name: 'admin-equipments' })
  } catch (error) {
    console.error('Erreur lors de la création de l\'équipement :', error)
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-7xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-equipments' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des équipements</span>
        </RouterLink>

        <h1 class="text-2xl font-bold text-gray-800">
          Créer un équipement
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Ajoutez un nouvel équipement (projecteur, sonorisation, micro, tableau...) au catalogue.
        </p>
      </div>

      <!-- ALERTE D'ERREUR GLOBALE -->
      <div
        v-if="adminEquipementsStore.errorMessage"
        class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
      >
        {{ adminEquipementsStore.errorMessage }}
      </div>

      <!-- FORMULAIRE -->
      <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
        <form @submit.prevent="handleCreateEquipement">
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- CHAMPS PRINCIPAUX (2 Colonnes) -->
            <div class="space-y-5 lg:col-span-2">
              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Nom -->
                <div>
                  <label for="nom" class="mb-2 block text-sm font-medium text-gray-700">
                    Nom de l'équipement <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="nom"
                    v-model="form.nom"
                    type="text"
                    required
                    placeholder="Ex: Vidéoprojecteur 4K Laser"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminEquipementsStore.errors.nom }"
                  />
                  <p v-if="adminEquipementsStore.errors.nom" class="mt-1.5 text-xs text-red-600">
                    {{ adminEquipementsStore.errors.nom[0] }}
                  </p>
                </div>

                <!-- Stock Total -->
                <div>
                  <label for="stock_total" class="mb-2 block text-sm font-medium text-gray-700">
                    Stock initial disponible <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="stock_total"
                    v-model="form.stock_total"
                    type="number"
                    min="0"
                    required
                    placeholder="Ex: 10"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminEquipementsStore.errors.stock_total }"
                  />
                  <p v-if="adminEquipementsStore.errors.stock_total" class="mt-1.5 text-xs text-red-600">
                    {{ adminEquipementsStore.errors.stock_total[0] }}
                  </p>
                </div>
              </div>

              <!-- Statut -->
              <div>
                <label for="status" class="mb-2 block text-sm font-medium text-gray-700">
                  Statut <span class="text-red-500">*</span>
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  required
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminEquipementsStore.errors.status }"
                >
                  <option value="disponible">Disponible (prêt à la réservation)</option>
                  <option value="indisponible">Indisponible (maintenance / hors service)</option>
                </select>
                <p v-if="adminEquipementsStore.errors.status" class="mt-1.5 text-xs text-red-600">
                  {{ adminEquipementsStore.errors.status[0] }}
                </p>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                  Description technique & caractéristiques
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  placeholder="Marque, connectique (HDMI, USB-C), puissance, spécifications..."
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3.5 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminEquipementsStore.errors.description }"
                ></textarea>
                <p v-if="adminEquipementsStore.errors.description" class="mt-1.5 text-xs text-red-600">
                  {{ adminEquipementsStore.errors.description[0] }}
                </p>
              </div>
            </div>

            <!-- UPLOAD IMAGE (1 Colonne) -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Photo de l'équipement
              </label>

              <div
                v-if="!previewUrl"
                class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center transition hover:border-blue-400 hover:bg-blue-50/20"
              >
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                  <UploadCloud :size="24" />
                </div>
                <p class="mt-3 text-sm font-medium text-gray-700">
                  Glissez une photo ou
                  <label class="cursor-pointer font-semibold text-blue-600 hover:underline">
                    parcourez
                    <input
                      ref="fileInput"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleFileChange"
                    />
                  </label>
                </p>
                <p class="mt-1 text-xs text-gray-400">PNG, JPG, WEBP jusqu'à 10 Mo</p>
              </div>

              <!-- APERÇU DE L'IMAGE -->
              <div v-else class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                <img
                  :src="previewUrl"
                  alt="Aperçu équipement"
                  class="h-48 w-full object-cover"
                />
                <button
                  type="button"
                  class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-md transition hover:bg-red-700"
                  @click="removeFile"
                >
                  <X :size="16" />
                </button>
              </div>

              <p v-if="adminEquipementsStore.errors.image" class="mt-1.5 text-xs text-red-600">
                {{ adminEquipementsStore.errors.image[0] }}
              </p>
            </div>
          </div>

          <!-- BOUTONS D'ACTION -->
          <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
            <RouterLink
              :to="{ name: 'admin-equipments' }"
              class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
            >
              Annuler
            </RouterLink>

            <button
              type="submit"
              :disabled="adminEquipementsStore.loading"
              class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <Plus :size="18" />
              <span>{{ adminEquipementsStore.loading ? 'Création en cours...' : 'Créer l\'équipement' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppAdmin>
</template>
