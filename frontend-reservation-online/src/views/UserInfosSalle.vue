<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import { useSallesStore } from '@/store/salles'
import {
    ArrowLeft,
    MapPin,
    Users,
    Banknote,
    Calendar,
    CheckCircle2,
    AlertCircle,
    Loader2,
    ArrowUpRight,
    Sparkles,
    ShieldCheck,
    Clock,
    DoorOpen,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const sallesStore = useSallesStore()

const salleId = route.params.id
const salle = ref(null)
const isFetching = ref(true)
const fetchError = ref(null)

// Index de la photo sélectionnée dans la galerie
const selectedPhotoIndex = ref(0)

// Test de disponibilité
const debutDateTime = ref('')
const finDateTime = ref('')
const checkingDispo = ref(false)
const dispoResult = ref(null)
const dispoError = ref(null)

const isConnected = computed(() => !!localStorage.getItem('token'))

const defaultPlaceholder =
    'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80'

onMounted(async () => {
    try {
        isFetching.value = true
        salle.value = await sallesStore.fetchSalle(salleId)
    } catch (err) {
        fetchError.value = err.message || 'Impossible de charger les détails de cette salle.'
        console.error('Erreur fetchSalle:', err)
    } finally {
        isFetching.value = false
    }

    // Initialiser les dates du créneau par défaut : demain de 09:00 à 12:00
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    const yyyy = tomorrow.getFullYear()
    const mm = String(tomorrow.getMonth() + 1).padStart(2, '0')
    const dd = String(tomorrow.getDate()).padStart(2, '0')
    debutDateTime.value = `${yyyy}-${mm}-${dd}T09:00`
    finDateTime.value = `${yyyy}-${mm}-${dd}T12:00`
})

const imagesList = computed(() => {
    if (salle.value && salle.value.images && salle.value.images.length > 0) {
        return salle.value.images.map((img) => img.url || img.path || defaultPlaceholder)
    }
    return [defaultPlaceholder]
})

const activeImage = computed(() => {
    return imagesList.value[selectedPhotoIndex.value] || imagesList.value[0]
})

const isDisponible = computed(() => {
    return salle.value && (!salle.value.status || salle.value.status.toLowerCase() === 'disponible')
})

const formatPrice = (price) => {
    if (!price && price !== 0) return '0'
    return new Intl.NumberFormat('fr-FR').format(price)
}

const checkCreneau = async () => {
    if (!debutDateTime.value || !finDateTime.value) {
        dispoError.value = 'Veuillez renseigner une date de début et une date de fin.'
        return
    }
    if (new Date(debutDateTime.value) >= new Date(finDateTime.value)) {
        dispoError.value = 'La date de fin doit être postérieure à la date de début.'
        return
    }

    checkingDispo.value = true
    dispoError.value = null
    dispoResult.value = null

    try {
        const formattedDebut = debutDateTime.value.replace('T', ' ') + ':00'
        const formattedFin = finDateTime.value.replace('T', ' ') + ':00'
        const res = await sallesStore.checkDisponibilite(salleId, formattedDebut, formattedFin)
        dispoResult.value = res
    } catch (err) {
        dispoError.value = err.message || 'Erreur lors de la vérification de disponibilité.'
    } finally {
        checkingDispo.value = false
    }
}

const handleReserver = () => {
    const token = localStorage.getItem('token')
    if (!token) {
        router.push({
            name: 'login',
            query: { redirect: '/reserver', salle_id: salleId, debut: debutDateTime.value, fin: finDateTime.value },
        })
        return
    }

    router.push({
        name: 'user-create-reservation',
        query: {
            salle_id: salleId,
            debut: debutDateTime.value,
            fin: finDateTime.value,
        },
    })
}
</script>

<template>
    <div class="min-h-screen bg-[#F8FAFC] flex flex-col justify-between">
        <NavBar />

        <main class="flex-1 px-4 py-10 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">

                <!-- Navigation retour -->
                <div class="mb-8">
                    <RouterLink
                        :to="{ name: 'salles' }"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300"
                    >
                        <ArrowLeft :size="15" />
                        <span>Retour aux salles</span>
                    </RouterLink>
                </div>

                <!-- Chargement -->
                <div v-if="isFetching" class="flex flex-col items-center justify-center py-20">
                    <Loader2 :size="40" class="animate-spin text-[#4F46E5] mb-3" />
                    <p class="text-sm font-medium text-slate-600">Chargement des détails de la salle...</p>
                </div>

                <!-- Erreur -->
                <div
                    v-else-if="fetchError"
                    class="rounded-3xl border border-rose-200 bg-rose-50/70 p-10 text-center"
                >
                    <AlertCircle :size="44" class="mx-auto mb-3 text-rose-500" />
                    <h2 class="text-lg font-bold text-rose-900">Salle introuvable</h2>
                    <p class="mt-1 text-sm text-rose-700">{{ fetchError }}</p>
                    <RouterLink
                        :to="{ name: 'salles' }"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#0F172A] px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition"
                    >
                        <span>Retourner au catalogue des salles</span>
                    </RouterLink>
                </div>

                <!-- Contenu Fiche Salle -->
                <div v-else-if="salle" class="space-y-8">

                    <!-- En-tête : Titre, Localisation & Prix -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-slate-200/80 pb-6">
                        <div>
                            <div class="inline-flex items-center gap-1.5 rounded-full bg-[#EEF2FF] px-3 py-1 text-xs font-semibold text-[#4F46E5] mb-3">
                                <Sparkles :size="13" />
                                <span>Espace Événementiel</span>
                            </div>

                            <h1 class="text-3xl font-extrabold tracking-tight text-[#0F172A] sm:text-4xl">
                                {{ salle.nom }}
                            </h1>

                            <div class="mt-2 flex items-center gap-2 text-sm text-[#64748B]">
                                <MapPin :size="16" class="text-[#4F46E5] shrink-0" />
                                <span>{{ salle.localisation || 'Localisation non renseignée' }}</span>
                            </div>
                        </div>

                        <div class="sm:text-right">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tarif par réservation</span>
                            <p class="text-3xl sm:text-4xl font-black text-[#4F46E5]">
                                {{ formatPrice(salle.prix) }}
                                <span class="text-base font-semibold text-slate-500">FCFA</span>
                            </p>
                        </div>
                    </div>

                    <!-- Galerie Photos -->
                    <div class="space-y-3">
                        <div class="relative aspect-[16/9] md:aspect-[21/9] w-full overflow-hidden rounded-3xl bg-slate-200 shadow-md">
                            <img
                                :src="activeImage"
                                :alt="salle.nom"
                                class="h-full w-full object-cover transition-all duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/10"></div>

                            <!-- Statut Badge -->
                            <div
                                class="absolute left-5 top-5 inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-xs font-semibold text-white shadow-lg backdrop-blur-md"
                                :class="isDisponible ? 'bg-emerald-600/90' : 'bg-rose-600/90'"
                            >
                                <CheckCircle2 v-if="isDisponible" :size="14" />
                                <AlertCircle v-else :size="14" />
                                <span>{{ isDisponible ? 'Disponible' : 'Actuellement Occupée' }}</span>
                            </div>
                        </div>

                        <!-- Miniatures si multiples images -->
                        <div
                            v-if="imagesList.length > 1"
                            class="flex items-center gap-3 overflow-x-auto py-2"
                        >
                            <button
                                v-for="(img, idx) in imagesList"
                                :key="'thumb-' + idx"
                                type="button"
                                @click="selectedPhotoIndex = idx"
                                class="h-16 w-24 shrink-0 overflow-hidden rounded-xl border-2 transition-all cursor-pointer"
                                :class="selectedPhotoIndex === idx ? 'border-[#4F46E5] scale-105 shadow-md' : 'border-transparent opacity-60 hover:opacity-100'"
                            >
                                <img :src="img" class="h-full w-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <!-- Grille Informations & Réservation -->
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                        <!-- Colonne gauche : Description & Caractéristiques (7 cols) -->
                        <div class="lg:col-span-7 space-y-6">

                            <!-- Carte Caractéristiques -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-[#4F46E5] mb-2">
                                        <Users :size="18" />
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium">Capacité</p>
                                    <p class="text-base font-bold text-[#0F172A]">{{ salle.capacite }} personnes</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 mb-2">
                                        <CheckCircle2 :size="18" />
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium">Disponibilité</p>
                                    <p class="text-base font-bold capitalize" :class="isDisponible ? 'text-emerald-600' : 'text-rose-600'">
                                        {{ salle.status }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm col-span-2 sm:col-span-1">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-600 mb-2">
                                        <ShieldCheck :size="18" />
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium">Confort</p>
                                    <p class="text-base font-bold text-[#0F172A]">Climatisée & équipée</p>
                                </div>
                            </div>

                            <!-- Description détaillée -->
                            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                                <h2 class="text-base font-bold uppercase tracking-wider text-[#0F172A] mb-4">
                                    À propos de cet espace
                                </h2>
                                <p class="text-sm sm:text-base leading-relaxed text-slate-600 whitespace-pre-line">
                                    {{ salle.description || 'Cette salle moderne et fonctionnelle est prête à accueillir vos réunions, séminaires, conférences et formations professionnelles avec tout le confort nécessaire.' }}
                                </p>
                            </div>

                        </div>

                        <!-- Colonne droite : Vérification de créneau & Réservation (5 cols) -->
                        <div class="lg:col-span-5">
                            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-md sticky top-6 space-y-6">

                                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#EEF2FF] text-[#4F46E5]">
                                        <Calendar :size="22" />
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-[#0F172A]">
                                            Vérifier & Réserver
                                        </h3>
                                        <p class="text-xs text-slate-500">
                                            Testez un créneau avant confirmation
                                        </p>
                                    </div>
                                </div>

                                <!-- Formulaire de test de créneau -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Date et heure de début
                                        </label>
                                        <input
                                            v-model="debutDateTime"
                                            type="datetime-local"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#4F46E5]/10"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Date et heure de fin
                                        </label>
                                        <input
                                            v-model="finDateTime"
                                            type="datetime-local"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#4F46E5]/10"
                                        />
                                    </div>

                                    <button
                                        type="button"
                                        @click="checkCreneau"
                                        :disabled="checkingDispo"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 hover:bg-slate-200 py-2.5 text-xs font-semibold text-slate-800 transition cursor-pointer disabled:opacity-50"
                                    >
                                        <Loader2 v-if="checkingDispo" :size="15" class="animate-spin text-[#4F46E5]" />
                                        <span>{{ checkingDispo ? 'Vérification...' : 'Vérifier la disponibilité' }}</span>
                                    </button>

                                    <!-- Message d'erreur vérification -->
                                    <div
                                        v-if="dispoError"
                                        class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 flex items-start gap-2"
                                    >
                                        <AlertCircle :size="15" class="shrink-0 mt-0.5" />
                                        <span>{{ dispoError }}</span>
                                    </div>

                                    <!-- Résultat du test -->
                                    <div
                                        v-if="dispoResult"
                                        class="rounded-2xl p-4 border transition-all"
                                        :class="dispoResult.disponible ? 'border-emerald-200 bg-emerald-50/70' : 'border-rose-200 bg-rose-50/70'"
                                    >
                                        <div
                                            class="flex items-center gap-2 font-semibold text-xs"
                                            :class="dispoResult.disponible ? 'text-emerald-800' : 'text-rose-800'"
                                        >
                                            <CheckCircle2 v-if="dispoResult.disponible" :size="16" class="text-emerald-600 shrink-0" />
                                            <AlertCircle v-else :size="16" class="text-rose-600 shrink-0" />
                                            <span>
                                                {{ dispoResult.disponible ? 'Créneau 100% disponible !' : 'Créneau déjà réservé' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bouton Réserver -->
                                <button
                                    type="button"
                                    @click="handleReserver"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-[#4F46E5] py-3.5 px-6 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 hover:bg-[#4338CA] active:scale-[0.98] transition cursor-pointer"
                                >
                                    <span>{{ isConnected ? 'Réserver cette salle' : 'Se connecter pour réserver' }}</span>
                                    <ArrowUpRight :size="18" />
                                </button>

                                <p class="text-center text-[11px] text-slate-400">
                                    Réservation instantanée sécurisée
                                </p>

                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </main>

        <Footer />
    </div>
</template>
