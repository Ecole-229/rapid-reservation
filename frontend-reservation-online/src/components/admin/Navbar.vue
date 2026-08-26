<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
    Menu,
    Plus,
    Search,
    Maximize,
    Minimize,
    Bell,
    ChevronDown,
    User,
    LogOut,
    Mail,
    Phone,
    Shield,
    Calendar,
    X,
    CalendarPlus,
    UserPlus,
    Image,
    Monitor,
    DoorOpen,
} from 'lucide-vue-next'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()
const isFullscreen = ref(false)
const isProfileMenuOpen = ref(false)
const isProfileModalOpen = ref(false)
const isCreateMenuOpen = ref(false)
const profileDropdownRef = ref(null)
const createDropdownRef = ref(null)

// Définition des options de création
const createOptions = [
    {
        key: 'user',
        label: 'Utilisateur',
        routeName: 'admin-users',
        icon: 'UserPlus',
        color: '#818CF8',
        desc: 'Gérer les comptes',
    },
    {
        key: 'reservation',
        label: 'Réservation',
        routeName: 'admin-reservations',
        icon: 'CalendarPlus',
        color: '#38BDF8',
        desc: 'Nouvelle réservation',
    },
    {
        key: 'salle',
        label: 'Salle',
        routeName: 'admin-salles',
        icon: 'DoorOpen',
        color: '#34D399',
        desc: 'Ajouter un espace',
    },
    {
        key: 'equipment',
        label: 'Équipement',
        routeName: 'admin-equipments',
        icon: 'Monitor',
        color: '#FCD34D',
        desc: 'Matériel disponible',
    },
    {
        key: 'galerie',
        label: 'Galerie',
        routeName: 'admin-galeries',
        icon: 'Image',
        color: '#F9A8D4',
        desc: 'Photos & médias',
    },
]

// Option par défaut basée sur la route active
const defaultCreateOption = computed(() => {
    const matched = createOptions.find(o => o.routeName === route.name)
    return matched || createOptions[1] // Réservation par défaut
})

// Options du menu (défaut en premier, reste en suivant)
const orderedCreateOptions = computed(() => {
    const def = defaultCreateOption.value
    return [def, ...createOptions.filter(o => o.key !== def.key)]
})

const toggleCreateMenu = () => {
    isCreateMenuOpen.value = !isCreateMenuOpen.value
    if (isCreateMenuOpen.value) {
        isProfileMenuOpen.value = false
    }
}

const handleCreate = (option) => {
    isCreateMenuOpen.value = false
    router.push({ name: option.routeName })
}

const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(() => {
            isFullscreen.value = true
        }).catch(() => {})
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen().then(() => {
                isFullscreen.value = false
            }).catch(() => {})
        }
    }
}

const toggleProfileMenu = () => {
    isProfileMenuOpen.value = !isProfileMenuOpen.value
}

const openProfileModal = () => {
    isProfileMenuOpen.value = false
    isProfileModalOpen.value = true
}

const closeProfileModal = () => {
    isProfileModalOpen.value = false
}

const handleLogout = async () => {
    isProfileMenuOpen.value = false
    isProfileModalOpen.value = false
    await authStore.logout()
}

const handleClickOutside = (event) => {
    if (profileDropdownRef.value && !profileDropdownRef.value.contains(event.target)) {
        isProfileMenuOpen.value = false
    }
    if (createDropdownRef.value && !createDropdownRef.value.contains(event.target)) {
        isCreateMenuOpen.value = false
    }
}

const formatDate = (dateString) => {
    if (!dateString) return 'Non spécifié'
    try {
        const date = new Date(dateString)
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        })
    } catch {
        return dateString
    }
}

onMounted(() => {
    if (typeof document !== 'undefined') {
        document.documentElement.classList.remove('dark')
        localStorage.removeItem('admin_theme')
        document.addEventListener('fullscreenchange', () => {
            isFullscreen.value = !!document.fullscreenElement
        })
        document.addEventListener('click', handleClickOutside)
    }
})

onUnmounted(() => {
    if (typeof document !== 'undefined') {
        document.removeEventListener('click', handleClickOutside)
    }
})
</script>

