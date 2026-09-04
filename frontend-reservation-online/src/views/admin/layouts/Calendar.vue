<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminReservationsStore } from '@/store/adminReservations'
import { useAdminSallesStore } from '@/store/adminSalles'
import {
  Calendar as CalendarIcon,
  Clock,
  Building2,
  ChevronLeft,
  ChevronRight,
  Filter,
  User,
  Users,
  MapPin,
  CheckCircle2,
  XCircle,
  AlertCircle,
  Clock3,
  Check,
  Ban,
  Eye,
  RefreshCw,
  Loader2,
  X,
  Sparkles,
  Layers,
  ArrowRight,
} from 'lucide-vue-next'

const adminReservationsStore = useAdminReservationsStore()
const adminSallesStore = useAdminSallesStore()

// Mode d'affichage : 'semaine' | 'mois' | 'jour'
const viewMode = ref('semaine')

// Date de référence pour la navigation
const currentDate = ref(new Date())

// Filtres
const selectedSalleId = ref('')
const selectedStatus = ref('')
const colorScheme = ref('salle') // 'salle' | 'statut'

// Modale de détails d'une réservation
const selectedEvent = ref(null)

onMounted(async () => {
  await loadData()
})

const loadData = async () => {
  try {
    await Promise.all([
      adminReservationsStore.fetchReservations({ all: 'true' }),
      adminSallesStore.fetchSalles({ all: 'true' }),
    ])
  } catch (err) {
    console.error('Erreur chargement agenda:', err)
  }
}

const reservations = computed(() => adminReservationsStore.reservations || [])
const salles = computed(() => adminSallesStore.salles || [])
const isLoading = computed(() => adminReservationsStore.loading || adminSallesStore.loading)

