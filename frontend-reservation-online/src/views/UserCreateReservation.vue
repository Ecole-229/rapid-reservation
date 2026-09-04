<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import { useSallesStore } from '@/store/salles'
import { useEquipementsStore } from '@/store/equipements'
import { useReservationsStore } from '@/store/reservations'
import {
    ArrowLeft,
    CheckCircle2,
    AlertCircle,
    Loader2,
    Building2,
    Package,
    ClipboardCheck,
    Calendar,
    Users,
    MapPin,
    Plus,
    Minus,
    Trash2,
    BadgeCheck,
    Clock,
    Sparkles,
} from 'lucide-vue-next'

// ─── Stores ─────────────────────────────────────────────────────────────────
const sallesStore = useSallesStore()
const equipementsStore = useEquipementsStore()
const reservationsStore = useReservationsStore()

// ─── Router ─────────────────────────────────────────────────────────────────
const route = useRoute()
const router = useRouter()

// ─── State ──────────────────────────────────────────────────────────────────
const currentStep = ref(1)
const STEPS = [
    { id: 1, label: 'La salle', icon: Building2 },
    { id: 2, label: 'Équipements', icon: Package },
    { id: 3, label: 'Récapitulatif', icon: ClipboardCheck },
]

// ── Step 1 : Salle & Dates
const selectedSalleId = ref(route.query.salle_id ? Number(route.query.salle_id) : null)
const isFixedSalle = computed(() => !!route.query.salle_id)
const directFetchedSalle = ref(null)
const debutDateTime = ref(route.query.debut || '')
const finDateTime = ref(route.query.fin || '')
const nombrePersonnes = ref(1)
const step1Error = ref(null)
const checkingDispo = ref(false)
const dispoResult = ref(null)

// ── Step 2 : Équipements (optionnel)
const selectedEquipements = ref([]) // [{equipement_id, quantity, nom, stock_total}]

// ── Step 3 : Soumission
const submitting = computed(() => reservationsStore.submitting)
const submitError = ref(null)
const submitSuccess = ref(false)
const newReservationId = ref(null)

// ─── Computed ───────────────────────────────────────────────────────────────
const salles = computed(() => sallesStore.salles || [])
const equipements = computed(() => equipementsStore.equipements || [])

const selectedSalle = computed(() => {
    if (!selectedSalleId.value) return null
    return (
        salles.value.find((s) => Number(s.id) === Number(selectedSalleId.value)) ||
        directFetchedSalle.value ||
        null
    )
})

const defaultPlaceholder =
    'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80'

const salleCoverUrl = computed(() => {
    if (!selectedSalle.value) return defaultPlaceholder
    const imgs = selectedSalle.value.images
    if (imgs && imgs.length > 0) {
        return imgs[0].url || imgs[0].path || defaultPlaceholder
    }
    return defaultPlaceholder
})

const formatPrice = (p) =>
    p != null ? new Intl.NumberFormat('fr-FR').format(p) + ' FCFA' : 'Sur demande'

const formatDateTime = (dt) => {
    if (!dt) return ''
    try {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dt.replace('T', ' ')))
    } catch {
        return dt
    }
}

// Durée en heures entre début et fin
const dureHeures = computed(() => {
    if (!debutDateTime.value || !finDateTime.value) return 0
    const diff = new Date(finDateTime.value) - new Date(debutDateTime.value)
    return Math.max(0, diff / 1000 / 3600)
})

// Équipements déjà sélectionnés
const selectedEquipementIds = computed(() =>
    selectedEquipements.value.map((e) => e.equipement_id)
)

// ─── Helpers API datetime ───────────────────────────────────────────────────
const toApiDateTime = (dtLocal) =>
    dtLocal ? dtLocal.replace('T', ' ') + ':00' : ''

