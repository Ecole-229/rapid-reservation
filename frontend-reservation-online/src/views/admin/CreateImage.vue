<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminImagesStore } from '@/store/adminImages'
import { useAdminSallesStore } from '@/store/adminSalles'
import { ArrowLeft, Plus, UploadCloud, X, DoorOpen, Link as LinkIcon } from 'lucide-vue-next'

const router = useRouter()
const adminImagesStore = useAdminImagesStore()
const adminSallesStore = useAdminSallesStore()

const form = reactive({
  salle_id: '',
  nom: '',
  designation: '',
  path: '',
})

const uploadType = ref('file') // 'file' ou 'url'
const selectedFile = ref(null)
const previewUrl = ref(null)
const fileInput = ref(null)

onMounted(async () => {
  adminImagesStore.clearErrors()
  if (adminSallesStore.salles.length === 0) {
    try {
      await adminSallesStore.fetchSalles({ all: 'true' })
    } catch (e) {
      console.error('Erreur chargement des salles :', e)
    }
  }
})

const handleFileChange = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    selectedFile.value = file
    previewUrl.value = URL.createObjectURL(file)
    if (!form.nom) {
      // Pré-remplir le nom avec le nom du fichier sans extension
      form.nom = file.name.replace(/\.[^/.]+$/, '')
    }
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

const handleCreateImage = async () => {
  try {
    const formData = new FormData()
    formData.append('salle_id', form.salle_id)
    formData.append('nom', form.nom)
    if (form.designation) {
      formData.append('designation', form.designation)
    }

    if (uploadType.value === 'file' && selectedFile.value) {
      formData.append('image', selectedFile.value)
    } else if (uploadType.value === 'url' && form.path) {
      formData.append('path', form.path)
    }

    await adminImagesStore.createImage(formData)
    router.push({ name: 'admin-galeries' })
  } catch (error) {
    console.error('Erreur lors de l\'ajout de l\'image :', error)
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
          Ajouter une photo à la galerie
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Associez une nouvelle prise de vue ou un plan à une salle de réunion.
        </p>
      </div>

      <!-- ALERTE D'ERREUR GLOBALE -->
      <div
        v-if="adminImagesStore.errorMessage"
        class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
      >
        {{ adminImagesStore.errorMessage }}
      </div>

      <!-- FORMULAIRE -->
      <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
        <form @submit.prevent="handleCreateImage">
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- CHAMPS PRINCIPAUX (2 Colonnes) -->
            <div class="space-y-5 lg:col-span-2">
              <!-- Sélection Salle -->
              <div>
                <label for="salle_id" class="mb-2 block text-sm font-medium text-gray-700">
                  Salle associée <span class="text-red-500">*</span>
                </label>
                <div class="relative">
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
                </div>
                <p v-if="adminImagesStore.errors.salle_id" class="mt-1.5 text-xs text-red-600">
                  {{ adminImagesStore.errors.salle_id[0] }}
                </p>
              </div>

              <!-- Nom de la photo -->
              <div>
                <label for="nom" class="mb-2 block text-sm font-medium text-gray-700">
                  Titre / Nom de la photo <span class="text-red-500">*</span>
                </label>
                <input
                  id="nom"
                  v-model="form.nom"
                  type="text"
                  required
                  placeholder="Ex: Vue panoramique baie vitrée"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminImagesStore.errors.nom }"
                />
                <p v-if="adminImagesStore.errors.nom" class="mt-1.5 text-xs text-red-600">
                  {{ adminImagesStore.errors.nom[0] }}
                </p>
              </div>

              <!-- Désignation / Légende -->
              <div>
                <label for="designation" class="mb-2 block text-sm font-medium text-gray-700">
                  Désignation ou type de vue <span class="text-xs font-normal text-gray-400">(Facultatif)</span>
                </label>
                <input
                  id="designation"
                  v-model="form.designation"
                  type="text"
                  placeholder="Ex: Vue intérieure, Angle scène, Équipements audiovisuels..."
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminImagesStore.errors.designation }"
                />
                <p v-if="adminImagesStore.errors.designation" class="mt-1.5 text-xs text-red-600">
                  {{ adminImagesStore.errors.designation[0] }}
                </p>
              </div>

              <!-- Onglets Type d'upload -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Source de l'image <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                  <button
                    type="button"
                    class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    :class="uploadType === 'file' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    @click="uploadType = 'file'"
                  >
                    <UploadCloud :size="16" />
                    <span>Upload fichier (depuis l'appareil)</span>
                  </button>

                  <button
                    type="button"
                    class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    :class="uploadType === 'url' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    @click="uploadType = 'url'"
                  >
                    <LinkIcon :size="16" />
                    <span>Lien URL externe</span>
                  </button>
                </div>
              </div>

              <!-- URL si mode URL -->
              <div v-if="uploadType === 'url'">
                <label for="path" class="mb-2 block text-sm font-medium text-gray-700">
                  URL de l'image
                </label>
                <input
                  id="path"
                  v-model="form.path"
                  type="url"
                  placeholder="https://images.unsplash.com/..."
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                />
              </div>
            </div>

            <!-- ZONE D'APERÇU / UPLOAD FICHIER (1 Colonne) -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Aperçu du visuel
              </label>

              <!-- Mode Fichier : Pas de fichier sélectionné -->
              <div
                v-if="uploadType === 'file' && !previewUrl"
                class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-8 text-center transition hover:border-blue-400 hover:bg-blue-50/20"
              >
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                  <UploadCloud :size="28" />
                </div>
                <p class="mt-3 text-sm font-medium text-gray-700">
                  Déposez une photo ici ou
                  <label class="cursor-pointer font-semibold text-blue-600 hover:underline">
                    sélectionnez
                    <input
                      ref="fileInput"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleFileChange"
                    />
                  </label>
                </p>
                <p class="mt-1 text-xs text-gray-400">JPEG, PNG, WEBP, SVG (max 10 Mo)</p>
              </div>

              <!-- Mode Fichier : Aperçu -->
              <div
                v-else-if="uploadType === 'file' && previewUrl"
                class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 shadow-sm"
              >
                <img
                  :src="previewUrl"
                  alt="Aperçu photo"
                  class="h-56 w-full object-cover"
                />
                <button
                  type="button"
                  class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-md transition hover:bg-red-700"
                  @click="removeFile"
                >
                  <X :size="16" />
                </button>
              </div>

              <!-- Mode URL : Aperçu live si valide -->
              <div
                v-else-if="uploadType === 'url'"
                class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50"
              >
                <img
                  v-if="form.path"
                  :src="form.path"
                  alt="Aperçu URL"
                  class="h-56 w-full object-cover"
                />
                <div
                  v-else
                  class="flex h-56 w-full items-center justify-center text-xs text-gray-400"
                >
                  Saisissez une URL pour voir l'aperçu
                </div>
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
              <Plus :size="18" />
              <span>{{ adminImagesStore.loading ? 'Ajout en cours...' : 'Ajouter à la galerie' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppAdmin>
</template>
