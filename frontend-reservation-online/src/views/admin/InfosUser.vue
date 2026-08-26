<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminUsersStore } from '@/store/adminUsers'
import {
  Mail,
  Phone,
  ShieldCheck,
  CalendarDays,
  UserRound,
  Pencil,
  ArrowLeft,
  Loader2,
  AlertCircle,
} from 'lucide-vue-next'

const route = useRoute()
const adminUsersStore = useAdminUsersStore()

const userId = route.params.id
const user = ref(null)
const isFetching = ref(true)

const formatDate = (dateString) => {
  if (!dateString) return 'Non renseignée'
  try {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(date)
  } catch {
    return dateString
  }
}

const getRoleBadgeClass = (role) => {
  switch (role) {
    case 'admin':
      return 'bg-purple-50 text-purple-700 border border-purple-200'
    case 'responsable':
      return 'bg-blue-50 text-blue-700 border border-blue-200'
    default:
      return 'bg-slate-50 text-slate-700 border border-slate-200'
  }
}

onMounted(async () => {
  adminUsersStore.clearErrors()
  try {
    const data = await adminUsersStore.fetchUser(userId)
    user.value = data
  } catch (error) {
    console.error('Erreur lors du chargement des détails de l\'utilisateur :', error)
  } finally {
    isFetching.value = false
  }
})
</script>