// Palette de couleurs pour distinguer chaque salle
const SALLE_COLORS = [
  { bg: 'bg-indigo-50', border: 'border-indigo-200', text: 'text-indigo-800', bar: 'bg-[#4F46E5]', badge: 'bg-indigo-100 text-indigo-700' },
  { bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-800', bar: 'bg-emerald-500', badge: 'bg-emerald-100 text-emerald-700' },
  { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-800', bar: 'bg-amber-500', badge: 'bg-amber-100 text-amber-700' },
  { bg: 'bg-purple-50', border: 'border-purple-200', text: 'text-purple-800', bar: 'bg-purple-500', badge: 'bg-purple-100 text-purple-700' },
  { bg: 'bg-rose-50', border: 'border-rose-200', text: 'text-rose-800', bar: 'bg-rose-500', badge: 'bg-rose-100 text-rose-700' },
  { bg: 'bg-cyan-50', border: 'border-cyan-200', text: 'text-cyan-800', bar: 'bg-cyan-500', badge: 'bg-cyan-100 text-cyan-700' },
  { bg: 'bg-teal-50', border: 'border-teal-200', text: 'text-teal-800', bar: 'bg-teal-500', badge: 'bg-teal-100 text-teal-700' },
  { bg: 'bg-orange-50', border: 'border-orange-200', text: 'text-orange-800', bar: 'bg-orange-500', badge: 'bg-orange-100 text-orange-700' },
]

const getSalleColor = (salleId) => {
  const index = Math.abs(Number(salleId) || 0) % SALLE_COLORS.length
  return SALLE_COLORS[index]
}

const getStatusColor = (status) => {
  switch (status) {
    case 'confirmee':
      return { bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-800', bar: 'bg-emerald-500', badge: 'bg-emerald-100 text-emerald-700' }
    case 'en_attente':
      return { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-800', bar: 'bg-amber-500', badge: 'bg-amber-100 text-amber-700' }
    case 'rejetee':
      return { bg: 'bg-rose-50', border: 'border-rose-200', text: 'text-rose-800', bar: 'bg-rose-500', badge: 'bg-rose-100 text-rose-700' }
    case 'terminee':
      return { bg: 'bg-slate-100', border: 'border-slate-300', text: 'text-slate-800', bar: 'bg-slate-700', badge: 'bg-slate-200 text-slate-700' }
    default:
      return { bg: 'bg-slate-50', border: 'border-slate-200', text: 'text-slate-600', bar: 'bg-slate-400', badge: 'bg-slate-100 text-slate-600' }
  }
}

const getEventColor = (event) => {
  if (colorScheme.value === 'statut') {
    return getStatusColor(event.status)
  }
  return getSalleColor(event.salle_id)
}

// Calcul de la durée en texte clair (ex: 2h30)
const getDuration = (debutStr, finStr) => {
  if (!debutStr || !finStr) return ''
  const diffMs = new Date(finStr) - new Date(debutStr)
  if (diffMs <= 0) return '0 min'
  const totalMins = Math.round(diffMs / 60000)
  const h = Math.floor(totalMins / 60)
  const m = totalMins % 60
  if (h === 0) return `${m} min`
  if (m === 0) return `${h}h`
  return `${h}h${m < 10 ? '0' : ''}${m}`
}

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  try {
    return new Intl.DateTimeFormat('fr-FR', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateStr))
  } catch {
    return ''
  }
}

const formatDateLabel = (d) => {
  return new Intl.DateTimeFormat('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' }).format(d)
}

// Filtrage global des réservations
const filteredReservations = computed(() => {
  let list = reservations.value

  if (selectedSalleId.value) {
    list = list.filter((r) => String(r.salle_id) === String(selectedSalleId.value))
  }
  if (selectedStatus.value) {
    list = list.filter((r) => r.status === selectedStatus.value)
  }

  return list
})

// --- NAVIGATION TEMPORELLE ---
const goToToday = () => {
  currentDate.value = new Date()
}

const goPrevious = () => {
  const d = new Date(currentDate.value)
  if (viewMode.value === 'jour') {
    d.setDate(d.getDate() - 1)
  } else if (viewMode.value === 'semaine') {
    d.setDate(d.getDate() - 7)
  } else {
    d.setMonth(d.getMonth() - 1)
  }
  currentDate.value = d
}

const goNext = () => {
  const d = new Date(currentDate.value)
  if (viewMode.value === 'jour') {
    d.setDate(d.getDate() + 1)
  } else if (viewMode.value === 'semaine') {
    d.setDate(d.getDate() + 7)
  } else {
    d.setMonth(d.getMonth() + 1)
  }
  currentDate.value = d
}

// Titre de la période courante
const periodTitle = computed(() => {
  const d = currentDate.value
  if (viewMode.value === 'jour') {
    return new Intl.DateTimeFormat('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(d)
  } else if (viewMode.value === 'semaine') {
    const week = weekDays.value
    if (week.length === 0) return ''
    const start = week[0].date
    const end = week[6].date
    return `Semaine du ${start.getDate()} ${new Intl.DateTimeFormat('fr-FR', { month: 'short' }).format(start)} au ${end.getDate()} ${new Intl.DateTimeFormat('fr-FR', { month: 'short', year: 'numeric' }).format(end)}`
  } else {
    return new Intl.DateTimeFormat('fr-FR', { month: 'long', year: 'numeric' }).format(d)
  }
})

// --- CALCUL DES JOURS DE LA SEMAINE ---
const weekDays = computed(() => {
  const curr = new Date(currentDate.value)
  // Lundi = 1, Dimanche = 0 en JS (getDay)
  const day = curr.getDay()
  const diffToMonday = day === 0 ? -6 : 1 - day

  const monday = new Date(curr)
  monday.setDate(curr.getDate() + diffToMonday)
  monday.setHours(0, 0, 0, 0)

  const days = []
  for (let i = 0; i < 7; i++) {
    const d = new Date(monday)
    d.setDate(monday.getDate() + i)

    const isToday = d.toDateString() === new Date().toDateString()

    // Événements de ce jour
    const dayEvents = filteredReservations.value.filter((r) => {
      if (!r.date_heure_debut) return false
      const rDate = new Date(r.date_heure_debut)
      return rDate.toDateString() === d.toDateString()
    }).sort((a, b) => new Date(a.date_heure_debut) - new Date(b.date_heure_debut))

    days.push({
      date: d,
      name: new Intl.DateTimeFormat('fr-FR', { weekday: 'short' }).format(d),
      dayNumber: d.getDate(),
      isToday,
      events: dayEvents,
    })
  }

  return days
})

// --- CALCUL DU MOIS (VUE MOIS) ---
const monthDays = computed(() => {
  const curr = new Date(currentDate.value)
  const year = curr.getFullYear()
  const month = curr.getMonth()

  const firstDayOfMonth = new Date(year, month, 1)
  const lastDayOfMonth = new Date(year, month + 1, 0)

  // Trouver le lundi précédant le 1er du mois
  const firstDayWeekday = firstDayOfMonth.getDay()
  const diffToMonday = firstDayWeekday === 0 ? -6 : 1 - firstDayWeekday

  const startDate = new Date(firstDayOfMonth)
  startDate.setDate(firstDayOfMonth.getDate() + diffToMonday)

  const grid = []
  const currentIter = new Date(startDate)

  // 6 semaines x 7 jours = 42 cases
  for (let i = 0; i < 35; i++) {
    const isCurrentMonth = currentIter.getMonth() === month
    const isToday = currentIter.toDateString() === new Date().toDateString()

    const dayEvents = filteredReservations.value.filter((r) => {
      if (!r.date_heure_debut) return false
      return new Date(r.date_heure_debut).toDateString() === currentIter.toDateString()
    }).sort((a, b) => new Date(a.date_heure_debut) - new Date(b.date_heure_debut))

    grid.push({
      date: new Date(currentIter),
      dayNumber: currentIter.getDate(),
      isCurrentMonth,
      isToday,
      events: dayEvents,
    })

    currentIter.setDate(currentIter.getDate() + 1)
  }

  return grid
})

// --- VUE JOUR : ÉVÉNEMENTS DU JOUR COURANT ---
const dayEvents = computed(() => {
  const target = new Date(currentDate.value).toDateString()
  return filteredReservations.value
    .filter((r) => r.date_heure_debut && new Date(r.date_heure_debut).toDateString() === target)
    .sort((a, b) => new Date(a.date_heure_debut) - new Date(b.date_heure_debut))
})
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-7xl space-y-6 pb-12">
      <!-- EN-TÊTE DE L'AGENDA -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3.5 py-1 text-xs font-semibold text-[#4F46E5] mb-2">
            <CalendarIcon :size="13" />
            <span>Planning & Occupation des Salles</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#0F172A]">
            Agenda des Salles
          </h1>
          <p class="mt-1 text-xs sm:text-sm text-slate-500">
            Visualisez les créneaux occupés, les durées, les salles réservées et leurs disponibilités.
          </p>
        </div>

        <!-- ACTIONS & VUES -->
        <div class="flex flex-wrap items-center gap-2.5">
          <!-- Vues : Semaine, Mois, Jour -->
          <div class="flex items-center rounded-2xl bg-white p-1 border border-slate-200/80 shadow-xs">
            <button
              type="button"
              @click="viewMode = 'semaine'"
              class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="viewMode === 'semaine' ? 'bg-[#4F46E5] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
            >
              Semaine
            </button>
            <button
              type="button"
              @click="viewMode = 'mois'"
              class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="viewMode === 'mois' ? 'bg-[#4F46E5] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
            >
              Mois
            </button>
            <button
              type="button"
              @click="viewMode = 'jour'"
              class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="viewMode === 'jour' ? 'bg-[#4F46E5] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
            >
              Jour
            </button>
          </div>

          <!-- Bouton Aujourd'hui -->
          <button
            type="button"
            @click="goToToday"
            class="rounded-2xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition cursor-pointer"
          >
            Aujourd'hui
          </button>

          <!-- Actualiser -->
          <button
            type="button"
            @click="loadData"
            :disabled="isLoading"
            class="p-2 rounded-2xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition cursor-pointer disabled:opacity-50"
            title="Actualiser"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': isLoading }" />
          </button>
        </div>
      </div>

      <!-- BARRE DE CONTRÔLE : NAVIGATION & FILTRES -->
      <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 rounded-3xl border border-slate-200/80 bg-white p-4 shadow-xs">
        <!-- Flèches de navigation et titre de la période -->
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1">
            <button
              type="button"
              @click="goPrevious"
              class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 transition cursor-pointer"
            >
              <ChevronLeft :size="16" />
            </button>
            <button
              type="button"
              @click="goNext"
              class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 transition cursor-pointer"
            >
              <ChevronRight :size="16" />
            </button>
          </div>

          <span class="text-sm sm:text-base font-black text-[#0F172A] capitalize">
            {{ periodTitle }}
          </span>
        </div>

        <!-- Filtres : Salle, Statut, Coloration -->
        <div class="flex flex-wrap items-center gap-3">
          <!-- Filtre Salle -->
          <div class="flex items-center gap-1.5">
            <Building2 :size="14" class="text-slate-400 shrink-0" />
            <select
              v-model="selectedSalleId"
              class="rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-2.5 text-xs font-medium text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-hidden"
            >
              <option value="">Toutes les salles</option>
              <option v-for="s in salles" :key="s.id" :value="s.id">
                {{ s.nom }}
              </option>
            </select>
          </div>

          <!-- Filtre Statut -->
          <div class="flex items-center gap-1.5">
            <Filter :size="14" class="text-slate-400 shrink-0" />
            <select
              v-model="selectedStatus"
              class="rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-2.5 text-xs font-medium text-slate-800 focus:border-[#4F46E5] focus:bg-white focus:outline-hidden"
            >
              <option value="">Tous les statuts</option>
              <option value="confirmee">Confirmée</option>
              <option value="en_attente">En attente</option>
              <option value="terminee">Terminée</option>
              <option value="rejetee">Rejetée</option>
            </select>
          </div>

          <!-- Mode de coloration -->
          <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-[11px] font-bold">
            <button
              type="button"
              @click="colorScheme = 'salle'"
              class="px-2 py-1 rounded-lg transition"
              :class="colorScheme === 'salle' ? 'bg-white text-[#0F172A] shadow-xs' : 'text-slate-500'"
            >
              Par Salle
            </button>
            <button
              type="button"
              @click="colorScheme = 'statut'"
              class="px-2 py-1 rounded-lg transition"
              :class="colorScheme === 'statut' ? 'bg-white text-[#0F172A] shadow-xs' : 'text-slate-500'"
            >
              Par Statut
            </button>
          </div>
        </div>
      </div>

      <!-- LÉGENDE DES COULEURS DES SALLES (si colorScheme === 'salle') -->
      <div v-if="colorScheme === 'salle' && salles.length > 0" class="flex flex-wrap items-center gap-3 px-2">
        <span class="text-xs font-bold text-slate-400">Légende salles :</span>
        <div
          v-for="s in salles"
          :key="s.id"
          class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"
        >
          <span class="h-3 w-3 rounded-full" :class="getSalleColor(s.id).bar"></span>
          <span>{{ s.nom }}</span>
        </div>
      </div>

      <!-- LÉGENDE DES STATUTS (si colorScheme === 'statut') -->
      <div v-else class="flex flex-wrap items-center gap-4 px-2 text-xs font-medium">
        <span class="font-bold text-slate-400">Légende statuts :</span>
        <span class="inline-flex items-center gap-1.5 text-emerald-700">
          <span class="h-3 w-3 rounded-full bg-emerald-500"></span> Confirmée
        </span>
        <span class="inline-flex items-center gap-1.5 text-amber-700">
          <span class="h-3 w-3 rounded-full bg-amber-500"></span> En attente
        </span>
        <span class="inline-flex items-center gap-1.5 text-slate-700">
          <span class="h-3 w-3 rounded-full bg-slate-700"></span> Terminée
        </span>
        <span class="inline-flex items-center gap-1.5 text-rose-700">
          <span class="h-3 w-3 rounded-full bg-rose-500"></span> Rejetée
        </span>
      </div>

      <!-- ========================================================================= -->
      <!-- VUE 1 : AGENDA DE LA SEMAINE                                              -->
      <!-- ========================================================================= -->
      <div v-if="viewMode === 'semaine'" class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs">
        <!-- Grille des 7 jours en colonnes -->
        <div class="grid grid-cols-1 md:grid-cols-7 divide-y md:divide-y-0 md:divide-x divide-slate-200/80">
          <div
            v-for="day in weekDays"
            :key="day.date.toISOString()"
            class="min-h-[420px] flex flex-col"
            :class="day.isToday ? 'bg-indigo-50/20' : 'bg-white'"
          >
            <!-- En-tête du jour -->
            <div
              class="p-3 border-b border-slate-100 text-center"
              :class="day.isToday ? 'bg-indigo-50/60' : 'bg-slate-50/60'"
            >
              <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                {{ day.name }}
              </span>
              <span
                class="inline-flex h-7 w-7 items-center justify-center rounded-full text-sm font-black mt-1"
                :class="day.isToday ? 'bg-[#4F46E5] text-white shadow-xs' : 'text-slate-800'"
              >
                {{ day.dayNumber }}
              </span>
            </div>

            <!-- Liste des occupations / événements de ce jour -->
            <div class="flex-1 p-2 space-y-2 overflow-y-auto max-h-[550px]">
              <div
                v-for="ev in day.events"
                :key="ev.id"
                @click="selectedEvent = ev"
                class="group relative cursor-pointer rounded-2xl border p-2.5 transition-all duration-200 hover:shadow-md hover:scale-[1.01]"
                :class="[getEventColor(ev).bg, getEventColor(ev).border]"
              >
                <!-- Barre d'accentuation latérale -->
                <div
                  class="absolute left-0 top-2 bottom-2 w-1 rounded-r-md"
                  :class="getEventColor(ev).bar"
                ></div>

                <div class="pl-1.5 space-y-1">
                  <!-- Horaires et Durée -->
                  <div class="flex items-center justify-between text-[11px]">
                    <span class="font-bold flex items-center gap-1 text-slate-800">
                      <Clock :size="11" class="text-slate-400" />
                      {{ formatTime(ev.date_heure_debut) }} - {{ formatTime(ev.date_heure_fin) }}
                    </span>
                    <span class="font-black px-1.5 py-0.5 rounded text-[10px]" :class="getEventColor(ev).badge">
                      {{ getDuration(ev.date_heure_debut, ev.date_heure_fin) }}
                    </span>
                  </div>

                  <!-- Nom de la salle -->
                  <h4 class="text-xs font-bold text-slate-900 truncate">
                    {{ ev.salle?.nom || 'Salle #' + ev.salle_id }}
                  </h4>

                  <!-- Client -->
                  <p class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                    <User :size="10" />
                    <span>{{ ev.nom_affiche || ev.user?.nom || 'Client' }}</span>
                  </p>

                  <!-- Statut badge discret -->
                  <div class="pt-1 flex items-center justify-between text-[10px]">
                    <span class="capitalize text-slate-500 font-medium">
                      {{ ev.status }}
                    </span>
                    <span class="text-[#4F46E5] opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5 font-bold">
                      Voir <Eye :size="10" />
                    </span>
                  </div>
                </div>
              </div>

              <!-- Si aucune occupation ce jour -->
              <div
                v-if="day.events.length === 0"
                class="flex h-32 items-center justify-center text-[11px] text-slate-300 italic"
              >
                Créneaux libres
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- VUE 2 : GRILLE DU MOIS                                                    -->
      <!-- ========================================================================= -->
      <div v-else-if="viewMode === 'mois'" class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs">
        <!-- Noms des jours (Lundi à Dimanche) -->
        <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center py-2.5 text-xs font-bold text-slate-500 uppercase">
          <div>Lun</div>
          <div>Mar</div>
          <div>Mer</div>
          <div>Jeu</div>
          <div>Ven</div>
          <div>Sam</div>
          <div>Dim</div>
        </div>

        <!-- Cases des jours -->
        <div class="grid grid-cols-7 divide-x divide-y divide-slate-100">
          <div
            v-for="cell in monthDays"
            :key="cell.date.toISOString()"
            class="min-h-[110px] p-2 flex flex-col justify-between transition-colors"
            :class="[
              cell.isCurrentMonth ? 'bg-white' : 'bg-slate-50/50 text-slate-300',
              cell.isToday ? 'bg-indigo-50/30' : '',
            ]"
          >
            <!-- Numéro du jour -->
            <div class="flex items-center justify-between">
              <span
                class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold"
                :class="cell.isToday ? 'bg-[#4F46E5] text-white shadow-xs' : cell.isCurrentMonth ? 'text-slate-800' : 'text-slate-400'"
              >
                {{ cell.dayNumber }}
              </span>

              <span
                v-if="cell.events.length > 0"
                class="text-[10px] font-bold text-[#4F46E5] bg-indigo-50 px-1.5 py-0.5 rounded-full"
              >
                {{ cell.events.length }}
              </span>
            </div>

            <!-- Événements (pastilles) -->
            <div class="mt-1 space-y-1 overflow-y-auto max-h-20">
              <div
                v-for="ev in cell.events.slice(0, 3)"
                :key="ev.id"
                @click="selectedEvent = ev"
                class="group cursor-pointer rounded-lg p-1 text-[10px] truncate border font-medium transition hover:scale-102"
                :class="[getEventColor(ev).bg, getEventColor(ev).border, getEventColor(ev).text]"
              >
                <strong>{{ formatTime(ev.date_heure_debut) }}</strong> {{ ev.salle?.nom || 'Salle' }}
              </div>

              <div
                v-if="cell.events.length > 3"
                class="text-[9px] text-slate-400 text-center font-bold"
              >
                +{{ cell.events.length - 3 }} autre(s)
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- VUE 3 : CHRONOLOGIE DE LA JOURNÉE                                         -->
      <!-- ========================================================================= -->
      <div v-else class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <h3 class="text-base font-bold text-slate-900">
            Occupations prévues pour le {{ formatDateLabel(currentDate) }}
          </h3>
          <span class="text-xs font-bold text-[#4F46E5] bg-indigo-50 px-3 py-1 rounded-full">
            {{ dayEvents.length }} réservation(s) ce jour
          </span>
        </div>

        <div v-if="dayEvents.length === 0" class="py-16 text-center text-slate-400 text-sm">
          Aucune occupation programmée sur cette journée.
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="ev in dayEvents"
            :key="ev.id"
            @click="selectedEvent = ev"
            class="group cursor-pointer flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl border transition-all hover:shadow-md"
            :class="[getEventColor(ev).bg, getEventColor(ev).border]"
          >
            <div class="flex items-center gap-4">
              <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl text-white font-black text-xs shrink-0 shadow-xs"
                :class="getEventColor(ev).bar"
              >
                {{ getDuration(ev.date_heure_debut, ev.date_heure_fin) }}
              </div>

              <div>
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                  <span>{{ ev.salle?.nom }}</span>
                  <span class="text-xs font-normal text-slate-500">({{ ev.salle?.localisation }})</span>
                </h4>
                <p class="text-xs text-slate-600 flex items-center gap-3 mt-1">
                  <span class="font-bold flex items-center gap-1">
                    <Clock :size="12" class="text-slate-400" />
                    {{ formatTime(ev.date_heure_debut) }} - {{ formatTime(ev.date_heure_fin) }}
                  </span>
                  <span class="flex items-center gap-1">
                    <User :size="12" class="text-slate-400" />
                    {{ ev.nom_affiche || ev.user?.nom }}
                  </span>
                  <span class="flex items-center gap-1">
                    <Users :size="12" class="text-slate-400" />
                    {{ ev.nombre_personnes }} pers.
                  </span>
                </p>
              </div>
            </div>

            <div class="flex items-center gap-3 self-end sm:self-auto">
              <span class="rounded-full px-3 py-1 text-xs font-bold capitalize" :class="getEventColor(ev).badge">
                {{ ev.status }}
              </span>
              <button
                type="button"
                class="p-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50"
              >
                <Eye :size="14" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODALE INSPECTION D'UN CRÉNEAU OCCUPÉ                                     -->
    <!-- ========================================================================= -->
    <div
      v-if="selectedEvent"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
    >
      <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <div class="flex items-center gap-2.5">
            <span class="h-3 w-3 rounded-full" :class="getEventColor(selectedEvent).bar"></span>
            <div>
              <span class="text-[11px] font-bold text-[#4F46E5] uppercase">Fiche Occupation</span>
              <h3 class="text-base font-black text-[#0F172A]">
                Réservation #{{ selectedEvent.id }} — {{ selectedEvent.salle?.nom }}
              </h3>
            </div>
          </div>
          <button
            type="button"
            @click="selectedEvent = null"
            class="p-1 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600"
          >
            <X :size="20" />
          </button>
        </div>

        <div class="mt-4 space-y-3.5 text-xs">
          <!-- Créneau & Durée -->
          <div class="grid grid-cols-2 gap-2">
            <div class="rounded-xl bg-slate-50 p-3">
              <span class="text-slate-400">Horaires</span>
              <p class="font-bold text-slate-900 mt-0.5">
                {{ formatTime(selectedEvent.date_heure_debut) }} à {{ formatTime(selectedEvent.date_heure_fin) }}
              </p>
            </div>
            <div class="rounded-xl bg-slate-50 p-3">
              <span class="text-slate-400">Durée calculée</span>
              <p class="font-black text-[#4F46E5] mt-0.5">
                {{ getDuration(selectedEvent.date_heure_debut, selectedEvent.date_heure_fin) }}
              </p>
            </div>
          </div>

          <!-- Salle & Capacité -->
          <div class="rounded-xl bg-slate-50 p-3 space-y-1">
            <div class="flex justify-between">
              <span class="text-slate-400">Salle</span>
              <span class="font-bold text-slate-800">{{ selectedEvent.salle?.nom }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">Localisation</span>
              <span class="text-slate-700">{{ selectedEvent.salle?.localisation || 'Standard' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">Participants prévus</span>
              <span class="font-bold text-slate-800">{{ selectedEvent.nombre_personnes }} personnes</span>
            </div>
          </div>

          <!-- Bénéficiaire -->
          <div class="rounded-xl bg-slate-50 p-3 space-y-1">
            <div class="flex justify-between">
              <span class="text-slate-400">Client / Demandeur</span>
              <span class="font-bold text-slate-900">
                {{ selectedEvent.nom_affiche || selectedEvent.user?.nom || 'Client externe' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">Téléphone</span>
              <span class="text-slate-700 font-medium">
                {{ selectedEvent.telephone_affiche || selectedEvent.telephone_client || 'Non renseigné' }}
              </span>
            </div>
            <div v-if="selectedEvent.user?.email" class="flex justify-between">
              <span class="text-slate-400">Email</span>
              <span class="text-[#4F46E5] font-medium">{{ selectedEvent.user.email }}</span>
            </div>
          </div>

          <!-- Statut -->
          <div class="rounded-xl p-3 flex items-center justify-between" :class="getEventColor(selectedEvent).bg">
            <span class="font-bold" :class="getEventColor(selectedEvent).text">Statut de la réservation</span>
            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold capitalize" :class="getEventColor(selectedEvent).badge">
              {{ selectedEvent.status }}
            </span>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <RouterLink
            :to="{ name: 'info-reservation', params: { id: selectedEvent.id } }"
            class="inline-flex items-center gap-1.5 rounded-xl bg-[#4F46E5] px-4 py-2 text-xs font-bold text-white hover:bg-[#4338CA] transition"
          >
            <span>Dossier complet</span>
            <ArrowRight :size="13" />
          </RouterLink>
          <button
            type="button"
            @click="selectedEvent = null"
            class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
