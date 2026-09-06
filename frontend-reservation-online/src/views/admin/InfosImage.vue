<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminImagesStore } from '@/store/adminImages'
import {
  ArrowLeft,
  Pencil,
  DoorOpen,
  Calendar,
  AlertCircle,
  Loader2,
  ExternalLink,
  ArrowUpRight,
  Sparkles,
  CheckCircle2,
  HardDrive,
  ImageIcon,
} from 'lucide-vue-next'

const route = useRoute()
const adminImagesStore = useAdminImagesStore()

const imageId = route.params.id
const image = ref(null)
const isFetching = ref(true)

const defaultPlaceholder =
  'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80'

onMounted(async () => {
  try {
    isFetching.value = true
    image.value = await adminImagesStore.fetchImage(imageId)
  } catch (error) {
    console.error('Erreur chargement image :', error)
  } finally {
    isFetching.value = false
  }
})

const activeImage = computed(() => {
  return image.value?.url || defaultPlaceholder
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
          :to="{ name: 'admin-galeries' }"
          class="inline-flex items-center gap-2 text-xs font-semibold text-[#777] transition hover:text-[#191919]"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la galerie</span>
        </RouterLink>

        <div v-if="image" class="flex items-center gap-3">
          <RouterLink
            :to="{ name: 'update-image', params: { id: imageId } }"
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
          <p class="text-sm text-[#777]">Chargement des détails de la photo...</p>
        </div>
      </div>

      <!-- ERREUR -->
      <div
        v-else-if="adminImagesStore.errorMessage && !image"
        class="rounded-[15px] border border-[#eaded9] bg-white p-10 text-center"
      >
        <AlertCircle :size="42" class="mx-auto mb-3 text-slate-800" />
        <h2 class="text-xl font-semibold text-[#191919]">Photo introuvable</h2>
        <p class="mt-2 text-sm text-[#777]">{{ adminImagesStore.errorMessage }}</p>
        <RouterLink
          :to="{ name: 'admin-galeries' }"
          class="mt-6 inline-flex items-center gap-2 rounded-[9px] bg-[#191919] px-5 py-3 text-xs font-semibold text-white transition hover:bg-black"
        >
          Retourner à la galerie
        </RouterLink>
      </div>

      <!-- FICHE IMAGE : STYLE SALLEINFOSUSER -->
      <div v-else-if="image">
        <section class="overflow-hidden rounded-[15px] border border-[#ecebe7] bg-white shadow-sm">
          <div class="grid min-h-[465px] grid-cols-1 lg:grid-cols-[1.06fr_0.98fr_1fr]">
            <!-- IMAGE GAUCHE -->
            <div class="relative min-h-[390px] overflow-hidden bg-[#181818] lg:min-h-0 flex items-center justify-center">
              <img
                :src="activeImage"
                :alt="image.nom"
                class="absolute inset-0 h-full w-full object-cover"
              />

              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-black/10"></div>

              <div class="absolute left-5 top-5">
                <div
                  class="inline-flex items-center gap-1.5 rounded-full border border-white/35 bg-white/15 px-3 py-1.5 text-[11px] font-medium text-white backdrop-blur-md"
                >
                  <Sparkles :size="12" />
                  <span>Média de Galerie</span>
                </div>
              </div>

              <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-4">
                <div>
                  <p class="text-[11px] font-medium uppercase tracking-[0.12em] text-white/70">
                    {{ image.salle?.nom || 'Salle #' + image.salle_id }}
                  </p>
                  <p class="mt-1 text-xl font-semibold leading-tight text-white line-clamp-1">
                    {{ image.nom }}
                  </p>
                </div>

                <a
                  v-if="image.url"
                  :href="image.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1.5 text-[10px] font-semibold text-white backdrop-blur-md transition hover:bg-white/30"
                >
                  <ExternalLink :size="12" />
                  <span>Plein écran</span>
                </a>
              </div>
            </div>

            <!-- CENTRE : HIÉRARCHIE ÉDITORIALE -->
            <div class="flex flex-col justify-between border-b border-[#ecebe7] px-7 py-9 sm:px-10 lg:border-b-0 lg:border-r lg:border-[#ecebe7]">
              <div>
                <p class="text-[13px] font-medium text-[#7b7b7b]">Média photographique</p>

                <h1 class="mt-5 max-w-[320px] font-serif text-[42px] leading-[0.98] tracking-[-0.04em] text-[#191919]">
                  {{ image.nom }}
                </h1>

                <div class="mt-4 flex items-start gap-2 text-[13px] leading-5 text-[#777]">
                  <DoorOpen :size="15" class="mt-0.5 shrink-0 text-slate-800" />
                  <span>Rattachée à : {{ image.salle?.nom || 'Salle #' + image.salle_id }}</span>
                </div>

                <div class="mt-10 flex items-end gap-2">
                  <span class="font-serif text-[44px] leading-none tracking-[-0.04em] text-[#000000]">
                    Désignation
                  </span>
                </div>

                <p class="mt-3 max-w-[280px] text-[13px] leading-6 text-[#777]">
                  {{ image.designation || 'Aucune désignation spécifique fournie pour cette photo.' }}
                </p>
              </div>

              <div class="mt-10 space-y-2.5">
                <RouterLink
                  :to="{ name: 'update-image', params: { id: imageId } }"
                  class="inline-flex w-full items-center justify-between rounded-xl border border-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-widest text-neutral-900 transition hover:bg-neutral-900 hover:text-white"
                >
                  <span>Modifier le média</span>
                  <ArrowUpRight :size="15" />
                </RouterLink>

                <RouterLink
                  :to="{ name: 'admin-galeries' }"
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
                    <CheckCircle2 :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div>
                      <p class="text-[13px] font-medium text-[#5d5d5d]">Salle rattachée</p>
                      <p class="mt-1 text-[15px] font-medium text-[#222]">
                        {{ image.salle?.nom || 'Salle #' + image.salle_id }}
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 border-b border-[#efeee9] pb-4">
                    <CheckCircle2 :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div>
                      <p class="text-[13px] font-medium text-[#5d5d5d]">Statut d'affichage</p>
                      <p class="mt-1 text-[15px] font-medium text-[#2f9967]">
                        Publié dans le catalogue
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3 border-b border-[#efeee9] pb-4">
                    <CheckCircle2 :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div>
                      <p class="text-[13px] font-medium text-[#5d5d5d]">Qualité de rendu</p>
                      <p class="mt-1 text-[15px] font-medium text-[#222]">
                        Haute Définition (HD)
                      </p>
                    </div>
                  </div>

                  <div class="flex gap-3">
                    <HardDrive :size="17" class="mt-0.5 shrink-0 text-slate-800" />
                    <div class="min-w-0 flex-1">
                      <p class="text-[13px] font-medium text-[#5d5d5d]">Chemin du fichier</p>
                      <p class="mt-1 break-all text-[12px] font-mono text-[#777]">
                        {{ image.path || 'Stockage local' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- DÉTAILS ADMINISTRATIFS -->
              <div class="mt-9 border-t border-[#ecebe7] pt-6">
                <div class="flex items-center gap-2 mb-3">
                  <Calendar :size="16" class="text-slate-800" />
                  <h2 class="text-[12px] font-semibold uppercase tracking-[0.08em] text-[#222]">
                    Données d'enregistrement
                  </h2>
                </div>

                <div class="space-y-2 rounded-[8px] border border-[#deddd9] bg-[#fafaf8] p-3 text-[11px]">
                  <div class="flex items-center justify-between text-[#777]">
                    <span>Identifiant</span>
                    <span class="font-semibold text-[#191919]">#{{ image.id }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[#777]">
                    <span>Ajoutée le</span>
                    <span class="font-semibold text-[#191919">{{ formatDate(image.created_at) }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[#777]">
                    <span>Modifiée le</span>
                    <span class="font-semibold text-[#191919">{{ formatDate(image.updated_at) }}</span>
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
