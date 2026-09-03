<script setup>
/**
 * UserCreateReservation.vue
 * Formulaire multi-step de réservation utilisateur
 *
 * STEP 1 — Salle     : choisir une salle, créneau, nb. de personnes
 * STEP 2 — Équipements: ajouter optionnellement des équipements + quantités
 * STEP 3 — Récapitulatif & soumission vers POST /api/reservations
 */
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import { useSallesStore } from '@/store/salles'
import { useEquipementsStore } from '@/store/equipements'
import { useReservationsStore } from '@/store/reservations'
import {
    ArrowLeft,
    ArrowRight,
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
    Sparkles,
    BadgeCheck,
    ChevronRight,
} from 'lucide-vue-next'

// ─── Stores ────────────────────────────────────────────────────────────────
const sallesStore = useSallesStore()
const equipementsStore = useEquipementsStore()
const reservationsStore = useReservationsStore()

// ─── Router ────────────────────────────────────────────────────────────────
const route = useRoute()
const router = useRouter()

// ─── State ─────────────────────────────────────────────────────────────────
const currentStep = ref(1)
const STEPS = [
    { id: 1, label: 'La salle', icon: Building2 },
    { id: 2, label: 'Équipements', icon: Package },
    { id: 3, label: 'Récapitulatif', icon: ClipboardCheck },
]

// ── Step 1 : Salle
const selectedSalleId = ref(route.query.salle_id ? Number(route.query.salle_id) : null)
const debutDateTime = ref(route.query.debut || '')
const finDateTime = ref(route.query.fin || '')
const nombrePersonnes = ref(1)
const step1Error = ref(null)
const checkingDispo = ref(false)
const dispoResult = ref(null)

// ── Step 2 : Équipements (optionnel)
const selectedEquipements = ref([])   // [{equipement_id, quantity, nom, stock_total}]

// ── Step 3 : Soumission
const submitting = computed(() => reservationsStore.submitting)
const submitError = ref(null)
const submitSuccess = ref(false)
const newReservationId = ref(null)

// ─── Computed ───────────────────────────────────────────────────────────────
const salles = computed(() => sallesStore.salles)
const equipements = computed(() => equipementsStore.equipements)

const selectedSalle = computed(() =>
    salles.value.find((s) => s.id === selectedSalleId.value) ?? null
)

const salleCoverUrl = computed(() => {
    if (!selectedSalle.value) return ''
    const imgs = selectedSalle.value.images
    if (imgs && imgs.length > 0) return imgs[0].url || imgs[0].path || ''
    return 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=70'
})

const formatPrice = (p) =>
    p != null ? new Intl.NumberFormat('fr-FR').format(p) + ' FCFA' : 'N/A'

const formatDateTime = (dt) => {
    if (!dt) return ''
    try {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
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

// Equipements déjà sélectionnés (pour éviter de les re-ajouter)
const selectedEquipementIds = computed(() =>
    selectedEquipements.value.map((e) => e.equipement_id)
)

// ─── Initialisation ─────────────────────────────────────────────────────────
onMounted(async () => {
    // Initialiser les dates si pas déjà passées en query
    if (!debutDateTime.value || !finDateTime.value) {
        const tomorrow = new Date()
        tomorrow.setDate(tomorrow.getDate() + 1)
        const y = tomorrow.getFullYear()
        const m = String(tomorrow.getMonth() + 1).padStart(2, '0')
        const d = String(tomorrow.getDate()).padStart(2, '0')
        debutDateTime.value = `${y}-${m}-${d}T09:00`
        finDateTime.value = `${y}-${m}-${d}T12:00`
    }

    await Promise.all([
        sallesStore.fetchSalles(),
        equipementsStore.fetchEquipements(),
    ])
})

// Quand la salle change, on reset la dispo
watch(selectedSalleId, () => {
    dispoResult.value = null
    step1Error.value = null
})

// ─── Helpers de formatage datetime pour l'API ────────────────────────────────
const toApiDateTime = (dtLocal) =>
    dtLocal ? dtLocal.replace('T', ' ') + ':00' : ''

// ─── Step 1 : validation & vérification dispo ───────────────────────────────
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

const goStep2 = () => {
    step1Error.value = null
    if (!selectedSalleId.value) { step1Error.value = 'Choisissez une salle.'; return }
    if (!debutDateTime.value || !finDateTime.value) { step1Error.value = 'Renseignez les dates.'; return }
    if (new Date(debutDateTime.value) >= new Date(finDateTime.value)) { step1Error.value = 'Date de fin invalide.'; return }
    if (!nombrePersonnes.value || nombrePersonnes.value < 1) { step1Error.value = 'Nombre de personnes invalide.'; return }
    if (selectedSalle.value?.capacite && nombrePersonnes.value > selectedSalle.value.capacite) {
        step1Error.value = `Capacité maximale : ${selectedSalle.value.capacite} personnes.`
        return
    }
    currentStep.value = 2
}

// ─── Step 2 : gestion équipements ───────────────────────────────────────────
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

// ─── Step 3 : récap + soumission ────────────────────────────────────────────
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
    } catch (e) {
        // Affiche l'erreur backend (422 validation, 409 conflit, etc.)
        submitError.value =
            reservationsStore.errorMessage ||
            Object.values(reservationsStore.validationErrors).flat().join(' — ') ||
            'Une erreur est survenue.'
    }
}
</script>