<template>
    <header
        class="sticky top-0 z-40 flex h-[84px] items-center
               border-b border-[#E2E8F0] bg-white px-10 text-[#0F172A]"
    >
        <div class="flex w-full items-center gap-5">

            <!-- MENU BUTTON -->
            <button
                class="flex h-[42px] w-[42px] shrink-0 items-center
                       justify-center rounded-[13px] border border-[#E2E8F0]
                       bg-white text-[#475569]
                       shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]
                       transition-all duration-150 hover:bg-[#F8FAFC] hover:text-[#0F172A]
                       active:scale-[0.98]"
            >
                <Menu
                    :size="19"
                    :stroke-width="1.8"
                />
            </button>

            <!-- FULLSCREEN -->
            <button
                @click="toggleFullscreen"
                :title="isFullscreen ? 'Quitter le plein écran' : 'Plein écran'"
                class="flex h-[42px] w-[42px] shrink-0 items-center justify-center
                       rounded-[13px] border border-[#E2E8F0] bg-white
                       text-[#475569]
                       shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]
                       transition-all duration-150 hover:bg-[#F8FAFC] hover:text-[#0F172A]
                       active:scale-[0.98]"
            >
                <Minimize
                    v-if="isFullscreen"
                    :size="18"
                    :stroke-width="1.7"
                />
                <Maximize
                    v-else
                    :size="18"
                    :stroke-width="1.7"
                />
            </button>

            <!-- SEARCH -->
            <div class="relative ml-1 max-w-[480px] flex-1">
                <input
                    type="text"
                    placeholder="Search anything in Spark..."
                    class="h-[42px] w-full rounded-full border border-[#E2E8F0]
                           bg-white pl-5 pr-12 text-[14px] text-[#0F172A]
                           outline-none placeholder:text-[#94A3B8]
                           transition focus:border-[#4F46E5] focus:ring-4 focus:ring-[#4F46E5]/10"
                />

                <Search
                    :size="19"
                    :stroke-width="1.7"
                    class="absolute right-5 top-1/2 -translate-y-1/2
                           text-[#64748B]"
                />
            </div>

            <!-- RIGHT ACTIONS CLUSTER -->
            <div class="ml-auto flex shrink-0 items-center gap-3">

                <!-- CREATE DROPDOWN -->
                <div ref="createDropdownRef" class="relative">
                    <button
                        @click.stop="toggleCreateMenu"
                        class="flex h-[42px] shrink-0 items-center gap-2
                               rounded-[13px] bg-[#0F172A] pl-4 pr-3
                               text-[14px] font-semibold text-white
                               shadow-[0_4px_20px_-4px_rgba(15,23,42,0.3)]
                               transition-all duration-150 hover:bg-[#020617]
                               active:scale-[0.98]"
                    >
                        <Plus :size="17" :stroke-width="2.2" />
                        <span>{{ defaultCreateOption.label }}</span>
                        <ChevronDown
                            :size="14"
                            :stroke-width="2"
                            class="ml-0.5 transition-transform duration-200"
                            :class="{ 'rotate-180': isCreateMenuOpen }"
                        />
                    </button>

                    <!-- DROPDOWN MENU -->
                    <div
                        v-if="isCreateMenuOpen"
                        class="absolute right-0 top-[calc(100%+8px)] z-50 w-[180px] overflow-hidden rounded-xl bg-[#0F172A] shadow-[0_12px_32px_-4px_rgba(15,23,42,0.45)] border border-white/10"
                    >
                        <!-- Titre -->
                        <div class="px-4 py-2.5 border-b border-white/10">
                            <p class="text-[11px] font-bold uppercase tracking-[1.2px] text-white/40">Ajouter</p>
                        </div>

                        <!-- Options -->
                        <div class="p-1.5">
                            <button
                                v-for="option in orderedCreateOptions"
                                :key="option.key"
                                @click="handleCreate(option)"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left transition-colors duration-100"
                                :class="option.key === defaultCreateOption.key
                                    ? 'bg-white/10 text-white'
                                    : 'text-white/60 hover:bg-white/5 hover:text-white'"
                            >
                                <span class="text-[13px] font-medium">{{ option.label }}</span>
                                <span
                                    v-if="option.key === defaultCreateOption.key"
                                    class="h-1.5 w-1.5 rounded-full bg-white/50"
                                ></span>
                            </button>
                        </div>
                    </div>
                </div>



                <!-- PROFILE MENU (DROPDOWN) -->
                <div ref="profileDropdownRef" class="relative">
                    <button
                        @click.stop="toggleProfileMenu"
                        class="flex items-center gap-3 rounded-xl p-1.5 transition-all duration-150 hover:bg-[#F8FAFC] active:scale-[0.98]"
                    >
                        <div
                            class="flex h-[38px] w-[38px] items-center justify-center overflow-hidden rounded-full bg-[#EEF2FF] font-bold text-[#4F46E5]"
                        >
                            {{ (authStore.currentUser?.nom || 'A').charAt(0).toUpperCase() }}
                        </div>

                        <div class="hidden flex-col text-left xl:flex">
                            <span class="text-[14px] font-semibold text-[#0F172A]">
                                {{ authStore.currentUser?.nom || 'Administrateur' }}
                            </span>
                            <span class="text-[11px] capitalize text-[#64748B]">
                                {{ authStore.userRole || 'admin' }}
                            </span>
                        </div>

                        <ChevronDown
                            :size="16"
                            :stroke-width="1.8"
                            class="text-[#64748B] transition-transform duration-200"
                            :class="{ 'rotate-180 text-[#0F172A]': isProfileMenuOpen }"
                        />
                    </button>

                    <!-- FLOATING DROPDOWN -->
                    <div
                        v-if="isProfileMenuOpen"
                        class="absolute right-0 top-[calc(100%+8px)] z-50 w-[200px] rounded-xl border border-[#E2E8F0] bg-white p-1.5 shadow-[0_10px_30px_-5px_rgba(15,23,42,0.12)] animate-in fade-in zoom-in duration-150"
                    >
                        <button
                            @click="openProfileModal"
                            class="flex h-[38px] w-full items-center gap-2.5 rounded-lg px-3 text-[13px] font-medium text-[#0F172A] transition hover:bg-[#EEF2FF] hover:text-[#3730A3]"
                        >
                            <User :size="16" class="text-[#64748B]" />
                            <span>Voir mon profil</span>
                        </button>

                        <div class="my-1 border-t border-[#E2E8F0]"></div>

                        <button
                            @click="handleLogout"
                            class="flex h-[38px] w-full items-center gap-2.5 rounded-lg px-3 text-[13px] font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700"
                        >
                            <LogOut :size="16" class="text-red-500" />
                            <span>Se déconnecter</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MODAL PROFIL ADMINISTRATEUR -->
    <div
        v-if="isProfileModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 animate-in fade-in duration-200"
        @click.self="closeProfileModal"
    >
        <div
            class="w-full max-w-[480px] rounded-3xl border border-[#E2E8F0] bg-white p-6 shadow-2xl space-y-6 animate-in zoom-in-95 duration-200"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EEF2FF] text-[#4F46E5]">
                        <User :size="20" />
                    </div>
                    <div>
                        <h2 class="text-[17px] font-bold text-[#0F172A]">Profil Administrateur</h2>
                        <p class="text-[12px] text-[#64748B]">Détails du compte et informations personnelles</p>
                    </div>
                </div>

                <button
                    @click="closeProfileModal"
                    class="rounded-xl p-1.5 text-gray-400 hover:bg-[#F8FAFC] hover:text-[#0F172A] transition"
                >
                    <X :size="18" />
                </button>
            </div>

            <!-- Modal Body (Informations de l'administrateur) -->
            <div class="space-y-4">
                <!-- Avatar & Nom Banner -->
                <div class="flex items-center gap-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] p-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#4F46E5] text-xl font-bold text-white shadow-md">
                        {{ (authStore.currentUser?.nom || 'A').charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-[16px] font-bold text-[#0F172A]">
                            {{ authStore.currentUser?.nom || 'Administrateur' }}
                        </h3>
                        <p class="truncate text-[13px] text-[#64748B]">
                            {{ authStore.currentUser?.email || 'admin@email.com' }}
                        </p>
                        <span class="mt-1 inline-flex items-center rounded-full bg-[#EEF2FF] px-2.5 py-0.5 text-[11px] font-semibold text-[#3730A3]">
                            {{ authStore.userRole || 'admin' }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-[#E2E8F0] bg-white p-3.5">
                        <div class="flex items-center gap-2 text-[12px] font-medium text-[#64748B]">
                            <Mail :size="14" class="text-[#4F46E5]" />
                            <span>Adresse E-mail</span>
                        </div>
                        <p class="mt-1 truncate text-[13px] font-semibold text-[#0F172A]">
                            {{ authStore.currentUser?.email || 'Non renseigné' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-[#E2E8F0] bg-white p-3.5">
                        <div class="flex items-center gap-2 text-[12px] font-medium text-[#64748B]">
                            <Phone :size="14" class="text-[#4F46E5]" />
                            <span>Téléphone</span>
                        </div>
                        <p class="mt-1 truncate text-[13px] font-semibold text-[#0F172A]">
                            {{ authStore.currentUser?.telephone || 'Non renseigné' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-[#E2E8F0] bg-white p-3.5">
                        <div class="flex items-center gap-2 text-[12px] font-medium text-[#64748B]">
                            <Shield :size="14" class="text-[#4F46E5]" />
                            <span>Rôle attribué</span>
                        </div>
                        <p class="mt-1 text-[13px] font-semibold capitalize text-[#0F172A]">
                            {{ authStore.userRole || 'Administrateur' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-[#E2E8F0] bg-white p-3.5">
                        <div class="flex items-center gap-2 text-[12px] font-medium text-[#64748B]">
                            <Calendar :size="14" class="text-[#4F46E5]" />
                            <span>Date d'inscription</span>
                        </div>
                        <p class="mt-1 text-[13px] font-semibold text-[#0F172A]">
                            {{ formatDate(authStore.currentUser?.created_at) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between border-t border-[#E2E8F0] pt-4">
                <button
                    @click="handleLogout"
                    class="flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2.5 text-[13px] font-bold text-red-600 transition hover:bg-red-100 hover:text-red-700 active:scale-[0.98]"
                >
                    <LogOut :size="16" />
                    <span>Déconnexion</span>
                </button>

                <button
                    @click="closeProfileModal"
                    class="rounded-xl bg-[#0F172A] px-5 py-2.5 text-[13px] font-bold text-white transition hover:bg-[#020617] active:scale-[0.98]"
                >
                    Fermer
                </button>
            </div>
        </div>
    </div>
</template>