// ─── Step 1 : validation & dispo ───────────────────────────────────────────
const verifierDisponibilite = async () => {
    step1Error.value = null
    dispoResult.value = null

    if (!selectedSalleId.value) {
        step1Error.value = 'Veuillez choisir une salle.'
        return
    }
    if (!debutDateTime.value || !finDateTime.value) {
        step1Error.value = 'Veuillez renseigner les dates de début et de fin.'
        return
    }
    if (new Date(debutDateTime.value) >= new Date(finDateTime.value)) {
        step1Error.value = 'La date de fin doit être après la date de début.'
        return
    }

    checkingDispo.value = true
    try {
        const result = await sallesStore.checkDisponibilite(
            selectedSalleId.value,
            toApiDateTime(debutDateTime.value),
            toApiDateTime(finDateTime.value)
        )
        dispoResult.value = result
    } catch (e) {
        step1Error.value = e.message || 'Erreur lors de la vérification.'
    } finally {
        checkingDispo.value = false
    }
}

// ─── Réactivité Query (arrive depuis Salle Info) ────────────────────────────
watch(
    () => route.query,
    async (query) => {
        if (query.salle_id) {
            selectedSalleId.value = Number(query.salle_id)
            try {
                directFetchedSalle.value = await sallesStore.fetchSalle(selectedSalleId.value)
            } catch (e) {
                console.warn('Erreur chargement direct salle:', e)
            }
        }
        if (query.debut) {
            debutDateTime.value = query.debut
        }
        if (query.fin) {
            finDateTime.value = query.fin
        }
        if (selectedSalleId.value && debutDateTime.value && finDateTime.value) {
            await verifierDisponibilite()
        }
    },
    { deep: true }
)

// Quand la salle change, réinitialiser l'état de dispo
watch(selectedSalleId, () => {
    dispoResult.value = null
    step1Error.value = null
})

// ─── Initialisation ─────────────────────────────────────────────────────────
onMounted(async () => {
    // Initialiser les dates si pas déjà fournies
    if (!debutDateTime.value || !finDateTime.value) {
        const tomorrow = new Date()
        tomorrow.setDate(tomorrow.getDate() + 1)
        const y = tomorrow.getFullYear()
        const m = String(tomorrow.getMonth() + 1).padStart(2, '0')
        const d = String(tomorrow.getDate()).padStart(2, '0')
        debutDateTime.value = `${y}-${m}-${d}T09:00`
        finDateTime.value = `${y}-${m}-${d}T12:00`
    }

    try {
        const promises = [
            sallesStore.fetchSalles(),
            equipementsStore.fetchEquipements(),
        ]
        if (selectedSalleId.value) {
            promises.push(sallesStore.fetchSalle(selectedSalleId.value))
        }
        const results = await Promise.all(promises)
        if (selectedSalleId.value && results[2]) {
            directFetchedSalle.value = results[2]
        }
    } catch (e) {
        console.error('Erreur chargement salles/équipements:', e)
    }

    // Si salle_id était déjà dans les queries, lancer la vérification
    if (selectedSalleId.value && debutDateTime.value && finDateTime.value) {
        await verifierDisponibilite()
    }
})

const goStep2 = () => {
    step1Error.value = null
    if (!selectedSalleId.value) {
        step1Error.value = 'Veuillez choisir une salle.'
        return
    }
    if (!debutDateTime.value || !finDateTime.value) {
        step1Error.value = 'Veuillez renseigner les dates de début et de fin.'
        return
    }
    if (new Date(debutDateTime.value) >= new Date(finDateTime.value)) {
        step1Error.value = 'La date de fin doit être postérieure à la date de début.'
        return
    }
    if (!nombrePersonnes.value || nombrePersonnes.value < 1) {
        step1Error.value = 'Le nombre de personnes doit être supérieur à 0.'
        return
    }
    if (selectedSalle.value?.capacite && nombrePersonnes.value > selectedSalle.value.capacite) {
        step1Error.value = `Cette salle a une capacité maximale de ${selectedSalle.value.capacite} personnes.`
        return
    }
    currentStep.value = 2
}

// ─── Step 2 : Équipements ───────────────────────────────────────────────────
const addEquipement = (eq) => {
    if (selectedEquipementIds.value.includes(eq.id)) return
    selectedEquipements.value.push({
        equipement_id: eq.id,
        nom: eq.nom,
        stock_total: eq.stock_total || 1,
        quantity: 1,
    })
}

const removeEquipement = (equipementId) => {
    selectedEquipements.value = selectedEquipements.value.filter(
        (e) => e.equipement_id !== equipementId
    )
}

const incrementQty = (item) => {
    if (item.quantity < item.stock_total) item.quantity++
}

