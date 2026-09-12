<script setup>
import { ref, onMounted } from 'vue'
import {
  Search,
  ChevronDown,
  ArrowDownWideNarrow,
  ArrowUpWideNarrow,
  DoorOpen,
} from 'lucide-vue-next'
import { useAdminSallesStore } from '@/store/adminSalles'

const emit = defineEmits([
  'search',
  'status-change',
  'salle-change',
  'sort-change',
])

const adminSallesStore = useAdminSallesStore()

const search = ref('')
const selectedStatus = ref('')
const selectedSalle = ref('')
const isStatusOpen = ref(false)
const isSalleOpen = ref(false)
const sortDescending = ref(true)

const statuses = [
  { label: 'Tous les statuts', value: '' },
  { label: 'En attente', value: 'en_attente' },
  { label: 'Confirmée', value: 'confirmee' },
  { label: 'Terminée', value: 'terminee' },
  { label: 'Rejetée', value: 'rejetee' },
]

onMounted(async () => {
  if (adminSallesStore.salles.length === 0) {
    try {
      await adminSallesStore.fetchSalles({ all: 'true' })
    } catch (e) {
      console.error('Erreur chargement des salles pour filtres réservations', e)
    }
  }
})

const handleSearch = () => {
  emit('search', search.value)
}

const selectStatus = (status) => {
  selectedStatus.value = status.value
  isStatusOpen.value = false
  emit('status-change', status.value)
}

const selectSalle = (salleId) => {
  selectedSalle.value = salleId
  isSalleOpen.value = false
  emit('salle-change', salleId)
}

const toggleSort = () => {
  sortDescending.value = !sortDescending.value
  emit('sort-change', sortDescending.value)
}

const getSelectedSalleLabel = () => {
  if (!selectedSalle.value) return 'Toutes les salles'
  const salle = adminSallesStore.salles.find((s) => s.id === Number(selectedSalle.value))
  return salle ? salle.nom : 'Salle sélectionnée'
}
</script>

