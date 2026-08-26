<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminUsersStore } from '@/store/adminUsers'
import { ArrowLeft, Save, Loader2 } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const adminUsersStore = useAdminUsersStore()

const userId = route.params.id
const isFetching = ref(true)

const form = reactive({
  nom: '',
  email: '',
  telephone: '',
  role: 'user',
  password: '',
})

onMounted(async () => {
  adminUsersStore.clearErrors()
  try {
    const user = await adminUsersStore.fetchUser(userId)
    if (user) {
      form.nom = user.nom || ''
      form.email = user.email || ''
      form.telephone = user.telephone || ''
      form.role = user.role || 'user'
    }
  } catch (error) {
    console.error('Erreur lors du chargement de l\'utilisateur :', error)
  } finally {
    isFetching.value = false
  }
})

const handleUpdateUser = async () => {
  try {
    const payload = {
      nom: form.nom,
      email: form.email,
      telephone: form.telephone || null,
      role: form.role,
    }

    // Le mot de passe n'est envoyé que s'il est renseigné
    if (form.password && form.password.trim() !== '') {
      payload.password = form.password
    }

    await adminUsersStore.updateUser(userId, payload)

    // Redirection vers la liste
    router.push({ name: 'admin-users' })
  } catch (error) {
    console.error('Erreur lors de la mise à jour :', error)
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-7xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-users' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des utilisateurs</span>
        </RouterLink>

        <h1 class="text-2xl font-bold text-gray-800">
          Modifier l'utilisateur #{{ userId }}
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Mettez à jour les informations de l'utilisateur sur la plateforme.
        </p>
      </div>

      <!-- CHARGEMENT INITIAL -->
      <div
        v-if="isFetching"
        class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-12 shadow-sm"
      >
        <Loader2 :size="32" class="animate-spin text-blue-600" />
        <p class="mt-3 text-sm text-gray-500">Chargement des données de l'utilisateur...</p>
      </div>

      <div v-else>
        <!-- ALERTE D'ERREUR GLOBALE -->
        <div
          v-if="adminUsersStore.errorMessage"
          class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
          {{ adminUsersStore.errorMessage }}
        </div>

        <!-- FORMULAIRE HORIZONTAL -->
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
          <form @submit.prevent="handleUpdateUser">
            <!-- 5 champs alignés horizontalement sur grand écran -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
              <!-- Nom -->
              <div>
                <label for="nom" class="mb-2 block text-sm font-medium text-gray-700">
                  Nom <span class="text-red-500">*</span>
                </label>

                <input
                  id="nom"
                  v-model="form.nom"
                  type="text"
                  required
                  placeholder="Nom"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminUsersStore.errors.nom }"
                />

                <p v-if="adminUsersStore.errors.nom" class="mt-1.5 text-xs text-red-600">
                  {{ adminUsersStore.errors.nom[0] }}
                </p>
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                  Email <span class="text-red-500">*</span>
                </label>

                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="Email"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminUsersStore.errors.email }"
                />

                <p v-if="adminUsersStore.errors.email" class="mt-1.5 text-xs text-red-600">
                  {{ adminUsersStore.errors.email[0] }}
                </p>
              </div>

              <!-- Téléphone -->
              <div>
                <label for="phone" class="mb-2 block text-sm font-medium text-gray-700">
                  Téléphone
                </label>

                <input
                  id="phone"
                  v-model="form.telephone"
                  type="tel"
                  placeholder="Téléphone"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminUsersStore.errors.telephone }"
                />

                <p v-if="adminUsersStore.errors.telephone" class="mt-1.5 text-xs text-red-600">
                  {{ adminUsersStore.errors.telephone[0] }}
                </p>
              </div>

              <!-- Rôle -->
              <div>
                <label for="role" class="mb-2 block text-sm font-medium text-gray-700">
                  Rôle <span class="text-red-500">*</span>
                </label>

                <select
                  id="role"
                  v-model="form.role"
                  required
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': adminUsersStore.errors.role }"
                >
                  <option value="user">Utilisateur</option>
                  <option value="responsable">Responsable</option>
                  <option value="admin">Administrateur</option>
                </select>

                <p v-if="adminUsersStore.errors.role" class="mt-1.5 text-xs text-red-600">
                  {{ adminUsersStore.errors.role[0] }}
                </p>
              </div>

              <!-- Nouveau mot de passe (optionnel) -->
              <div>
                <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                  Mot de passe <span class="text-xs font-normal text-gray-400">(optionnel)</span>
                </label>

                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  placeholder="Laisser vide = inchangé"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                  :class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-100':
                      adminUsersStore.errors.password || adminUsersStore.errors.mot_de_passe,
                  }"
                />

                <p
                  v-if="adminUsersStore.errors.password || adminUsersStore.errors.mot_de_passe"
                  class="mt-1.5 text-xs text-red-600"
                >
                  {{ (adminUsersStore.errors.password || adminUsersStore.errors.mot_de_passe)[0] }}
                </p>
              </div>
            </div>

            <!-- BOUTONS D'ACTION -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
              <RouterLink
                :to="{ name: 'admin-users' }"
                class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
              >
                Annuler
              </RouterLink>

              <button
                type="submit"
                :disabled="adminUsersStore.loading"
                class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
              >
                <Save :size="18" />
                <span>{{ adminUsersStore.loading ? 'Enregistrement...' : 'Enregistrer' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