<template>
    <div class="min-h-screen bg-[#F8FAFC] flex flex-col">
        <NavBar />

        <main class="flex-1 px-4 py-10 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">

                <!-- RETOUR -->
                <div class="mb-8">
                    <RouterLink
                        :to="{ name: 'salles' }"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        <ArrowLeft :size="15" />
                        <span>Retour au catalogue</span>
                    </RouterLink>
                </div>

                <!-- TITRE -->
                <div class="mb-8">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-[#EEF2FF] px-3 py-1 text-xs font-semibold text-[#4F46E5] mb-3">
                        <Sparkles :size="13" />
                        <span>Nouvelle réservation</span>
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-[#0F172A] sm:text-3xl">
                        Réserver en quelques étapes
                    </h1>
                    <p class="mt-1.5 text-sm text-slate-500">
                        Choisissez votre salle, ajoutez des équipements, puis confirmez.
                    </p>
                </div>

                <!-- ============================================================ -->
                <!-- STEPPER                                                        -->
                <!-- ============================================================ -->
                <div class="mb-10 flex items-center gap-0">
                    <template v-for="(step, idx) in STEPS" :key="step.id">
                        <button
                            type="button"
                            class="flex items-center gap-2.5 cursor-default"
                            :class="currentStep >= step.id ? 'opacity-100' : 'opacity-40'"
                        >
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-all"
                                :class="
                                    currentStep > step.id
                                        ? 'bg-emerald-500 text-white'
                                        : currentStep === step.id
                                        ? 'bg-[#4F46E5] text-white shadow-md shadow-indigo-300'
                                        : 'bg-slate-200 text-slate-500'
                                "
                            >
                                <CheckCircle2 v-if="currentStep > step.id" :size="17" />
                                <component v-else :is="step.icon" :size="16" />
                            </div>
                            <span class="hidden text-xs font-semibold sm:block"
                                :class="currentStep === step.id ? 'text-[#4F46E5]' : 'text-slate-400'"
                            >
                                {{ step.label }}
                            </span>
                        </button>
                        <div
                            v-if="idx < STEPS.length - 1"
                            class="mx-3 h-px flex-1 transition-all"
                            :class="currentStep > step.id ? 'bg-emerald-400' : 'bg-slate-200'"
                        ></div>
                    </template>
                </div>

                <!-- ============================================================ -->
                <!-- STEP 1 — SALLE                                                 -->
                <!-- ============================================================ -->
                <div v-if="currentStep === 1" class="space-y-6">

                    <!-- Choix de la salle -->
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                        <h2 class="mb-5 text-base font-bold text-[#0F172A] flex items-center gap-2">
                            <Building2 :size="18" class="text-[#4F46E5]" />
                            Choisissez votre salle
                        </h2>

                        <!-- Loading salles -->
                        <div v-if="sallesStore.loading" class="flex items-center gap-3 text-sm text-slate-500 py-4">
                            <Loader2 :size="18" class="animate-spin text-[#4F46E5]" />
                            <span>Chargement des salles...</span>
                        </div>

                        <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label
                                v-for="salle in salles"
                                :key="salle.id"
                                class="relative flex cursor-pointer items-start gap-3 rounded-2xl border-2 p-4 transition-all"
                                :class="
                                    selectedSalleId === salle.id
                                        ? 'border-[#4F46E5] bg-[#EEF2FF]/60 shadow-sm'
                                        : 'border-slate-200 hover:border-slate-300'
                                "
                            >
                                <input
                                    type="radio"
                                    :value="salle.id"
                                    v-model="selectedSalleId"
                                    class="sr-only"
                                />

                                <!-- Mini image -->
                                <div class="h-16 w-20 shrink-0 overflow-hidden rounded-xl bg-slate-100">
                                    <img
                                        :src="(salle.images && salle.images[0]?.url) || 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=200&q=60'"
                                        class="h-full w-full object-cover"
                                        :alt="salle.nom"
                                    />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-[#0F172A] truncate">{{ salle.nom }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500 flex items-center gap-1">
                                        <MapPin :size="12" class="text-[#4F46E5]" />
                                        {{ salle.localisation || 'Sur site' }}
                                    </p>
                                    <div class="mt-1.5 flex flex-wrap gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                            <Users :size="10" /> {{ salle.capacite }} pers.
                                        </span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">
                                            {{ formatPrice(salle.prix) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Coche sélection -->
                                <CheckCircle2
                                    v-if="selectedSalleId === salle.id"
                                    :size="20"
                                    class="shrink-0 text-[#4F46E5]"
                                />
                            </label>
                        </div>
                    </div>

                    <!-- Créneau & nb personnes -->
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                        <h2 class="mb-5 text-base font-bold text-[#0F172A] flex items-center gap-2">
                            <Calendar :size="18" class="text-[#4F46E5]" />
                            Créneau & informations
                        </h2>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Date et heure de début *
                                </label>
                                <input
                                    v-model="debutDateTime"
                                    type="datetime-local"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#4F46E5]/10"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Date et heure de fin *
                                </label>
                                <input
                                    v-model="finDateTime"
                                    type="datetime-local"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#4F46E5]/10"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nombre de personnes *
                                </label>
                                <input
                                    v-model.number="nombrePersonnes"
                                    type="number"
                                    min="1"
                                    :max="selectedSalle?.capacite || 999"
                                    placeholder="Ex: 10"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#4F46E5]/10"
                                />
                                <p v-if="selectedSalle?.capacite" class="mt-1 text-[11px] text-slate-400">
                                    Capacité max. : {{ selectedSalle.capacite }} personnes
                                </p>
                            </div>

                            <div class="flex items-end">
                                <button
                                    type="button"
                                    @click="verifierDisponibilite"
                                    :disabled="checkingDispo"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 hover:bg-slate-200 py-2.5 text-xs font-semibold text-slate-800 transition cursor-pointer disabled:opacity-50"
                                >
                                    <Loader2 v-if="checkingDispo" :size="15" class="animate-spin" />
                                    <span>{{ checkingDispo ? 'Vérification...' : 'Vérifier la disponibilité' }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Résultat dispo -->
                        <div
                            v-if="dispoResult"
                            class="mt-4 rounded-2xl p-3.5 border flex items-center gap-2.5"
                            :class="dispoResult.disponible ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50'"
                        >
                            <CheckCircle2 v-if="dispoResult.disponible" :size="18" class="text-emerald-600 shrink-0" />
                            <AlertCircle v-else :size="18" class="text-rose-600 shrink-0" />
                            <p class="text-xs font-semibold" :class="dispoResult.disponible ? 'text-emerald-800' : 'text-rose-800'">
                                {{ dispoResult.disponible ? 'Créneau disponible — vous pouvez continuer.' : 'Ce créneau est déjà réservé. Choisissez une autre plage horaire.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Erreur step 1 -->
                    <div v-if="step1Error" class="flex items-start gap-2 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-700">
                        <AlertCircle :size="16" class="shrink-0 mt-0.5" />
                        <span>{{ step1Error }}</span>
                    </div>

                    <!-- Bouton suivant -->
                    <div class="flex justify-end">
                        <button
                            type="button"
                            @click="goStep2"
                            class="inline-flex items-center gap-2 rounded-2xl bg-[#4F46E5] px-7 py-3 text-sm font-bold text-white shadow-md shadow-indigo-300/40 hover:bg-[#4338CA] active:scale-[0.98] transition cursor-pointer"
                        >
                            <span>Continuer</span>
                            <ArrowRight :size="17" />
                        </button>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- STEP 2 — ÉQUIPEMENTS                                           -->
                <!-- ============================================================ -->
                <div v-if="currentStep === 2" class="space-y-6">

                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                        <h2 class="mb-1.5 text-base font-bold text-[#0F172A] flex items-center gap-2">
                            <Package :size="18" class="text-[#4F46E5]" />
                            Ajoutez des équipements (optionnel)
                        </h2>
                        <p class="mb-5 text-xs text-slate-500">Cliquez sur « + » pour ajouter un équipement à votre réservation.</p>

                        <!-- Loading -->
                        <div v-if="equipementsStore.loading" class="flex items-center gap-3 py-4 text-sm text-slate-500">
                            <Loader2 :size="18" class="animate-spin text-[#4F46E5]" />
                            <span>Chargement des équipements...</span>
                        </div>

                        <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div
                                v-for="eq in equipements"
                                :key="eq.id"
                                class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3.5 transition hover:border-slate-300"
                                :class="selectedEquipementIds.includes(eq.id) ? 'border-[#4F46E5]/40 bg-[#EEF2FF]/40' : ''"
                            >
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-[#4F46E5]">
                                    <Package :size="18" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#0F172A] truncate">{{ eq.nom }}</p>
                                    <p class="text-[11px] text-slate-400">
                                        Stock : {{ eq.stock_total || '—' }} dispo
                                    </p>
                                </div>

                                <!-- Bouton ajouter si pas encore sélectionné -->
                                <button
                                    v-if="!selectedEquipementIds.includes(eq.id)"
                                    type="button"
                                    @click="addEquipement(eq)"
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#4F46E5] text-white hover:bg-[#4338CA] active:scale-95 transition cursor-pointer"
                                    :title="'Ajouter ' + eq.nom"
                                >
                                    <Plus :size="16" />
                                </button>
                                <span v-else class="text-[11px] font-semibold text-[#4F46E5] shrink-0">✓ Ajouté</span>
                            </div>
                        </div>
                    </div>

                    <!-- Équipements sélectionnés + gestion quantité -->
                    <div
                        v-if="selectedEquipements.length > 0"
                        class="rounded-3xl border border-[#4F46E5]/20 bg-[#EEF2FF]/40 p-6 sm:p-8"
                    >
                        <h3 class="mb-4 text-sm font-bold text-[#4F46E5]">Équipements sélectionnés</h3>
                        <div class="space-y-3">
                            <div
                                v-for="item in selectedEquipements"
                                :key="item.equipement_id"
                                class="flex items-center gap-3 rounded-2xl bg-white border border-slate-200 p-3.5"
                            >
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-[#4F46E5]">
                                    <Package :size="16" />
                                </div>
                                <p class="flex-1 min-w-0 text-xs font-semibold text-[#0F172A] truncate">{{ item.nom }}</p>

                                <!-- Contrôle quantité -->
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="decrementQty(item)"
                                        class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 active:scale-95 transition cursor-pointer"
                                    >
                                        <Minus :size="13" />
                                    </button>
                                    <span class="w-6 text-center text-xs font-bold text-[#0F172A]">{{ item.quantity }}</span>
                                    <button
                                        type="button"
                                        @click="incrementQty(item)"
                                        class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 active:scale-95 transition cursor-pointer"
                                    >
                                        <Plus :size="13" />
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    @click="removeEquipement(item.equipement_id)"
                                    class="flex h-7 w-7 items-center justify-center rounded-full text-rose-400 hover:bg-rose-50 transition cursor-pointer"
                                    title="Retirer"
                                >
                                    <Trash2 :size="14" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between gap-4">
                        <button
                            type="button"
                            @click="currentStep = 1"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                        >
                            <ArrowLeft :size="16" />
                            <span>Retour</span>
                        </button>
                        <button
                            type="button"
                            @click="currentStep = 3"
                            class="inline-flex items-center gap-2 rounded-2xl bg-[#4F46E5] px-7 py-3 text-sm font-bold text-white shadow-md shadow-indigo-300/40 hover:bg-[#4338CA] active:scale-[0.98] transition cursor-pointer"
                        >
                            <span>Récapitulatif</span>
                            <ArrowRight :size="17" />
                        </button>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- STEP 3 — RÉCAPITULATIF & CONFIRMATION                         -->
                <!-- ============================================================ -->
                <div v-if="currentStep === 3" class="space-y-6">

                    <!-- Succès -->
                    <div
                        v-if="submitSuccess"
                        class="rounded-3xl border border-emerald-200 bg-emerald-50 p-10 text-center"
                    >
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 mb-4">
                            <BadgeCheck :size="36" class="text-emerald-600" />
                        </div>
                        <h2 class="text-xl font-extrabold text-emerald-900 mb-2">Réservation envoyée !</h2>
                        <p class="text-sm text-emerald-700 max-w-sm mx-auto">
                            Votre demande est en attente de confirmation. Vous serez notifié dès validation.
                        </p>
                        <div class="mt-6 flex justify-center gap-3">
                            <RouterLink
                                :to="{ name: 'salles' }"
                                class="rounded-xl border border-emerald-300 bg-white px-5 py-2.5 text-xs font-semibold text-emerald-800 hover:bg-emerald-50 transition"
                            >
                                Retour aux salles
                            </RouterLink>
                        </div>
                    </div>

                    <!-- Récapitulatif -->
                    <template v-else>
                        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                            <h2 class="mb-6 text-base font-bold text-[#0F172A] flex items-center gap-2">
                                <ClipboardCheck :size="18" class="text-[#4F46E5]" />
                                Récapitulatif de votre réservation
                            </h2>

                            <!-- Salle -->
                            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200">
                                <div class="flex items-stretch gap-0">
                                    <div class="h-28 w-28 shrink-0 overflow-hidden">
                                        <img
                                            :src="salleCoverUrl"
                                            :alt="selectedSalle?.nom"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>
                                    <div class="flex-1 p-4">
                                        <p class="text-xs font-semibold text-[#4F46E5] uppercase tracking-wider mb-0.5">Salle réservée</p>
                                        <p class="text-base font-extrabold text-[#0F172A]">{{ selectedSalle?.nom }}</p>
                                        <p class="mt-1 flex items-center gap-1 text-xs text-slate-500">
                                            <MapPin :size="12" class="text-[#4F46E5]" />
                                            {{ selectedSalle?.localisation || 'Sur site' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Détails -->
                            <dl class="space-y-3">
                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                    <dt class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                                        <Calendar :size="14" class="text-[#4F46E5]" />
                                        Début
                                    </dt>
                                    <dd class="text-xs font-bold text-[#0F172A]">{{ formatDateTime(debutDateTime) }}</dd>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                    <dt class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                                        <Calendar :size="14" class="text-[#4F46E5]" />
                                        Fin
                                    </dt>
                                    <dd class="text-xs font-bold text-[#0F172A]">{{ formatDateTime(finDateTime) }}</dd>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                    <dt class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                                        <Users :size="14" class="text-[#4F46E5]" />
                                        Nombre de personnes
                                    </dt>
                                    <dd class="text-xs font-bold text-[#0F172A]">{{ nombrePersonnes }}</dd>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                    <dt class="text-xs font-semibold text-slate-500">Durée estimée</dt>
                                    <dd class="text-xs font-bold text-[#0F172A]">{{ dureHeures.toFixed(1) }} heure(s)</dd>
                                </div>
                            </dl>

                            <!-- Équipements -->
                            <div v-if="selectedEquipements.length > 0" class="mt-5 pt-5 border-t border-slate-100">
                                <p class="mb-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Équipements</p>
                                <div class="space-y-2">
                                    <div
                                        v-for="item in selectedEquipements"
                                        :key="item.equipement_id"
                                        class="flex items-center justify-between rounded-xl bg-indigo-50/50 px-4 py-2.5"
                                    >
                                        <span class="flex items-center gap-2 text-xs font-semibold text-[#0F172A]">
                                            <Package :size="13" class="text-[#4F46E5]" />
                                            {{ item.nom }}
                                        </span>
                                        <span class="text-xs font-bold text-[#4F46E5]">× {{ item.quantity }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="mt-5 pt-5 border-t border-slate-100 text-xs text-slate-400 italic">
                                Aucun équipement supplémentaire sélectionné.
                            </div>
                        </div>

                        <!-- Erreur de soumission -->
                        <div v-if="submitError" class="flex items-start gap-2 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-700">
                            <AlertCircle :size="16" class="shrink-0 mt-0.5" />
                            <span>{{ submitError }}</span>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between gap-4">
                            <button
                                type="button"
                                @click="currentStep = 2"
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                            >
                                <ArrowLeft :size="16" />
                                <span>Retour</span>
                            </button>
                            <button
                                type="button"
                                @click="submitReservation"
                                :disabled="submitting"
                                class="inline-flex items-center gap-2 rounded-2xl bg-[#4F46E5] px-7 py-3 text-sm font-bold text-white shadow-md shadow-indigo-300/40 hover:bg-[#4338CA] active:scale-[0.98] transition cursor-pointer disabled:opacity-60"
                            >
                                <Loader2 v-if="submitting" :size="17" class="animate-spin" />
                                <span>{{ submitting ? 'Envoi...' : 'Confirmer la réservation' }}</span>
                                <ChevronRight v-if="!submitting" :size="17" />
                            </button>
                        </div>
                    </template>
                </div>

            </div>
        </main>

        <Footer />
    </div>
</template>