<template>
  <div
    class="w-full rounded-[16px] border border-[#E2E8F0] bg-white p-4 shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
  >
    <div class="flex w-full flex-wrap items-center gap-4 sm:flex-nowrap">

      <!-- ========================= -->
      <!-- RECHERCHE -->
      <!-- ========================= -->
      <div class="relative min-w-[220px] flex-1">
        <Search
          :size="18"
          :stroke-width="1.8"
          class="absolute left-4 top-1/2 -translate-y-1/2 text-[#64748B]"
        />
        <input
          v-model="search"
          type="text"
          placeholder="Rechercher par client, téléphone, salle..."
          class="h-[44px] w-full rounded-[10px] border border-[#E2E8F0] bg-white pl-11 pr-4 text-[14px] text-[#0F172A] outline-none placeholder:text-[#94A3B8] transition focus:border-[#4F46E5] focus:ring-4 focus:ring-[#4F46E5]/10"
          @input="handleSearch"
        />
      </div>

      <!-- ========================= -->
      <!-- FILTRE STATUT -->
      <!-- ========================= -->
      <div class="relative w-[190px]">
        <button
          type="button"
          class="flex h-[44px] w-full items-center justify-between rounded-[10px] border border-[#E2E8F0] bg-white px-4 text-[14px] font-medium text-[#0F172A] outline-none transition-colors duration-200 hover:bg-[#F8FAFC]"
          @click="isStatusOpen = !isStatusOpen; isSalleOpen = false"
        >
          <span>{{ statuses.find((s) => s.value === selectedStatus)?.label }}</span>
          <ChevronDown
            :size="17"
            :stroke-width="1.8"
            class="text-[#64748B] transition-transform duration-200"
            :class="{ 'rotate-180': isStatusOpen }"
          />
        </button>

        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 -translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-1"
        >
          <div
            v-if="isStatusOpen"
            class="absolute left-0 top-[50px] z-50 w-full overflow-hidden rounded-[10px] border border-[#E2E8F0] bg-white shadow-[0_4px_20px_-4px_rgba(15,23,42,0.12)]"
          >
            <button
              v-for="status in statuses"
              :key="status.value"
              type="button"
              class="flex h-[44px] w-full items-center px-4 text-left text-[14px] transition-colors duration-200 hover:bg-[#EEF2FF]"
              :class="
                selectedStatus === status.value
                  ? 'bg-[#EEF2FF] font-medium text-[#3730A3]'
                  : 'text-[#475569]'
              "
              @click="selectStatus(status)"
            >
              {{ status.label }}
            </button>
          </div>
        </Transition>
      </div>

      <!-- ========================= -->
      <!-- FILTRE SALLE -->
      <!-- ========================= -->
      <div class="relative w-[210px]">
        <button
          type="button"
          class="flex h-[44px] w-full items-center justify-between rounded-[10px] border border-[#E2E8F0] bg-white px-4 text-[14px] font-medium text-[#0F172A] outline-none transition-colors duration-200 hover:bg-[#F8FAFC]"
          @click="isSalleOpen = !isSalleOpen; isStatusOpen = false"
        >
          <div class="flex items-center gap-2 truncate">
            <DoorOpen :size="16" class="text-gray-400 shrink-0" />
            <span class="truncate">{{ getSelectedSalleLabel() }}</span>
          </div>
          <ChevronDown
            :size="17"
            :stroke-width="1.8"
            class="text-[#64748B] transition-transform duration-200 shrink-0 ml-1"
            :class="{ 'rotate-180': isSalleOpen }"
          />
        </button>

        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 -translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-1"
        >
          <div
            v-if="isSalleOpen"
            class="absolute left-0 top-[50px] z-50 max-h-60 w-full overflow-y-auto rounded-[10px] border border-[#E2E8F0] bg-white shadow-[0_4px_20px_-4px_rgba(15,23,42,0.12)]"
          >
            <button
              type="button"
              class="flex h-[40px] w-full items-center px-4 text-left text-[14px] transition-colors duration-200 hover:bg-[#EEF2FF]"
              :class="!selectedSalle ? 'bg-[#EEF2FF] font-medium text-[#3730A3]' : 'text-[#475569]'"
              @click="selectSalle('')"
            >
              Toutes les salles
            </button>
            <button
              v-for="salle in adminSallesStore.salles"
              :key="salle.id"
              type="button"
              class="flex h-[40px] w-full items-center px-4 text-left text-[14px] transition-colors duration-200 hover:bg-[#EEF2FF]"
              :class="
                selectedSalle === String(salle.id)
                  ? 'bg-[#EEF2FF] font-medium text-[#3730A3]'
                  : 'text-[#475569]'
              "
              @click="selectSalle(String(salle.id))"
            >
              {{ salle.nom }}
            </button>
          </div>
        </Transition>
      </div>

      <!-- ========================= -->
      <!-- TRI / CLASSEMENT -->
      <!-- ========================= -->
      <button
        type="button"
        class="flex h-[44px] min-w-[190px] items-center justify-between rounded-[10px] border border-[#E2E8F0] bg-white px-4 text-[14px] font-medium text-[#0F172A] transition-colors duration-200 hover:bg-[#F8FAFC] active:scale-[0.98]"
        @click="toggleSort"
      >
        <div class="flex items-center gap-3">
          <ArrowDownWideNarrow
            v-if="sortDescending"
            :size="18"
            :stroke-width="1.8"
            class="text-[#4F46E5]"
          />
          <ArrowUpWideNarrow
            v-else
            :size="18"
            :stroke-width="1.8"
            class="text-[#4F46E5]"
          />
          <span>{{ sortDescending ? 'Ordre décroissant' : 'Ordre croissant' }}</span>
        </div>
        <ChevronDown :size="17" :stroke-width="1.8" class="text-[#64748B]" />
      </button>

    </div>
  </div>
</template>
