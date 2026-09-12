<script setup>
import { computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { useReservationsStore } from '@/store/reservations'
import {
  X,
  User,
  Mail,
  Phone,
  ShieldCheck,
  Calendar,
  Ticket,
  PlusCircle,
  LogOut,
  Sparkles,
  CheckCircle2,
} from 'lucide-vue-next'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close', 'update:isOpen'])

const authStore = useAuthStore()
const reservationsStore = useReservationsStore()
const router = useRouter()

const currentUser = computed(() => authStore.currentUser)

// Formatage de la date d'inscription
const formattedJoinDate = computed(() => {
  const dateStr = currentUser.value?.created_at
  if (!dateStr) return 'Récemment'
  try {
    const d = new Date(dateStr)
    return new Intl.DateTimeFormat('fr-FR', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    }).format(d)
  } catch {
    return 'Récemment'
  }
})

// Rôle affiché joliment
const roleLabel = computed(() => {
  const role = currentUser.value?.role || authStore.userRole
  if (role === 'admin') return 'Administrateur'
  if (role === 'responsable') return 'Responsable Salles'
  return 'Client Membre'
})

// Nombre de réservations si dispo
const reservationsCount = computed(() => {
  if (!reservationsStore.reservations) return 0
  return reservationsStore.reservations.length
})

const closeModal = () => {
  emit('update:isOpen', false)
  emit('close')
}

// Navigation rapide
const goToReservations = () => {
  closeModal()
  router.push({ name: 'user-reservations' })
}

const goToCreateReservation = () => {
  closeModal()
  router.push({ name: 'user-create-reservation' })
}

const handleLogout = async () => {
  closeModal()
  await authStore.logout()
}

// Touche Échap
const onKeyDown = (e) => {
  if (e.key === 'Escape' && props.isOpen) {
    closeModal()
  }
}

// Bloquer le scroll de la page quand le modal est ouvert
watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      document.body.style.overflow = 'hidden'
      // Rafraîchir les informations de l'utilisateur en tâche de fond
      if (authStore.isAuthenticated) {
        authStore.fetchUser()
      }
    } else {
      document.body.style.overflow = ''
    }
  },
  { immediate: true }
)