<template>
  <AppAdmin>
    <div class="min-h-screen bg-[#F8FAFC]">
      <!-- RETOUR & HEADER -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-users' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des utilisateurs</span>
        </RouterLink>

        <h1 class="text-[30px] font-bold tracking-[-0.8px] text-[#0F172A]">
          Détails de l'utilisateur
        </h1>
        <p class="mt-1 text-[14px] text-[#64748B]">
          Consultez et gérez les informations du profil utilisateur.
        </p>
      </div>

      <!-- CHARGEMENT -->
      <div
        v-if="isFetching"
        class="flex flex-col items-center justify-center rounded-[20px] border border-[#E2E8F0] bg-white p-16 shadow-sm"
      >
        <Loader2 :size="36" class="animate-spin text-blue-600" />
        <p class="mt-4 text-sm font-medium text-gray-500">Chargement des informations...</p>
      </div>

      <!-- ERREUR SI INTROUVABLE -->
      <div
        v-else-if="!user"
        class="flex flex-col items-center justify-center rounded-[20px] border border-red-200 bg-white p-12 text-center shadow-sm"
      >
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
          <AlertCircle :size="24" />
        </div>
        <h3 class="mt-4 text-lg font-bold text-gray-900">Utilisateur non trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">
          L'utilisateur demandé avec l'ID #{{ userId }} n'existe pas ou a été supprimé.
        </p>
        <RouterLink
          :to="{ name: 'admin-users' }"
          class="mt-5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
        >
          Retour aux utilisateurs
        </RouterLink>
      </div>

      <!-- PROFILE CARD -->
      <div
        v-else
        class="overflow-hidden rounded-[20px] border border-[#E2E8F0] bg-white shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
      >
        <!-- TOP HEADER -->
        <div class="border-b border-[#E2E8F0] px-6 py-5 lg:px-8">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-[18px] font-semibold text-[#0F172A]">
                Informations du compte #{{ user.id }}
              </h2>
              <p class="mt-1 text-[13px] text-[#64748B]">
                Données du compte enregistrées dans la base de données.
              </p>
            </div>

            <!-- EDIT BUTTON -->
            <RouterLink
              :to="{ name: 'update-user', params: { id: user.id } }"
              class="flex h-[40px] items-center gap-2 rounded-[10px] bg-[#0F172A] px-4 text-[13px] font-medium text-white transition-all duration-150 hover:bg-[#020617] active:scale-[0.98]"
            >
              <Pencil :size="16" :stroke-width="1.8" />
              <span>Modifier</span>
            </RouterLink>
          </div>
        </div>

        <!-- CONTENT GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr]">
          <!-- PHOTO & RESUME -->
          <div
            class="flex flex-col items-center justify-center border-b border-[#E2E8F0] p-8 lg:border-b-0 lg:border-r"
          >
            <!-- AVATAR AVEC INITIALES -->
            <div
              class="flex h-[140px] w-[140px] items-center justify-center rounded-full border-[6px] border-[#EEF2FF] bg-gradient-to-tr from-blue-600 to-indigo-600 text-4xl font-bold text-white shadow-inner"
            >
              {{ user.nom ? user.nom.charAt(0).toUpperCase() : 'U' }}
            </div>

            <!-- NAME -->
            <h3 class="mt-5 text-[20px] font-bold text-[#0F172A]">
              {{ user.nom }}
            </h3>

            <!-- ROLE -->
            <span
              class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-[12px] font-semibold capitalize"
              :class="getRoleBadgeClass(user.role)"
            >
              {{ user.role }}
            </span>

            <!-- STATUS -->
            <div class="mt-4 flex items-center gap-2">
              <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
              <span class="text-[12px] font-medium text-[#64748B]">
                Compte actif
              </span>
            </div>
          </div>

          <!-- INFORMATIONS DETAILLEES -->
          <div class="p-6 lg:p-8">
            <div class="mb-6 flex items-center gap-3">
              <div
                class="flex h-9 w-9 items-center justify-center rounded-[9px] bg-[#EEF2FF]"
              >
                <UserRound :size="18" :stroke-width="1.8" class="text-[#4F46E5]" />
              </div>

              <div>
                <h3 class="text-[15px] font-semibold text-[#0F172A]">
                  Informations personnelles
                </h3>
                <p class="text-[12px] text-[#94A3B8]">
                  Détails associés à l'utilisateur
                </p>
              </div>
            </div>

            <!-- INFOS GRID -->
            <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2">
              <!-- NOM COMPLET -->
              <div>
                <p class="mb-2 text-[12px] font-medium uppercase tracking-wide text-[#94A3B8]">
                  Nom complet
                </p>
                <p class="text-[14px] font-medium text-[#0F172A]">
                  {{ user.nom }}
                </p>
              </div>

              <!-- IDENTIFIANT -->
              <div>
                <p class="mb-2 text-[12px] font-medium uppercase tracking-wide text-[#94A3B8]">
                  Identifiant système (ID)
                </p>
                <p class="text-[14px] font-medium text-[#0F172A]">
                  #{{ user.id }}
                </p>
              </div>

              <!-- EMAIL -->
              <div>
                <p class="mb-2 text-[12px] font-medium uppercase tracking-wide text-[#94A3B8]">
                  Adresse email
                </p>
                <div class="flex items-center gap-2">
                  <Mail :size="16" :stroke-width="1.8" class="text-[#64748B]" />
                  <p class="text-[14px] font-medium text-[#0F172A]">
                    {{ user.email }}
                  </p>
                </div>
              </div>

              <!-- TELEPHONE -->
              <div>
                <p class="mb-2 text-[12px] font-medium uppercase tracking-wide text-[#94A3B8]">
                  Numéro de téléphone
                </p>
                <div class="flex items-center gap-2">
                  <Phone :size="16" :stroke-width="1.8" class="text-[#64748B]" />
                  <p class="text-[14px] font-medium text-[#0F172A]">
                    {{ user.telephone || 'Non renseigné' }}
                  </p>
                </div>
              </div>

              <!-- ROLE -->
              <div>
                <p class="mb-2 text-[12px] font-medium uppercase tracking-wide text-[#94A3B8]">
                  Rôle
                </p>
                <div class="flex items-center gap-2">
                  <ShieldCheck :size="17" :stroke-width="1.8" class="text-[#4F46E5]" />
                  <span
                    class="rounded-full px-3 py-1 text-[12px] font-semibold capitalize"
                    :class="getRoleBadgeClass(user.role)"
                  >
                    {{ user.role }}
                  </span>
                </div>
              </div>

              <!-- DATE D'INSCRIPTION -->
              <div>
                <p class="mb-2 text-[12px] font-medium uppercase tracking-wide text-[#94A3B8]">
                  Date d'enregistrement
                </p>
                <div class="flex items-center gap-2">
                  <CalendarDays :size="16" :stroke-width="1.8" class="text-[#64748B]" />
                  <p class="text-[14px] font-medium text-[#0F172A]">
                    {{ formatDate(user.created_at) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- SEPARATEUR -->
            <div class="my-8 h-px bg-[#E2E8F0]"></div>

            <!-- ACTIONS RAPIDES -->
            <div
              class="flex flex-col gap-4 rounded-[12px] border border-[#E2E8F0] bg-[#F8FAFC] p-5 sm:flex-row sm:items-center sm:justify-between"
            >
              <div>
                <h4 class="text-[14px] font-semibold text-[#0F172A]">
                  Gestion du compte
                </h4>
                <p class="mt-1 text-[12px] text-[#64748B]">
                  Vous pouvez mettre à jour les autorisations ou modifier les informations.
                </p>
              </div>

              <RouterLink
                :to="{ name: 'update-user', params: { id: user.id } }"
                class="inline-flex items-center justify-center rounded-[9px] border border-[#E2E8F0] bg-white px-4 py-2 text-[13px] font-medium text-[#0F172A] transition-colors duration-200 hover:bg-[#EEF2FF] hover:text-[#3730A3]"
              >
                Mettre à jour ce profil
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
