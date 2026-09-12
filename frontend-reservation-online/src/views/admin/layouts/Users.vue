<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { useAdminUsersStore } from '@/store/adminUsers'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import {
  UserPlus,
  Eye,
  Pencil,
  Trash2,
  AlertTriangle,
  RefreshCw,
  Search,
} from 'lucide-vue-next'

const authStore = useAuthStore()
const adminUsersStore = useAdminUsersStore()

const search = ref('')
const role = ref('')
const sortOrder = ref('desc') // 'desc' ou 'asc'

// Modale de confirmation de suppression
const isDeleteModalOpen = ref(false)
const userToDelete = ref(null)
const isDeleting = ref(false)

const loadUsers = async () => {
  try {
    const params = {
      all: 'true',
    }
    if (role.value) {
      params.role = role.value
    }
    if (search.value) {
      params.search = search.value
    }
    await adminUsersStore.fetchUsers(params)
  } catch (error) {
    console.error('Erreur lors du chargement des utilisateurs:', error)
  }
}

onMounted(() => {
  loadUsers()
})

const handleSearch = () => {
  loadUsers()
}

const handleRoleChange = () => {
  loadUsers()
}

const filteredUsers = computed(() => {
  const result = [...adminUsersStore.users]

  result.sort((a, b) => {
    return sortOrder.value === 'desc' ? b.id - a.id : a.id - b.id
  })

  return result
})

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    }).format(date)
  } catch {
    return dateString
  }
}

const getRoleBadgeClass = (userRole) => {
  switch (userRole) {
    case 'admin':
      return 'bg-purple-100 text-purple-700'
    case 'responsable':
      return 'bg-blue-100 text-blue-700'
    default:
      return 'bg-slate-100 text-slate-700'
  }
}

const openDeleteModal = (user) => {
  userToDelete.value = user
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  userToDelete.value = null
}

