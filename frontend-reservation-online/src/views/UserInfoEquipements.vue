<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import { useEquipementsStore } from '@/store/equipements'
import {
    ArrowLeft,
    Package,
    CheckCircle2,
    AlertCircle,
    Calendar,
    Loader2,
    ArrowUpRight,
    Sparkles,
    ShieldCheck,
    Layers,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const equipementsStore = useEquipementsStore()

const equipementId = route.params.id
const equipement = ref(null)
const isFetching = ref(true)
const fetchError = ref(null)

const isConnected = computed(() => !!localStorage.getItem('token'))

const defaultPlaceholder = '/images/equipements/equipement-1.png'

onMounted(async () => {
    try {
        isFetching.value = true
        equipement.value = await equipementsStore.fetchEquipement(equipementId)
    } catch (err) {
        fetchError.value = err.message || "Impossible de charger les informations de cet équipement."
        console.error("Erreur fetchEquipement:", err)
    } finally {
        isFetching.value = false
    }
})

const isDisponible = computed(() => {
    return equipement.value && (!equipement.value.status || equipement.value.status.toLowerCase() === 'disponible')
})

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A'
    try {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }).format(new Date(dateStr))
    } catch {
        return dateStr
    }
}

const handleReserver = () => {
    const token = localStorage.getItem('token')
    if (!token) {
        router.push({ name: 'login', query: { redirect: 'reservation', equipement_id: equipementId } })
        return
    }
    router.push({ name: 'create-reservation', query: { equipement_id: equipementId } })
}
</script>

<template>
    <div class="min-h-screen bg-[#F8FAFC] flex flex-col justify-between">
        <NavBar />

        <main class="flex-1 px-4 py-10 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl">

                <!-- Navigation retour -->
                <div class="mb-8">
                    <RouterLink
                        :to="{ name: 'equipements' }"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300"
                    >
                        <ArrowLeft :size="15" />
                        <span>Retour aux équipements</span>
                    </RouterLink>
                </div>

                <!-- Chargement -->
                <div v-if="isFetching" class="flex flex-col items-center justify-center py-20">
                    <Loader2 :size="40" class="animate-spin text-[#4F46E5] mb-3" />
                    <p class="text-sm font-medium text-slate-600">Chargement de l'équipement...</p>
                </div>

                <!-- Erreur -->
                <div
                    v-else-if="fetchError"
                    class="rounded-3xl border border-rose-200 bg-rose-50/70 p-10 text-center"
                >
                    <AlertCircle :size="44" class="mx-auto mb-3 text-rose-500" />
                    <h2 class="text-lg font-bold text-rose-900">Équipement introuvable</h2>
                    <p class="mt-1 text-sm text-rose-700">{{ fetchError }}</p>
                    <RouterLink
                        :to="{ name: 'equipements' }"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#0F172A] px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition"
                    >
                        <span>Voir la liste des équipements</span>
                    </RouterLink>
                </div>

                <!-- Contenu fiche équipement -->
                <div
                    v-else-if="equipement"
                    class="overflow-hidden rounded-3xl border border-[#E2E8F0] bg-white shadow-[0_4px_25px_-5px_rgba(15,23,42,0.07)]"
                >
                    <div class="grid grid-cols-1 lg:grid-cols-12">

                        <!-- Image de l'équipement -->
                        <div class="relative aspect-video lg:aspect-auto lg:col-span-5 bg-slate-100 overflow-hidden">
                            <img
                                :src="equipement.image_url || equipement.image || defaultPlaceholder"
                                :alt="equipement.nom"
                                class="h-full w-full object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/10"></div>

                            <!-- Badge Statut -->
                            <div
                                class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold text-white shadow-md backdrop-blur-md"
                                :class="isDisponible ? 'bg-emerald-600/90' : 'bg-rose-600/90'"
                            >
                                <CheckCircle2 v-if="isDisponible" :size="13" />
                                <AlertCircle v-else :size="13" />
                                <span>{{ isDisponible ? 'Disponible immédiatement' : 'Actuellement Indisponible' }}</span>
                            </div>
                        </div>

                        <!-- Détails -->
                        <div class="p-6 sm:p-8 lg:col-span-7 flex flex-col justify-between">
                            <div>
                                <div class="inline-flex items-center gap-1.5 rounded-full bg-[#EEF2FF] px-3 py-1 text-xs font-semibold text-[#4F46E5] mb-3">
                                    <Sparkles :size="13" />
                                    <span>Fiche Matériel</span>
                                </div>

                                <h1 class="text-2xl font-bold tracking-tight text-[#0F172A] sm:text-3xl">
                                    {{ equipement.nom }}
                                </h1>

                                <p class="mt-4 text-sm leading-relaxed text-[#64748B] whitespace-pre-line">
                                    {{ equipement.description || 'Aucune description détaillée n\'est renseignée pour cet équipement.' }}
                                </p>

                                <!-- Caractéristiques -->
                                <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4">
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-[#4F46E5] mb-2">
                                            <Package :size="18" />
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium">Quantité en stock</p>
                                        <p class="text-lg font-bold text-[#0F172A]">{{ equipement.stock_total || 0 }} unités</p>
                                    </div>

                                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 mb-2">
                                            <ShieldCheck :size="18" />
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium">État du matériel</p>
                                        <p class="text-lg font-bold text-[#0F172A]">Certifié conforme</p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-2 text-xs text-slate-400">
                                    <Calendar :size="13" />
                                    <span>Ajouté le {{ formatDate(equipement.created_at) }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                <RouterLink
                                    :to="{ name: 'equipements' }"
                                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                                >
                                    Retour
                                </RouterLink>

                                <button
                                    type="button"
                                    @click="handleReserver"
                                    class="inline-flex items-center gap-2 rounded-xl bg-[#0F172A] px-6 py-2.5 text-xs font-semibold text-white shadow-md hover:bg-[#4F46E5] transition cursor-pointer"
                                >
                                    <span>{{ isConnected ? 'Réserver cet équipement' : 'Se connecter pour réserver' }}</span>
                                    <ArrowUpRight :size="15" />
                                </button>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </main>

        <Footer />
    </div>
</template>