onMounted(() => {
  window.addEventListener('keydown', onKeyDown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeyDown)
  document.body.style.overflow = ''
})
</script>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-md"
        @click.self="closeModal"
      >
      <div
        class="relative w-full max-w-[490px] overflow-hidden rounded-[26px] border border-[#ecebe7] bg-white p-6 sm:p-7 shadow-[0_24px_64px_rgba(0,0,0,0.18)] animate-in zoom-in-95 duration-200"
        role="dialog"
        aria-modal="true"
        aria-labelledby="user-profile-title"
      >
        <!-- BOUTON FERMER -->
        <button
          type="button"
          @click="closeModal"
          class="absolute top-5 right-5 flex h-9 w-9 items-center justify-center rounded-full bg-[#f6f6f4] text-[#666] transition hover:bg-[#eae9e5] hover:text-[#111] cursor-pointer"
          aria-label="Fermer la fenêtre du profil"
        >
          <X :size="18" />
        </button>

        <!-- EN-TÊTE / BADGE -->
        <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.12em] text-[#888]">
          <Sparkles :size="13" class="text-amber-500" />
          <span>Informations du profil</span>
        </div>

        <!-- CARTE HERO UTILISATEUR -->
        <div class="mt-4 flex items-center gap-4 rounded-[20px] bg-gradient-to-br from-[#18191a] to-[#242628] p-5 text-white shadow-sm">
          <div
            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-amber-400 to-amber-200 text-xl font-black text-[#111] shadow-md border-2 border-white/20"
          >
            {{ (currentUser?.nom || 'U').charAt(0).toUpperCase() }}
          </div>
          <div class="min-w-0 flex-1">
            <h2 id="user-profile-title" class="truncate text-[18px] font-bold leading-tight text-white">
              {{ currentUser?.nom || 'Utilisateur' }}
            </h2>
            <p class="truncate text-[12px] text-white/70 mt-0.5">
              {{ currentUser?.email || 'Non renseigné' }}
            </p>
            <div class="mt-2 flex items-center gap-2">
              <span
                class="inline-flex items-center gap-1 rounded-full bg-white/15 px-2.5 py-0.5 text-[11px] font-semibold text-white/90 backdrop-blur-sm"
              >
                <CheckCircle2 :size="11" class="text-emerald-400" />
                {{ roleLabel }}
              </span>
              <span
                v-if="currentUser?.id"
                class="text-[11px] text-white/40 font-mono"
              >
                ID #{{ currentUser.id }}
              </span>
            </div>
          </div>
        </div>

        <!-- GRILLE D'INFORMATIONS DÉTAILLÉES -->
        <div class="mt-5 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
          <!-- Nom complet -->
          <div class="rounded-[14px] border border-[#ecebe7] bg-[#fbfbfa] p-3.5">
            <div class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-[0.05em] text-[#888]">
              <User :size="13" class="text-[#444]" />
              <span>Nom complet</span>
            </div>
            <p class="mt-1 text-[13px] font-bold text-[#151515] truncate">
              {{ currentUser?.nom || 'Non renseigné' }}
            </p>
          </div>

          <!-- Email -->
          <div class="rounded-[14px] border border-[#ecebe7] bg-[#fbfbfa] p-3.5">
            <div class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-[0.05em] text-[#888]">
              <Mail :size="13" class="text-[#444]" />
              <span>Adresse e-mail</span>
            </div>
            <p class="mt-1 text-[13px] font-bold text-[#151515] truncate" :title="currentUser?.email">
              {{ currentUser?.email || 'Non renseigné' }}
            </p>
          </div>

          <!-- Téléphone -->
          <div class="rounded-[14px] border border-[#ecebe7] bg-[#fbfbfa] p-3.5">
            <div class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-[0.05em] text-[#888]">
              <Phone :size="13" class="text-[#444]" />
              <span>Téléphone</span>
            </div>
            <p class="mt-1 text-[13px] font-bold text-[#151515] truncate">
              {{ currentUser?.telephone || 'Non renseigné' }}
            </p>
          </div>

          <!-- Statut / Rôle -->
          <div class="rounded-[14px] border border-[#ecebe7] bg-[#fbfbfa] p-3.5">
            <div class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-[0.05em] text-[#888]">
              <ShieldCheck :size="13" class="text-[#444]" />
              <span>Statut compte</span>
            </div>
            <div class="mt-1 flex items-center gap-1.5">
              <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
              <p class="text-[13px] font-bold text-[#151515] capitalize">
                {{ roleLabel }}
              </p>
            </div>
          </div>

          <!-- Date d'inscription (pleine largeur) -->
          <div class="sm:col-span-2 rounded-[14px] border border-[#ecebe7] bg-[#fbfbfa] p-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-[#ecebe7] text-[#444]">
                <Calendar :size="14" />
              </div>
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.05em] text-[#888]">Date d'adhésion</p>
                <p class="text-[13px] font-bold text-[#151515]">{{ formattedJoinDate }}</p>
              </div>
            </div>

            <!-- Badges réservations si user -->
            <div v-if="authStore.isUser" class="text-right">
              <p class="text-[11px] font-semibold uppercase tracking-[0.05em] text-[#888]">Réservations</p>
              <span class="inline-flex items-center gap-1 text-[12px] font-bold text-[#151515]">
                <Ticket :size="13" class="text-amber-600" />
                <span>{{ reservationsCount }} active{{ reservationsCount > 1 ? 's' : '' }}</span>
              </span>
            </div>
          </div>
        </div>

        <!-- RACCOURCIS / ACTIONS RAPIDES (POUR UTILISATEURS) -->
        <div v-if="authStore.isUser" class="mt-4 flex gap-2.5">
          <button
            type="button"
            @click="goToReservations"
            class="flex-1 inline-flex items-center justify-center gap-2 rounded-[12px] border border-[#ecebe7] bg-white px-3.5 py-2.5 text-[12px] font-bold text-[#151515] transition hover:bg-[#f6f6f4] hover:border-[#deddd9] cursor-pointer"
          >
            <Ticket :size="14" />
            <span>Mes réservations</span>
          </button>

          <button
            type="button"
            @click="goToCreateReservation"
            class="flex-1 inline-flex items-center justify-center gap-2 rounded-[12px] bg-[#151515] px-3.5 py-2.5 text-[12px] font-bold text-white transition hover:bg-[#2c2d2e] cursor-pointer"
          >
            <PlusCircle :size="14" />
            <span>Réserver</span>
          </button>
        </div>

        <!-- PIED DU MODAL -->
        <div class="mt-5 flex items-center justify-between border-t border-[#ecebe7] pt-4">
          <button
            type="button"
            @click="handleLogout"
            class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-rose-600 transition hover:text-rose-700 cursor-pointer"
          >
            <LogOut :size="14" />
            <span>Se déconnecter</span>
          </button>

          <button
            type="button"
            @click="closeModal"
            class="rounded-[10px] bg-[#f2f1ed] px-4 py-2 text-[12px] font-bold text-[#333] transition hover:bg-[#e7e6e1] cursor-pointer"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </Transition>
  </Teleport>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