const confirmDelete = async () => {
  if (!userToDelete.value) return
  isDeleting.value = true

  try {
    await adminUsersStore.deleteUser(userToDelete.value.id)
    closeDeleteModal()
  } catch (error) {
    console.error('Erreur lors de la suppression :', error)
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <AppAdmin>
    <div class="min-h-screen bg-[#F8FAFC]">
      <!-- EN-TÊTE DE PAGE -->
      <div class="mb-6 mt-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-slate-800">
            Gestion des Utilisateurs
          </h1>
          <p class="mt-1 text-xs text-slate-500">
            Consultez, filtrez et administrez l'ensemble des comptes de la plateforme.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95"
            @click="loadUsers"
          >
            <RefreshCw :size="14" :class="{ 'animate-spin': adminUsersStore.loading }" />
            <span>Actualiser</span>
          </button>

          <RouterLink
            :to="{ name: 'create-user' }"
            class="inline-flex items-center gap-2 rounded-xl border border-neutral-900 bg-neutral-900 px-4 py-2 text-xs font-medium uppercase tracking-widest text-white shadow-sm transition hover:bg-black active:scale-95"
          >
            <UserPlus :size="15" />
            <span>Ajouter un utilisateur</span>
          </RouterLink>
        </div>
      </div>

      <!-- FLASH MESSAGES -->
      <div
        v-if="adminUsersStore.successMessage"
        class="mb-4 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800"
      >
        <span>{{ adminUsersStore.successMessage }}</span>
        <button class="font-bold text-emerald-700 hover:text-emerald-900" @click="adminUsersStore.successMessage = null">
          ×
        </button>
      </div>

      <div
        v-if="adminUsersStore.errorMessage"
        class="mb-4 flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800"
      >
        <span>{{ adminUsersStore.errorMessage }}</span>
        <button class="font-bold text-rose-700 hover:text-rose-900" @click="adminUsersStore.errorMessage = null">
          ×
        </button>
      </div>

      <!-- BARRE DE RECHERCHE & FILTRES (STYLE DIGILAB) -->
      <div class="flex flex-wrap items-center justify-between gap-4 rounded-t-xl border border-b-0 border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-1 flex-wrap items-center gap-3">
          <div class="relative w-72">
            <input
              v-model="search"
              type="text"
              placeholder="Rechercher par nom, email..."
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 pl-9 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white"
              @input="handleSearch"
            />
            <Search :size="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          </div>

          <select
            v-model="role"
            class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm text-slate-600 outline-none transition focus:border-blue-500 focus:bg-white"
            @change="handleRoleChange"
          >
            <option value="">Tous les rôles</option>
            <option value="admin">Administrateur</option>
            <option value="responsable">Responsable</option>
            <option value="user">Utilisateur</option>
          </select>

          <span class="text-xs text-slate-400">
            {{ filteredUsers.length }} utilisateur(s) affiché(s)
          </span>
        </div>

        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 text-xs text-slate-500">
            <span>Tri :</span>
            <select
              v-model="sortOrder"
              class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-medium text-slate-700 outline-none focus:border-blue-500"
            >
              <option value="desc">Plus récents (ID Décroissant)</option>
              <option value="asc">Plus anciens (ID Croissant)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- TABLEAU DES UTILISATEURS (STYLE DIGILAB - 1 INFO PAR COLONNE STRICTEMENT) -->
      <div class="overflow-x-auto rounded-b-xl border border-slate-200 bg-white shadow-sm">
        <!-- CHARGEMENT -->
        <div v-if="adminUsersStore.loading" class="flex flex-col items-center justify-center py-20">
          <div class="h-8 w-8 animate-spin rounded-full border-3 border-slate-800 border-t-transparent"></div>
          <p class="mt-3 text-xs font-medium text-slate-500">Chargement des utilisateurs...</p>
        </div>

        <!-- LISTE VIDE -->
        <div
          v-else-if="filteredUsers.length === 0"
          class="flex flex-col items-center justify-center py-16 text-center"
        >
          <p class="font-semibold text-slate-800">Aucun utilisateur trouvé</p>
          <p class="mt-1 text-xs text-slate-400">
            Ajustez vos filtres ou enregistrez un nouvel utilisateur.
          </p>
        </div>

        <!-- TABLE -->
        <table v-else class="w-full text-left text-sm">
          <thead class="bg-slate-50 text-xs text-slate-400 uppercase tracking-wider">
            <tr>
              <th class="py-3 px-4 font-medium w-16">ID</th>
              <th class="py-3 px-4 font-medium">Nom</th>
              <th class="py-3 px-4 font-medium">Email</th>
              <th class="py-3 px-4 font-medium">Rôle</th>
              <th class="py-3 px-4 font-medium text-right w-28">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-600">
            <tr
              v-for="user in filteredUsers"
              :key="user.id"
              class="hover:bg-slate-50 transition"
            >
              <!-- 1. ID -->
              <td class="py-3.5 px-4 font-mono text-xs text-slate-400">
                #{{ user.id }}
              </td>

              <!-- 2. NOM -->
              <td class="py-3.5 px-4 font-semibold text-slate-800">
                {{ user.nom || user.name }}
              </td>

              <!-- 3. EMAIL -->
              <td class="py-3.5 px-4 text-xs text-slate-500">
                {{ user.email }}
              </td>

              <!-- 4. RÔLE -->
              <td class="py-3.5 px-4">
                <span
                  class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                  :class="getRoleBadgeClass(user.role)"
                >
                  • {{ user.role }}
                </span>
              </td>

              <!-- 5. ACTIONS -->
              <td class="py-3.5 px-4 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <RouterLink
                    :to="{ name: 'info-user', params: { id: user.id } }"
                    title="Voir les détails"
                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                  >
                    <Eye :size="13" />
                  </RouterLink>

                  <RouterLink
                    :to="{ name: 'update-user', params: { id: user.id } }"
                    title="Modifier"
                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                  >
                    <Pencil :size="13" />
                  </RouterLink>

                  <button
                    type="button"
                    title="Supprimer"
                    :disabled="authStore.user?.id === user.id"
                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-rose-500 transition hover:bg-rose-50 hover:border-rose-300 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                    @click="openDeleteModal(user)"
                  >
                    <Trash2 :size="13" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODALE DE CONFIRMATION DE SUPPRESSION -->
    <div
      v-if="isDeleteModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-center gap-4">
          <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
            <AlertTriangle :size="24" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">
              Confirmer la suppression
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              Êtes-vous sûr de vouloir supprimer l'utilisateur
              <strong class="text-gray-800">{{ userToDelete?.nom }}</strong> ({{ userToDelete?.email }}) ?
            </p>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            type="button"
            class="rounded-xl border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-widest text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900"
            @click="closeDeleteModal"
          >
            Annuler
          </button>

          <button
            type="button"
            :disabled="isDeleting"
            class="rounded-xl border border-rose-600 bg-rose-600 px-5 py-2.5 text-xs font-medium uppercase tracking-widest text-white transition hover:bg-rose-700 disabled:opacity-50"
            @click="confirmDelete"
          >
            <span v-if="isDeleting">Suppression...</span>
            <span v-else>Supprimer définitivement</span>
          </button>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
