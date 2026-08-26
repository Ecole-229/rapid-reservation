<script setup>
import { reactive, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminUsersStore } from '@/store/adminUsers'
import { ArrowLeft, UserPlus } from 'lucide-vue-next'

const router = useRouter()
const adminUsersStore = useAdminUsersStore()

const form = reactive({
  nom: '',
  email: '',
  telephone: '',
  role: 'user',
  password: '',
})

onMounted(() => {
  adminUsersStore.clearErrors()
})

const handleCreateUser = async () => {
  try {
    await adminUsersStore.createUser({
      nom: form.nom,
      email: form.email,
      telephone: form.telephone || null,
      role: form.role,
      password: form.password,
    })

    // Redirection vers la liste des utilisateurs après création réussie
    router.push({ name: 'admin-users' })
  } catch (error) {
    console.error('Erreur lors de la création :', error)
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
          Créer un utilisateur
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Ajoutez un nouvel utilisateur à la plateforme.
        </p>
      </div>

      <!-- ALERTE D'ERREUR GLOBALE -->
      <div
        v-if="adminUsersStore.errorMessage"
        class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
      >
        {{ adminUsersStore.errorMessage }}
      </div>

      <!-- FORMULAIRE HORIZONTAL -->
      <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
        <form @submit.prevent="handleCreateUser">
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

            <!-- Mot de passe -->
            <div>
              <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                Mot de passe <span class="text-red-500">*</span>
              </label>

              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                minlength="6"
                placeholder="Mot de passe"
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
              <UserPlus :size="18" />
              <span>{{ adminUsersStore.loading ? 'Création en cours...' : 'Créer le compte' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppAdmin>
</template>
