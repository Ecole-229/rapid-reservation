<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminImagesStore } from '@/store/adminImages'
import { useAdminSallesStore } from '@/store/adminSalles'
import { ArrowLeft, Save, Loader2, UploadCloud, X, DoorOpen, Link as LinkIcon } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const adminImagesStore = useAdminImagesStore()
const adminSallesStore = useAdminSallesStore()

const imageId = route.params.id
const isFetching = ref(true)

const form = reactive({
  salle_id: '',
  nom: '',
  designation: '',
  path: '',
})

const existingImageUrl = ref(null)
const uploadType = ref('file')
const selectedFile = ref(null)
const previewUrl = ref(null)
const fileInput = ref(null)

onMounted(async () => {
  adminImagesStore.clearErrors()
  try {
    const [image] = await Promise.all([
      adminImagesStore.fetchImage(imageId),
      adminSallesStore.salles.length === 0 ? adminSallesStore.fetchSalles({ all: 'true' }) : Promise.resolve(),
    ])

    if (image) {
      form.salle_id = image.salle_id || ''
      form.nom = image.nom || ''
      form.designation = image.designation || ''
      form.path = image.path || ''
      existingImageUrl.value = image.url || null
    }
  } catch (error) {
    console.error('Erreur chargement image :', error)
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

const handleUpdateImage = async () => {
  try {
    const formData = new FormData()
    formData.append('salle_id', form.salle_id)
    formData.append('nom', form.nom)
    formData.append('designation', form.designation || '')

    if (selectedFile.value) {
      formData.append('image', selectedFile.value)
    } else if (form.path) {
      formData.append('path', form.path)
    }

    await adminImagesStore.updateImage(imageId, formData)
    router.push({ name: 'admin-galeries' })
  } catch (error) {
    console.error('Erreur lors de la mise à jour de l\'image :', error)
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-7xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-galeries' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la galerie</span>
        </RouterLink>

        <h1 class="text-2xl font-bold text-gray-800">
          Modifier la photo #{{ imageId }}
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Mettez à jour le titre, la salle associée ou remplacez le fichier image.
        </p>
      </div>

      <!-- CHARGEMENT INITIAL -->
      <div
        v-if="isFetching"
        class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-12 shadow-sm"
      >
        <Loader2 :size="32" class="animate-spin text-blue-600" />
        <p class="mt-3 text-sm text-gray-500">Chargement de la photo...</p>
      </div>

      <div v-else>
        <!-- ALERTE D'ERREUR GLOBALE -->
        <div
          v-if="adminImagesStore.errorMessage"
          class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
          {{ adminImagesStore.errorMessage }}
        </div>

        <!-- FORMULAIRE -->
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
          <form @submit.prevent="handleUpdateImage">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <!-- CHAMPS PRINCIPAUX (2 Colonnes) -->
              <div class="space-y-5 lg:col-span-2">
                <!-- Salle associée -->
                <div>
                  <label for="salle_id" class="mb-2 block text-sm font-medium text-gray-700">
                    Salle associée <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="salle_id"
                    v-model="form.salle_id"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminImagesStore.errors.salle_id }"
                  >
                    <option value="" disabled>Sélectionnez une salle</option>
                    <option
                      v-for="salle in adminSallesStore.salles"
                      :key="salle.id"
                      :value="salle.id"
                    >
                      {{ salle.nom }} ({{ salle.localisation }})
                    </option>
                  </select>
                  <p v-if="adminImagesStore.errors.salle_id" class="mt-1.5 text-xs text-red-600">
                    {{ adminImagesStore.errors.salle_id[0] }}
                  </p>
                </div>

                <!-- Nom / Titre -->
                <div>
                  <label for="nom" class="mb-2 block text-sm font-medium text-gray-700">
                    Titre / Nom de la photo <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="nom"
                    v-model="form.nom"
                    type="text"
                    required
                    placeholder="Nom de l'image"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminImagesStore.errors.nom }"
                  />
                  <p v-if="adminImagesStore.errors.nom" class="mt-1.5 text-xs text-red-600">
                    {{ adminImagesStore.errors.nom[0] }}
                  </p>
                </div>

                <!-- Désignation -->
                <div>
                  <label for="designation" class="mb-2 block text-sm font-medium text-gray-700">
                    Désignation ou type de vue
                  </label>
                  <input
                    id="designation"
                    v-model="form.designation"
                    type="text"
                    placeholder="Ex: Vue estrade, vue balcon..."
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminImagesStore.errors.designation }"
                  />
                  <p v-if="adminImagesStore.errors.designation" class="mt-1.5 text-xs text-red-600">
                    {{ adminImagesStore.errors.designation[0] }}
                  </p>
                </div>
              </div>

              <!-- PHOTO / REMPLACEMENT (1 Colonne) -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Visuel actuel & remplacement
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
                    Nouveau fichier sélectionné
                  </div>
                </div>

                <!-- Affichage image actuelle -->
                <div v-else-if="existingImageUrl" class="space-y-3">
                  <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                    <img :src="existingImageUrl" alt="Image actuelle" class="h-44 w-full object-cover" />
                  </div>
                  <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <UploadCloud :size="18" />
                    <span>Remplacer le fichier image</span>
                    <input
                      ref="fileInput"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleFileChange"
                    />
                  </label>
                </div>

                <!-- Pas d'image -->
                <div
                  v-else
                  class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center"
                >
                  <label class="cursor-pointer font-semibold text-blue-600 hover:underline">
                    Uploader une photo
                    <input
                      ref="fileInput"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleFileChange"
                    />
                  </label>
                </div>

                <p v-if="adminImagesStore.errors.image" class="mt-1.5 text-xs text-red-600">
                  {{ adminImagesStore.errors.image[0] }}
                </p>
              </div>
            </div>

            <!-- BOUTONS D'ACTION -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
              <RouterLink
                :to="{ name: 'admin-galeries' }"
                class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
              >
                Annuler
              </RouterLink>

              <button
                type="submit"
                :disabled="adminImagesStore.loading"
                class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
              >
                <Save :size="18" />
                <span>{{ adminImagesStore.loading ? 'Enregistrement...' : 'Enregistrer les modifications' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