const decrementQty = (item) => {
    if (item.quantity > 1) item.quantity--
}

// ─── Step 3 : Soumission ────────────────────────────────────────────────────
const submitReservation = async () => {
    submitError.value = null
    const payload = {
        salle_id: selectedSalleId.value,
        date_heure_debut: toApiDateTime(debutDateTime.value),
        date_heure_fin: toApiDateTime(finDateTime.value),
        nombre_personnes: nombrePersonnes.value,
    }

    if (selectedEquipements.value.length > 0) {
        payload.equipements = selectedEquipements.value.map((e) => ({
            equipement_id: e.equipement_id,
            quantity: e.quantity,
        }))
    }

    try {
        const result = await reservationsStore.createReservation(payload)
        newReservationId.value = result?.id ?? null
        submitSuccess.value = true
        if (result?.id) {
            router.push({ name: 'user-reservation-details', params: { id: result.id } })
        } else {
            router.push({ name: 'user-reservations' })
        }
    } catch (e) {
        submitError.value =
            reservationsStore.errorMessage ||
            Object.values(reservationsStore.validationErrors).flat().join(' — ') ||
            'Une erreur est survenue lors de la réservation.'
    }
}
</script>

<template>
    <div class="min-h-screen bg-[#080909] text-[#eee9dc] flex flex-col justify-between">
        <!-- VRAIE BARRE DE NAVIGATION COMMUNE DE L'APPLICATION -->
        <NavBar />

        <!-- CONTENU PRINCIPAL AVEC ESPACEMENT POUR LA NAVBAR FIXE -->
        <main class="flex-1 pt-28 pb-16 px-4 sm:px-6 lg:px-8 w-full max-w-[1240px] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-[1.05fr_0.95fr] gap-5 min-h-[640px]">
                <!-- GAUCHE : HERO / CARTE SALLE SÉLECTIONNÉE -->
                <section class="relative min-h-[480px] lg:min-h-full overflow-hidden rounded-[20px] border border-white/10 bg-[#141515] flex flex-col justify-between p-6 sm:p-8">
                    <!-- Image de fond avec overlay dégradé -->
                    <img
                        :src="salleCoverUrl"
                        :alt="selectedSalle?.nom || 'Salle'"
                        class="absolute inset-0 h-full w-full object-cover transition-opacity duration-500"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/30"></div>

                    <!-- En-tête de la carte gauche -->
                    <div class="relative z-10 flex items-center justify-between">
                        <RouterLink
                            :to="{ name: 'salles' }"
                            class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/40 px-3.5 py-1.5 text-[11px] uppercase tracking-[0.08em] text-[#dcd6c8] backdrop-blur-md transition hover:bg-white/10"
                        >
                            <ArrowLeft :size="13" />
                            <span>Retour aux salles</span>
                        </RouterLink>

                        <div class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] text-white/90 backdrop-blur-md">
                            <Sparkles :size="12" />
                            <span>Espace Professionnel</span>
                        </div>
                    </div>

                    <!-- Pied de la carte gauche : Infos de la salle -->
                    <div class="relative z-10 mt-auto pt-16">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <span class="rounded-full border border-white/20 bg-black/30 px-3 py-1 text-[10px] uppercase tracking-[0.12em] text-white/80 backdrop-blur-sm">
                                Réservation
                            </span>
                            <span
                                v-if="selectedSalle"
                                class="rounded-full border px-3 py-1 text-[10px] uppercase tracking-[0.12em] backdrop-blur-sm"
                                :class="selectedSalle.status && selectedSalle.status.toLowerCase() !== 'disponible'
                                    ? 'border-rose-300/35 bg-rose-500/20 text-rose-100'
                                    : 'border-emerald-300/35 bg-emerald-500/20 text-emerald-100'"
                            >
                                {{ selectedSalle.status || 'Disponible' }}
                            </span>
                        </div>

                        <h1 class="font-serif text-[42px] sm:text-[54px] leading-[0.95] tracking-[-0.03em] text-[#f0e9da]">
                            {{ selectedSalle ? selectedSalle.nom : 'RÉSERVER UNE SALLE' }}
                        </h1>

                        <div v-if="selectedSalle" class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] uppercase tracking-[0.08em] text-white/80">
                            <span class="inline-flex items-center gap-1.5">
                                <MapPin :size="13" class="text-[#d8cbb0]" />
                                {{ selectedSalle.localisation || 'Sur site' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <Users :size="13" class="text-[#d8cbb0]" />
                                {{ selectedSalle.capacite }} personnes max
                            </span>
                            <span class="text-[#d8cbb0] font-medium">
                                {{ formatPrice(selectedSalle.prix) }}
                            </span>
                        </div>
                        <p v-else class="mt-4 text-xs text-white/60">
                            Choisissez une salle dans le formulaire pour configurer votre événement.
                        </p>
                    </div>
                </section>

                <!-- DROITE : STEPPER ET FORMULAIRE -->
                <section class="flex flex-col justify-between rounded-[20px] border border-white/10 bg-[#0c0d0d] p-6 sm:p-10 shadow-2xl">
                    <div class="mx-auto w-full max-w-[540px]">
                        <!-- Stepper de progression -->
                        <div class="mb-8 flex items-center gap-2">
                            <template v-for="(step, idx) in STEPS" :key="step.id">
                                <button
                                    type="button"
                                    @click="currentStep >= step.id ? (currentStep = step.id) : null"
                                    class="flex items-center gap-2 text-[10px] uppercase tracking-[0.1em] transition"
                                    :class="currentStep === step.id ? 'text-[#ddd3bd]' : 'text-[#76736c] hover:text-[#aaa]'"
                                >
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full border text-[9px] font-semibold"
                                        :class="
                                            currentStep > step.id
                                                ? 'border-[#8a9b72] bg-[#8a9b72] text-[#101010]'
                                                : currentStep === step.id
                                                    ? 'border-[#9c927d] text-[#ddd3bd] bg-white/[0.04]'
                                                    : 'border-white/15 text-[#77736c]'
                                        "
                                    >
                                        <CheckCircle2 v-if="currentStep > step.id" :size="11" />
                                        <span v-else>{{ step.id }}</span>
                                    </span>
                                    <span class="hidden sm:inline">{{ step.label }}</span>
                                </button>
                                <div v-if="idx < STEPS.length - 1" class="h-px flex-1 bg-white/10"></div>
                            </template>
                        </div>

                        <!-- ÉTAPE 1 : SALLE & DATES -->
                        <div v-if="currentStep === 1">
                            <div class="text-center">
                                <div class="mb-3 flex items-center justify-center gap-2 text-white/35">
                                    <span class="h-px w-7 bg-white/20"></span>
                                    <span class="text-[14px]">◇</span>
                                    <span class="h-px w-7 bg-white/20"></span>
                                </div>
                                <h2 class="font-serif text-[32px] sm:text-[38px] uppercase leading-none tracking-[0.02em] text-[#e7dfcf]">
                                    Reservation
                                </h2>
                              
                            </div>

                            <div class="mt-8 space-y-5">
                                <!-- SI SALLE FIXÉE DEPUIS INFOSALLE : PAS D'AUTRE CHOIX -->
                                <div v-if="isFixedSalle">
                                    <div class="mb-2 flex items-center justify-between">
                                        <label class="block font-serif text-[12px] uppercase tracking-[0.04em] text-[#d8cfbe]">
                                            Salle à réservée
                                        </label>

                                    </div>

                                    <div v-if="selectedSalle" class="rounded-[11px] border border-[#cdbf9f]/35 bg-[#141515] p-4 shadow-lg">
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-[8px] border border-white/10 bg-[#181919]">
                                                <img :src="salleCoverUrl" :alt="selectedSalle.nom" class="h-full w-full object-cover" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="rounded-full bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 px-2 py-0.5 text-[9px] uppercase tracking-wider font-bold">
                                                        Salle sélectionnée
                                                    </span>
                                                    <span class="text-[11px] text-[#cdbf9f] font-semibold">
                                                        {{ formatPrice(selectedSalle.prix) }}
                                                    </span>
                                                </div>
                                                <p class="mt-1 text-[14px] font-medium text-[#f0e9da] truncate">
                                                    {{ selectedSalle.nom }}
                                                </p>
                                                <p class="text-[11px] text-[#8e8a82] flex items-center gap-1.5 mt-0.5 truncate">
                                                    <MapPin :size="11" />
                                                    <span>{{ selectedSalle.localisation || 'Sur site' }}</span>
                                                    <span>•</span>
                                                    <Users :size="11" />
                                                    <span>{{ selectedSalle.capacite }} pers. max</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="flex items-center gap-2 rounded-[11px] border border-white/10 bg-[#141515] p-4 text-xs text-[#8e8a82]">
                                        <Loader2 :size="14" class="animate-spin text-[#cdbf9f]" />
                                        <span>Chargement des détails de la salle...</span>
                                    </div>
                                </div>

                                <!-- SINON : SÉLECTION LIBRE DE LA SALLE -->
                                <div v-else>
                                    <label class="mb-2 block font-serif text-[12px] uppercase tracking-[0.04em] text-[#d8cfbe]">
                                        Choisir une salle
                                    </label>
                                    <select
                                        v-model="selectedSalleId"
                                        class="w-full rounded-[9px] border border-white/15 bg-[#121313] px-4 py-3.5 text-[13px] text-[#e0dad0] outline-none transition focus:border-[#cdbf9f]"
                                    >
                                        <option :value="null">Sélectionnez une salle</option>
                                        <option
                                            v-for="salle in salles"
                                            :key="salle.id"
                                            :value="Number(salle.id)"
                                        >
                                            {{ salle.nom }} — {{ formatPrice(salle.prix) }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Date début & fin -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block font-serif text-[12px] uppercase tracking-[0.04em] text-[#d8cfbe]">
                                            Début
                                        </label>
                                        <input
                                            v-model="debutDateTime"
                                            type="datetime-local"
                                            class="w-full rounded-[9px] border border-white/15 bg-[#121313] px-4 py-3 text-[13px] text-[#e0dad0] outline-none transition focus:border-[#cdbf9f]"
                                        />
                                    </div>

                                    <div>
                                        <label class="mb-2 block font-serif text-[12px] uppercase tracking-[0.04em] text-[#d8cfbe]">
                                            Fin
                                        </label>
                                        <input
                                            v-model="finDateTime"
                                            type="datetime-local"
                                            class="w-full rounded-[9px] border border-white/15 bg-[#121313] px-4 py-3 text-[13px] text-[#e0dad0] outline-none transition focus:border-[#cdbf9f]"
                                        />
                                    </div>
                                </div>

                                <!-- Nombre de personnes & Capacité -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block font-serif text-[12px] uppercase tracking-[0.04em] text-[#d8cfbe]">
                                            Nombre de personnes
                                        </label>
                                        <input
                                            v-model.number="nombrePersonnes"
                                            type="number"
                                            min="1"
                                            :max="selectedSalle?.capacite || 999"
                                            placeholder="Ex: 5"
                                            class="w-full rounded-[9px] border border-white/15 bg-[#121313] px-4 py-3 text-[13px] text-[#e0dad0] outline-none transition focus:border-[#cdbf9f]"
                                        />
                                    </div>

                                    <div>
                                        <label class="mb-2 block font-serif text-[12px] uppercase tracking-[0.04em] text-[#77736c]">
                                            Capacité max
                                        </label>
                                        <div class="flex h-[47px] items-center rounded-[9px] border border-white/10 bg-[#121313] px-4 text-[13px] text-[#cfc7b8]">
                                            {{ selectedSalle?.capacite ? `${selectedSalle.capacite} places` : '—' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Bouton Vérification Disponibilité -->
                                <div>
                                    <button
                                        type="button"
                                        @click="verifierDisponibilite"
                                        :disabled="checkingDispo"
                                        class="w-full rounded-[8px] border border-white/15 bg-[#18191a] py-3 text-[11px] uppercase tracking-[0.08em] text-[#c7c1b5] transition hover:bg-[#222324] disabled:opacity-50"
                                    >
                                        <span class="inline-flex items-center justify-center gap-2">
                                            <Loader2 v-if="checkingDispo" :size="14" class="animate-spin" />
                                            {{ checkingDispo ? 'Vérification en cours...' : 'Vérifier la disponibilité' }}
                                        </span>
                                    </button>
                                </div>

                                <!-- Feedback disponibilité -->
                                <div
                                    v-if="dispoResult"
                                    class="rounded-[9px] border p-3.5 text-[12px]"
                                    :class="dispoResult.disponible ? 'border-emerald-400/30 bg-emerald-400/[0.08] text-emerald-200' : 'border-rose-400/30 bg-rose-400/[0.08] text-rose-200'"
                                >
                                    <div class="flex items-center gap-2">
                                        <CheckCircle2 v-if="dispoResult.disponible" :size="16" />
                                        <AlertCircle v-else :size="16" />
                                        <span>
                                            {{ dispoResult.disponible ? 'Créneau 100% disponible ! Vous pouvez continuer.' : 'Ce créneau est déjà réservé. Veuillez choisir une autre plage.' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Erreur Step 1 -->
                                <div v-if="step1Error" class="rounded-[9px] border border-rose-400/25 bg-rose-400/[0.08] p-3.5 text-[12px] text-rose-200">
                                    <div class="flex items-start gap-2">
                                        <AlertCircle :size="15" class="mt-0.5 shrink-0" />
                                        <span>{{ step1Error }}</span>
                                    </div>
                                </div>

                                <!-- Bouton continuer -->
                                <button
                                    type="button"
                                    @click="goStep2"
                                    class="mt-2 w-full rounded-[9px] border border-[#cdbf9f] bg-[#d8cbb0] py-3.5 text-[11px] font-semibold uppercase tracking-[0.1em] text-[#171717] transition hover:bg-[#e4dbca]"
                                >
                                    Étape suivante : Équipements
                                </button>
                            </div>
                        </div>

                        <!-- ÉTAPE 2 : ÉQUIPEMENTS -->
                        <div v-else-if="currentStep === 2">
                            <div class="text-center">
                                <div class="mb-3 flex items-center justify-center gap-2 text-white/35">
                                    <span class="h-px w-7 bg-white/20"></span>
                                    <span class="text-[14px]">◇</span>
                                    <span class="h-px w-7 bg-white/20"></span>
                                </div>
                                <h2 class="font-serif text-[32px] sm:text-[38px] uppercase leading-none tracking-[0.02em] text-[#e7dfcf]">
                                    Équipements
                                </h2>
                                <p class="mx-auto mt-2.5 max-w-[420px] text-[13px] leading-relaxed text-[#96938d]">
                                    Ajoutez des équipements optionnels selon les besoins de votre événement.
                                </p>
                            </div>

                            <div class="mt-8 space-y-3">
                                <div v-if="equipementsStore.loading" class="rounded-[9px] border border-white/10 bg-[#121313] p-6 text-center text-[12px] text-[#77736c]">
                                    <Loader2 :size="18" class="mx-auto mb-2 animate-spin text-[#cdbf9f]" />
                                    Chargement des équipements...
                                </div>

                                <div
                                    v-else-if="equipements.length === 0"
                                    class="rounded-[9px] border border-white/10 bg-[#121313] p-6 text-center text-[12px] text-[#77736c]"
                                >
                                    Aucun équipement disponible pour le moment.
                                </div>

                                <div
                                    v-else
                                    v-for="eq in equipements"
                                    :key="eq.id"
                                    class="flex items-center gap-3.5 rounded-[9px] border border-white/10 bg-[#121313] p-3.5 transition"
                                    :class="selectedEquipementIds.includes(eq.id) ? 'border-[#cdbf9f]/40 bg-[#181919]' : ''"
                                >
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[7px] border border-white/10 text-[#bcb3a4]">
                                        <Package :size="16" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-[13px] text-[#ddd5c7]">{{ eq.nom }}</p>
                                        <p class="mt-0.5 text-[10px] uppercase tracking-[0.07em] text-[#736f68]">
                                            Stock : {{ eq.stock_total || 'Disponible' }}
                                        </p>
                                    </div>

                                    <button
                                        v-if="!selectedEquipementIds.includes(eq.id)"
                                        type="button"
                                        @click="addEquipement(eq)"
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-white/15 text-[#d6ccba] transition hover:bg-white/10"
                                        title="Ajouter cet équipement"
                                    >
                                        <Plus :size="14" />
                                    </button>
                                    <span v-else class="shrink-0 text-[10px] uppercase tracking-[0.08em] text-[#cdbf9f] font-medium">
                                        Ajouté
                                    </span>
                                </div>
                            </div>

                            <!-- Liste des équipements choisis -->
                            <div v-if="selectedEquipements.length > 0" class="mt-6 rounded-[9px] border border-white/10 bg-[#101111] p-4">
                                <p class="mb-2.5 text-[10px] uppercase tracking-[0.12em] text-[#8f897e]">Équipements retenus</p>
                                <div class="space-y-2">
                                    <div
                                        v-for="item in selectedEquipements"
                                        :key="item.equipement_id"
                                        class="flex items-center gap-3 rounded-[7px] border border-white/5 bg-[#161717] px-3 py-2.5"
                                    >
                                        <p class="min-w-0 flex-1 truncate text-[12px] text-[#ddd5c7]">{{ item.nom }}</p>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="decrementQty(item)"
                                                class="flex h-6 w-6 items-center justify-center rounded-full border border-white/10 text-[#aaa49b] hover:bg-white/10"
                                            >
                                                <Minus :size="11" />
                                            </button>
                                            <span class="w-5 text-center text-[11px] font-semibold text-[#ddd5c7]">{{ item.quantity }}</span>
                                            <button
                                                type="button"
                                                @click="incrementQty(item)"
                                                class="flex h-6 w-6 items-center justify-center rounded-full border border-white/10 text-[#aaa49b] hover:bg-white/10"
                                            >
                                                <Plus :size="11" />
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeEquipement(item.equipement_id)"
                                            class="flex h-6 w-6 items-center justify-center text-[#907770] hover:text-[#d38b7d]"
                                            title="Retirer"
                                        >
                                            <Trash2 :size="12" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-7 grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    @click="currentStep = 1"
                                    class="rounded-[8px] border border-white/15 bg-transparent py-3 text-[11px] uppercase tracking-[0.08em] text-[#a39e95] transition hover:bg-white/5"
                                >
                                    Retour
                                </button>
                                <button
                                    type="button"
                                    @click="currentStep = 3"
                                    class="rounded-[8px] border border-[#cdbf9f] bg-[#d8cbb0] py-3 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#171717] transition hover:bg-[#e4dbca]"
                                >
                                    Continuer
                                </button>
                            </div>
                        </div>

                        <!-- ÉTAPE 3 : RÉCAPITULATIF & SOUMISSION -->
                        <div v-else>
                            <div v-if="submitSuccess" class="text-center py-6">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-emerald-300/30 bg-emerald-400/10">
                                    <BadgeCheck :size="34" class="text-emerald-300" />
                                </div>
                                <h2 class="mt-5 font-serif text-[32px] uppercase tracking-[0.02em] text-[#e7dfcf]">
                                    Réservation envoyée
                                </h2>
                                <p class="mx-auto mt-3 max-w-[400px] text-[13px] leading-relaxed text-[#96938d]">
                                    Votre demande a été enregistrée avec succès. Notre équipe va examiner votre créneau.
                                </p>
                                <RouterLink
                                    :to="{ name: 'user-reservations' }"
                                    class="mt-7 inline-flex rounded-[8px] border border-white/15 px-6 py-3 text-[11px] uppercase tracking-[0.08em] text-[#d6cebf] transition hover:bg-white/10"
                                >
                                    Consulter mes réservations
                                </RouterLink>
                            </div>

                            <div v-else>
                                <div class="text-center">
                                    <div class="mb-3 flex items-center justify-center gap-2 text-white/35">
                                        <span class="h-px w-7 bg-white/20"></span>
                                        <span class="text-[14px]">◇</span>
                                        <span class="h-px w-7 bg-white/20"></span>
                                    </div>
                                    <h2 class="font-serif text-[32px] sm:text-[38px] uppercase leading-none tracking-[0.02em] text-[#e7dfcf]">
                                        Confirmation
                                    </h2>
                                    <p class="mx-auto mt-2.5 max-w-[420px] text-[13px] leading-relaxed text-[#96938d]">
                                        Vérifiez le récapitulatif de votre réservation avant de valider.
                                    </p>
                                </div>

                                <div class="mt-7 overflow-hidden rounded-[10px] border border-white/10 bg-[#121313]">
                                    <div class="flex items-stretch border-b border-white/10">
                                        <div class="h-[96px] w-[96px] shrink-0 overflow-hidden">
                                            <img :src="salleCoverUrl" :alt="selectedSalle?.nom" class="h-full w-full object-cover" />
                                        </div>
                                        <div class="min-w-0 flex-1 p-3.5">
                                            <p class="text-[9px] uppercase tracking-[0.1em] text-[#8d877c]">Salle choisie</p>
                                            <p class="mt-1 truncate text-[14px] font-medium text-[#e2dacd]">{{ selectedSalle?.nom || '—' }}</p>
                                            <p class="mt-1.5 flex items-center gap-1.5 truncate text-[11px] text-[#77736c]">
                                                <MapPin :size="12" />
                                                {{ selectedSalle?.localisation || 'Sur site' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <dl class="space-y-2.5 text-[11px]">
                                            <div class="flex items-center justify-between gap-4">
                                                <dt class="inline-flex items-center gap-1.5 uppercase tracking-[0.07em] text-[#77736c]">
                                                    <Calendar :size="12" /> Début
                                                </dt>
                                                <dd class="text-right text-[#c8c0b2]">{{ formatDateTime(debutDateTime) }}</dd>
                                            </div>
                                            <div class="flex items-center justify-between gap-4">
                                                <dt class="inline-flex items-center gap-1.5 uppercase tracking-[0.07em] text-[#77736c]">
                                                    <Calendar :size="12" /> Fin
                                                </dt>
                                                <dd class="text-right text-[#c8c0b2]">{{ formatDateTime(finDateTime) }}</dd>
                                            </div>
                                            <div class="flex items-center justify-between gap-4">
                                                <dt class="inline-flex items-center gap-1.5 uppercase tracking-[0.07em] text-[#77736c]">
                                                    <Users :size="12" /> Personnes
                                                </dt>
                                                <dd class="text-right text-[#c8c0b2]">{{ nombrePersonnes }}</dd>
                                            </div>
                                            <div class="flex items-center justify-between gap-4">
                                                <dt class="inline-flex items-center gap-1.5 uppercase tracking-[0.07em] text-[#77736c]">
                                                    <Clock :size="12" /> Durée
                                                </dt>
                                                <dd class="text-right text-[#c8c0b2]">{{ dureHeures.toFixed(1) }} heure(s)</dd>
                                            </div>
                                        </dl>

                                        <div v-if="selectedEquipements.length > 0" class="mt-4 border-t border-white/10 pt-3">
                                            <p class="mb-2 text-[10px] uppercase tracking-[0.1em] text-[#77736c]">Équipements inclus</p>
                                            <div class="space-y-1.5">
                                                <div
                                                    v-for="item in selectedEquipements"
                                                    :key="item.equipement_id"
                                                    class="flex items-center justify-between gap-3 rounded-[6px] border border-white/5 bg-[#171818] px-2.5 py-1.5 text-[11px]"
                                                >
                                                    <span class="truncate text-[#c8c0b2]">{{ item.nom }}</span>
                                                    <span class="shrink-0 text-[#cdbf9f] font-medium">× {{ item.quantity }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="submitError" class="mt-4 rounded-[8px] border border-rose-400/25 bg-rose-400/[0.08] p-3.5 text-[12px] text-rose-200">
                                    <div class="flex items-start gap-2">
                                        <AlertCircle :size="15" class="mt-0.5 shrink-0" />
                                        <span>{{ submitError }}</span>
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <button
                                        type="button"
                                        @click="currentStep = 2"
                                        class="rounded-[8px] border border-white/15 bg-transparent py-3 text-[11px] uppercase tracking-[0.08em] text-[#a39e95] transition hover:bg-white/5"
                                    >
                                        Retour
                                    </button>
                                    <button
                                        type="button"
                                        @click="submitReservation"
                                        :disabled="submitting"
                                        class="rounded-[8px] border border-[#cdbf9f] bg-[#d8cbb0] py-3 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#171717] transition hover:bg-[#e4dbca] disabled:opacity-55"
                                    >
                                        <span class="inline-flex items-center justify-center gap-2">
                                            <Loader2 v-if="submitting" :size="14" class="animate-spin" />
                                            {{ submitting ? 'Envoi...' : 'Confirmer la réservation' }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- FOOTER COMMUNE DE L'APPLICATION -->
        <Footer />
    </div>
</template>
