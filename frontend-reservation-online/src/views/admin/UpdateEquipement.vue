<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminEquipementsStore } from '@/store/adminEquipements'
import { ArrowLeft, Save, Loader2, UploadCloud, X } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const adminEquipementsStore = useAdminEquipementsStore()

const equipementId = route.params.id
const isFetching = ref(true)

const form = reactive({
  nom: '',
  stock_total: 0,
  status: 'disponible',
  description: '',
})

const existingImageUrl = ref(null)
const selectedFile = ref(null)
const previewUrl = ref(null)
const fileInput = ref(null)

onMounted(async () => {
  adminEquipementsStore.clearErrors()
  try {
    const equipement = await adminEquipementsStore.fetchEquipement(equipementId)
    if (equipement) {
      form.nom = equipement.nom || ''
      form.stock_total = Number(equipement.stock_total) || 0
      form.status = equipement.status || 'disponible'
      form.description = equipement.description || ''
      existingImageUrl.value = equipement.image_url || null
    }
  } catch (error) {
    console.error('Erreur chargement équipement :', error)
  } finally {
    isFetching.value = false
  }
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

const handleUpdateEquipement = async () => {
  try {
    const formData = new FormData()
    formData.append('nom', form.nom)
    formData.append('stock_total', form.stock_total)
    formData.append('status', form.status)
    formData.append('description', form.description || '')

    if (selectedFile.value) {
      formData.append('image', selectedFile.value)
    }

    await adminEquipementsStore.updateEquipement(equipementId, formData)
    router.push({ name: 'admin-equipments' })
  } catch (error) {
    console.error('Erreur lors de la mise à jour de l\'équipement :', error)
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
          Modifier l'équipement #{{ equipementId }}
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Mettez à jour les informations, le stock et l'illustration de l'équipement.
        </p>
      </div>

      <!-- CHARGEMENT INITIAL -->
      <div
        v-if="isFetching"
        class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-12 shadow-sm"
      >
        <Loader2 :size="32" class="animate-spin text-blue-600" />
        <p class="mt-3 text-sm text-gray-500">Chargement des données de l'équipement...</p>
      </div>

      <div v-else>
        <!-- ALERTE D'ERREUR GLOBALE -->
        <div
          v-if="adminEquipementsStore.errorMessage"
          class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
          {{ adminEquipementsStore.errorMessage }}
        </div>

        <!-- FORMULAIRE -->
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
          <form @submit.prevent="handleUpdateEquipement">
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
                      placeholder="Nom de l'équipement"
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
                      Stock disponible <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="stock_total"
                      v-model="form.stock_total"
                      type="number"
                      min="0"
                      required
                      placeholder="Quantité"
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
                    <option value="disponible">Disponible</option>
                    <option value="indisponible">Indisponible</option>
                  </select>
                  <p v-if="adminEquipementsStore.errors.status" class="mt-1.5 text-xs text-red-600">
                    {{ adminEquipementsStore.errors.status[0] }}
                  </p>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                    Description de l'équipement
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    placeholder="Détails techniques, câblage, état..."
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3.5 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminEquipementsStore.errors.description }"
                  ></textarea>
                  <p v-if="adminEquipementsStore.errors.description" class="mt-1.5 text-xs text-red-600">
                    {{ adminEquipementsStore.errors.description[0] }}
                  </p>
                </div>
              </div>

              <!-- PHOTO / REMPLACEMENT -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Photo de l'équipement
                </label>

                <!-- Prévisualisation du nouveau fichier -->
                <div v-if="previewUrl" class="relative overflow-hidden rounded-2xl border border-blue-200 bg-blue-50">
                  <img :src="previewUrl" alt="Nouvelle image" class="h-48 w-full object-cover" />
                  <button
                    type="button"
                    class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-md transition hover:bg-red-700"
                    @click="removeFile"
                  >
                    <X :size="16" />
                  </button>
                  <div class="p-2 text-center text-xs font-semibold text-blue-700">
                    Nouvelle image sélectionnée
                  </div>
                </div>

                <!-- Affichage image actuelle -->
                <div v-else-if="existingImageUrl" class="space-y-3">
                  <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                    <img :src="existingImageUrl" alt="Image actuelle" class="h-44 w-full object-cover" />
                  </div>
                  <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <UploadCloud :size="18" />
                    <span>Remplacer l'image</span>
                    <input
                      ref="fileInput"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleFileChange"
                    />
                  </label>
                </div>

                <!-- Pas d'image encore -->
                <div
                  v-else
                  class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center transition hover:border-blue-400"
                >
                  <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                    <UploadCloud :size="24" />
                  </div>
                  <p class="mt-3 text-sm font-medium text-gray-700">
                    <label class="cursor-pointer font-semibold text-blue-600 hover:underline">
                      Ajouter une photo
                      <input
                        ref="fileInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleFileChange"
                      />
                    </label>
                  </p>
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
                <Save :size="18" />
                <span>{{ adminEquipementsStore.loading ? 'Enregistrement...' : 'Enregistrer les modifications' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
