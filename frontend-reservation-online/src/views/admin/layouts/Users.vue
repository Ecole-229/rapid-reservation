<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { useAdminUsersStore } from '@/store/adminUsers'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import UsersFilters from '@/components/admin/UsersFilters.vue'
import { ref, computed, onMounted } from 'vue'
import {
  UserPlus,
  Eye,
  Pencil,
  Trash2,
  AlertTriangle,
  Mail,
  Calendar,
  Phone,
  RefreshCw,
} from 'lucide-vue-next'

const authStore = useAuthStore()
const adminUsersStore = useAdminUsersStore()

const search = ref('')
const role = ref('')
const descending = ref(true)

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

const handleSearch = (value) => {
  search.value = value
  loadUsers()
}

const handleRoleChange = (value) => {
  role.value = value
  loadUsers()
}

const handleSortChange = (value) => {
  descending.value = value
}

const filteredUsers = computed(() => {
  const result = [...adminUsersStore.users]

  result.sort((a, b) => {
    return descending.value ? b.id - a.id : a.id - b.id
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
      return 'bg-purple-50 text-purple-700 border border-purple-200'
    case 'responsable':
      return 'bg-blue-50 text-blue-700 border border-blue-200'
    default:
      return 'bg-slate-50 text-slate-700 border border-slate-200'
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
      <!-- TITRE & ACTIONS -->
      <div class="mb-6 mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-[30px] font-bold tracking-[-0.8px] text-[#0F172A]">
            Gestion des Utilisateurs
          </h1>
          <p class="mt-1 text-sm text-[#64748B]">
            Consultez, filtrez et gérez l'ensemble des comptes (Administrateurs, Responsables et Utilisateurs).
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 active:scale-95"
            @click="loadUsers"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': adminUsersStore.loading }" />
            <span>Actualiser</span>
          </button>

          <RouterLink
            :to="{ name: 'create-user' }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
          >
            <UserPlus :size="18" />
            <span>Ajouter un utilisateur</span>
          </RouterLink>
        </div>
      </div>

      <!-- MESSAGES FLASH -->
      <div
        v-if="adminUsersStore.successMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
      >
        <span>{{ adminUsersStore.successMessage }}</span>
        <button class="font-bold text-green-700 hover:text-green-900" @click="adminUsersStore.successMessage = null">
          ×
        </button>
      </div>

      <div
        v-if="adminUsersStore.errorMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
      >
        <span>{{ adminUsersStore.errorMessage }}</span>
        <button class="font-bold text-red-700 hover:text-red-900" @click="adminUsersStore.errorMessage = null">
          ×
        </button>
      </div>

      <!-- FILTRES -->
      <UsersFilters
        @search="handleSearch"
        @role-change="handleRoleChange"
        @sort-change="handleSortChange"
      />

      <!-- TABLE / CONTENU -->
      <div
        class="mt-6 overflow-hidden rounded-[16px] border border-[#E2E8F0] bg-white shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
      >
        <!-- LOADING SPINNER -->
        <div v-if="adminUsersStore.loading" class="flex flex-col items-center justify-center py-20">
          <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
          <p class="mt-4 text-sm font-medium text-gray-500">Chargement des utilisateurs...</p>
        </div>

        <!-- LISTE VIDE -->
        <div
          v-else-if="filteredUsers.length === 0"
          class="flex flex-col items-center justify-center py-16 text-center"
        >
          <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <Mail :size="24" />
          </div>
          <h3 class="mt-4 text-base font-semibold text-gray-900">Aucun utilisateur trouvé</h3>
          <p class="mt-1 text-sm text-gray-500">
            Essayez de modifier vos filtres ou ajoutez un nouvel utilisateur.
          </p>
        </div>

        <!-- TABLEAU -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Utilisateur
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Email & Téléphone
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Rôle
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Date d'inscription
                </th>
                <th class="px-6 py-4 text-right text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody class="divide-y divide-[#E2E8F0]">
              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="transition-colors duration-200 hover:bg-[#F8FAFC]"
              >
                <!-- NOM & AVATAR -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-700"
                    >
                      {{ user.nom ? user.nom.charAt(0).toUpperCase() : 'U' }}
                    </div>
                    <div>
                      <p class="text-[14px] font-semibold text-[#0F172A]">
                        {{ user.nom }}
                      </p>
                      <p class="text-[12px] text-gray-400">
                        ID: #{{ user.id }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- EMAIL & TEL -->
                <td class="px-6 py-4">
                  <div class="text-[14px] text-[#0F172A]">{{ user.email }}</div>
                  <div v-if="user.telephone" class="mt-0.5 flex items-center gap-1 text-[12px] text-gray-500">
                    <Phone :size="12" />
                    <span>{{ user.telephone }}</span>
                  </div>
                </td>

                <!-- ROLE -->
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-[12px] font-semibold capitalize"
                    :class="getRoleBadgeClass(user.role)"
                  >
                    {{ user.role }}
                  </span>
                </td>

                <!-- DATE -->
                <td class="px-6 py-4 text-[14px] text-[#64748B]">
                  <div class="flex items-center gap-1.5">
                    <Calendar :size="14" class="text-gray-400" />
                    <span>{{ formatDate(user.created_at) }}</span>
                  </div>
                </td>

                <!-- ACTIONS -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <!-- Voir -->
                    <RouterLink
                      :to="{ name: 'info-user', params: { id: user.id } }"
                      title="Voir le profil"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
                    >
                      <Eye :size="15" />
                    </RouterLink>

                    <!-- Modifier -->
                    <RouterLink
                      :to="{ name: 'update-user', params: { id: user.id } }"
                      title="Modifier"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600"
                    >
                      <Pencil :size="15" />
                    </RouterLink>

                    <!-- Supprimer -->
                    <button
                      type="button"
                      title="Supprimer"
                      :disabled="authStore.user?.id === user.id"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40"
                      @click="openDeleteModal(user)"
                    >
                      <Trash2 :size="15" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
            class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            @click="closeDeleteModal"
          >
            Annuler
          </button>

          <button
            type="button"
            :disabled="isDeleting"
            class="flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:opacity-50"
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
