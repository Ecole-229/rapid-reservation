<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminEquipementsStore } from '@/store/adminEquipements'
import {
  ArrowLeft,
  Pencil,
  Package,
  CheckCircle2,
  AlertCircle,
  Loader2,
  ArrowUpRight,
  Sparkles,
  ShieldCheck,
  Calendar,
  Layers,
} from 'lucide-vue-next'

const route = useRoute()
const adminEquipementsStore = useAdminEquipementsStore()

const equipementId = route.params.id
const equipement = ref(null)
const isFetching = ref(true)

const defaultPlaceholder =
  'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=80'

onMounted(async () => {
  try {
    isFetching.value = true
    equipement.value = await adminEquipementsStore.fetchEquipement(equipementId)
  } catch (error) {
    console.error('Erreur chargement équipement :', error)
  } finally {
    isFetching.value = false
  }
})

const isDisponible = computed(() => {
  return (
    equipement.value &&
    (!equipement.value.status || equipement.value.status.toLowerCase() === 'disponible') &&
    equipement.value.stock_total > 0
  )
})

const activeImage = computed(() => {
  return equipement.value?.image_url || equipement.value?.image || defaultPlaceholder
})

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(dateString))
  } catch {
    return dateString
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-[1180px] text-[#151515]">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <RouterLink
          :to="{ name: 'admin-equipments' }"
          class="inline-flex items-center gap-2 text-xs font-semibold text-[#777] transition hover:text-[#191919]"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des équipements</span>
        </RouterLink>

        <div v-if="equipement" class="flex items-center gap-3">
          <RouterLink
            :to="{ name: 'update-equipment', params: { id: equipementId } }"
            class="inline-flex items-center gap-2 rounded-xl border border-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-widest text-neutral-900 transition hover:bg-neutral-900 hover:text-white"
          >
            <Pencil :size="13" />
            <span>Modifier</span>
          </RouterLink>
        </div>
      </div>

      <!-- CHARGEMENT -->
      <div
        v-if="isFetching"
        class="flex min-h-[420px] items-center justify-center rounded-[15px] border border-[#ecebe7] bg-white"
      >
        <div class="flex flex-col items-center gap-3">
          <Loader2 :size="34" class="animate-spin text-slate-800" />
          <p class="text-sm text-[#777]">Chargement des détails de l'équipement...</p>
        </div>
      </div>

      <!-- ERREUR -->
      <div
        v-else-if="adminEquipementsStore.errorMessage && !equipement"
        class="rounded-[15px] border border-[#eaded9] bg-white p-10 text-center"
      >
        <AlertCircle :size="42" class="mx-auto mb-3 text-slate-800" />
        <h2 class="text-xl font-semibold text-[#191919]">Équipement introuvable</h2>
        <p class="mt-2 text-sm text-[#777]">{{ adminEquipementsStore.errorMessage }}</p>
        <RouterLink
          :to="{ name: 'admin-equipments' }"
          class="mt-6 inline-flex items-center gap-2 rounded-[9px] bg-[#191919] px-5 py-3 text-xs font-semibold text-white transition hover:bg-black"
        >
          Retourner au catalogue des équipements
        </RouterLink>
      </div>

      <!-- FICHE ÉQUIPEMENT : STYLE SALLEINFOSUSER -->
      <div v-else-if="equipement">
        <section class="overflow-hidden rounded-[15px] border border-[#ecebe7] bg-white shadow-sm">
          <div class="grid min-h-[465px] grid-cols-1 lg:grid-cols-[1.06fr_0.98fr_1fr]">
            <!-- IMAGE GAUCHE -->
            <div class="relative min-h-[390px] overflow-hidden bg-[#e9e8e4] lg:min-h-0">
              <img
                :src="activeImage"
                :alt="equipement.nom"
                class="absolute inset-0 h-full w-full object-cover"
              />

              <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/0 to-black/5"></div>

              <div class="absolute left-5 top-5">
                <div
                  class="inline-flex items-center gap-1.5 rounded-full border border-white/35 bg-white/15 px-3 py-1.5 text-[11px] font-medium text-white backdrop-blur-md"
                >
                  <Sparkles :size="12" />
                  <span>Fiche Matériel</span>
                </div>
              </div>

              <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-4">
                <div>
                  <p class="text-[11px] font-medium uppercase tracking-[0.12em] text-white/70">
                    {{ isDisponible ? 'Disponible' : 'Actuellement indisponible' }}
                  </p>
                  <p class="mt-1 text-xl font-semibold leading-tight text-white">
                    {{ equipement.nom }}
                  </p>
                </div>

                <div
                  class="shrink-0 rounded-full px-3 py-1.5 text-[10px] font-semibold text-white backdrop-blur-md"
                  :class="isDisponible ? 'bg-emerald-500/90' : 'bg-rose-500/90'"
                >
                  {{ isDisponible ? 'Disponible' : (equipement.stock_total <= 0 ? 'Rupture' : 'Indisponible') }}
                </div>
              </div>
            </div>

            <!-- CENTRE : HIÉRARCHIE ÉDITORIALE -->
            <div class="flex flex-col justify-between border-b border-[#ecebe7] px-7 py-9 sm:px-10 lg:border-b-0 lg:border-r lg:border-[#ecebe7]">
              <div>
                <p class="text-[13px] font-medium text-[#7b7b7b]">Fiche équipement</p>

                <h1 class="mt-5 max-w-[320px] font-serif text-[42px] leading-[0.98] tracking-[-0.04em] text-[#191919]">
                  {{ equipement.nom }}
                </h1>

                <div class="mt-4 flex items-start gap-2 text-[13px] leading-5 text-[#777]">
                  <Calendar :size="15" class="mt-0.5 shrink-0 text-slate-800" />
                  <span>Enregistré le {{ formatDate(equipement.created_at) }}</span>
                </div>

                <div class="mt-10 flex items-end gap-2">
                  <span class="font-serif text-[44px] leading-none tracking-[-0.04em] text-[#000000]">
                    Description
                  </span>
                </div>

                <p class="mt-3 max-w-[280px] text-[13px] leading-6 text-[#777]">
                  {{ equipement.description || "Aucune description détaillée n'est renseignée pour cet équipement." }}
                </p>
              </div>

              <div class="mt-10 space-y-2.5">
                <RouterLink
                  :to="{ name: 'update-equipment', params: { id: equipementId } }"
                  class="inline-flex w-full items-center justify-between rounded-xl border border-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-widest text-neutral-900 transition hover:bg-neutral-900 hover:text-white"
                >
                  <span>Modifier l'équipement</span>
                  <ArrowUpRight :size="15" />
                </RouterLink>

                <RouterLink
                  :to="{ name: 'admin-equipments' }"
                  class="inline-flex w-full items-center justify-center rounded-xl border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-widest text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900"
                >
                  Annuler / Retour
                </RouterLink>
              </div>
            </div>

            <!-- DROITE : CARACTÉRISTIQUES & ADMIN SPECS -->
            <div class="flex flex-col justify-between px-7 py-9 sm:px-10">
              <div>
                <p class="text-[12px] font-medium text-slate-800">Ce qui est inclus</p>

                <div class="mt-6 space-y-5">
                  <div class="flex gap-3 border-b border-[#efeee9] pb-4">
                    <Package :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div>
                      <p class="text-[13px] font-medium text-[#5d5d5d]">Quantité en stock</p>
                      <p class="mt-1 text-[15px] font-medium text-[#222]">
                        {{ equipement.stock_total || 0 }} unités
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 border-b border-[#efeee9] pb-4">
                    <CheckCircle2 :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div>
                      <p class="text-[13px] font-medium text-[#5d5d5d]">Disponibilité</p>
                      <p
                        class="mt-1 text-[15px] font-medium capitalize"
                        :class="isDisponible ? 'text-[#2f9967]' : 'text-[#c65757]'"
                      >
                        {{ equipement.status || (isDisponible ? 'Disponible' : 'Indisponible') }}
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 border-b border-[#efeee9] pb-4">
                    <ShieldCheck :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div>
                      <p class="text-[13px] font-medium text-[#5d5d5d]">État du matériel</p>
                      <p class="mt-1 text-[15px] font-medium text-[#222]">
                        Certifié conforme
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3">
                    <CheckCircle2 :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div>
                      <p class="text-[13px] font-medium text-[#5d5d5d]">Réservations associées</p>
                      <p class="mt-1 text-[15px] font-medium text-[#222]">
                        {{ equipement.reservations_count ?? 0 }} fois
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- DÉTAILS ADMINISTRATIFS -->
              <div class="mt-9 border-t border-[#ecebe7] pt-6">
                <div class="flex items-center gap-2 mb-3">
                  <Layers :size="16" class="text-slate-800" />
                  <h2 class="text-[12px] font-semibold uppercase tracking-[0.08em] text-[#222]">
                    Données d'inventaire
                  </h2>
                </div>

                <div class="space-y-2 rounded-[8px] border border-[#deddd9] bg-[#fafaf8] p-3 text-[11px]">
                  <div class="flex items-center justify-between text-[#777]">
                    <span>Identifiant</span>
                    <span class="font-semibold text-[#191919]">#{{ equipement.id }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[#777]">
                    <span>Créé le</span>
                    <span class="font-semibold text-[#191919">{{ formatDate(equipement.created_at) }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[#777]">
                    <span>Modifié le</span>
                    <span class="font-semibold text-[#191919]">{{ formatDate(equipement.updated_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </AppAdmin>
</template>
