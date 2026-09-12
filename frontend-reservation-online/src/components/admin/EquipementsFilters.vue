<script setup>
import { ref } from 'vue'
import {
  Search,
  ChevronDown,
  ArrowDownWideNarrow,
  ArrowUpWideNarrow,
  SlidersHorizontal,
  X,
} from 'lucide-vue-next'

const emit = defineEmits([
  'search',
  'status-change',
  'sort-change',
  'stock-change',
])

const search = ref('')
const selectedStatus = ref('')
const isStatusOpen = ref(false)
const sortDescending = ref(true)

// Filtre avancé : stock minimum
const isAdvancedOpen = ref(false)
const minStock = ref('')

const statuses = [
  { label: 'Tous les statuts', value: '' },
  { label: 'Disponible', value: 'disponible' },
  { label: 'Indisponible', value: 'indisponible' },
]

const activeAdvancedCount = () => (minStock.value !== '' ? 1 : 0)

const handleSearch = () => {
  emit('search', search.value)
}

const selectStatus = (status) => {
  selectedStatus.value = status.value
  isStatusOpen.value = false
  emit('status-change', status.value)
}

const toggleSort = () => {
  sortDescending.value = !sortDescending.value
  emit('sort-change', sortDescending.value)
}

const applyStockFilter = () => {
  emit('stock-change', minStock.value !== '' ? Number(minStock.value) : null)
}

const resetAdvanced = () => {
  minStock.value = ''
  emit('stock-change', null)
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
          placeholder="Rechercher un équipement, description..."
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
          @click="isStatusOpen = !isStatusOpen; isAdvancedOpen = false"
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
      <!-- FILTRES AVANCÉS (Stock) -->
      <!-- ========================= -->
      <div class="relative w-[190px]">
        <button
          type="button"
          class="flex h-[44px] w-full items-center justify-between rounded-[10px] border px-4 text-[14px] font-medium outline-none transition-colors duration-200"
          :class="
            isAdvancedOpen || activeAdvancedCount() > 0
              ? 'border-[#4F46E5] bg-[#EEF2FF] text-[#3730A3]'
              : 'border-[#E2E8F0] bg-white text-[#0F172A] hover:bg-[#F8FAFC]'
          "
          @click="isAdvancedOpen = !isAdvancedOpen; isStatusOpen = false"
        >
          <div class="flex items-center gap-2">
            <SlidersHorizontal :size="16" :stroke-width="1.8" />
            <span>Stock min</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span
              v-if="activeAdvancedCount() > 0"
              class="flex h-5 w-5 items-center justify-center rounded-full bg-[#4F46E5] text-[11px] font-bold text-white"
            >
              {{ activeAdvancedCount() }}
            </span>
            <ChevronDown
              :size="17"
              :stroke-width="1.8"
              class="transition-transform duration-200"
              :class="{
                'rotate-180': isAdvancedOpen,
                'text-[#4F46E5]': isAdvancedOpen || activeAdvancedCount() > 0,
                'text-[#64748B]': !isAdvancedOpen && activeAdvancedCount() === 0,
              }"
            />
          </div>
        </button>

        <!-- DROPDOWN FILTRE STOCK -->
        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 -translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-1"
        >
          <div
            v-if="isAdvancedOpen"
            class="absolute left-0 top-[50px] z-50 w-[260px] overflow-hidden rounded-[12px] border border-[#C7D2FE] bg-white shadow-[0_8px_30px_-4px_rgba(79,70,229,0.15)]"
          >
            <!-- Header dropdown -->
            <div class="flex items-center justify-between border-b border-[#E2E8F0] bg-[#F5F3FF] px-4 py-3">
              <span class="text-[13px] font-semibold text-[#3730A3]">Filtrer par quantité</span>
              <button
                v-if="activeAdvancedCount() > 0"
                type="button"
                class="flex items-center gap-1 rounded-lg px-2 py-1 text-[12px] font-medium text-[#6366F1] transition hover:bg-[#EEF2FF]"
                @click="resetAdvanced"
              >
                <X :size="11" />
                Effacer
              </button>
            </div>

            <div class="space-y-4 p-4">
              <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-[#6366F1]">
                  📦 Stock minimum en inventaire
                </label>
                <input
                  v-model="minStock"
                  type="number"
                  min="0"
                  placeholder="Ex: 5"
                  class="h-[38px] w-full rounded-[8px] border border-[#E2E8F0] bg-[#F8FAFC] px-3 text-[13px] text-gray-800 outline-none transition focus:border-[#4F46E5] focus:bg-white focus:ring-2 focus:ring-[#4F46E5]/10"
                  @change="applyStockFilter"
                />
              </div>

              <button
                type="button"
                class="w-full rounded-[8px] bg-[#4F46E5] py-2 text-[13px] font-semibold text-white transition hover:bg-[#4338CA] active:scale-[0.98]"
                @click="applyStockFilter(); isAdvancedOpen = false"
              >
                Appliquer
              </button>
            </div>
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
