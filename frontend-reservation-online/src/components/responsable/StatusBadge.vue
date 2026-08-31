<script setup>
import { Clock, CheckCircle2, XCircle, CircleSlash, CheckCheck } from "@lucide/vue";

defineProps({
  statut: {
    type: String,
    required: true,
  },
});

const config = {
  en_attente: { label: "En attente", icon: Clock, classes: "bg-amber-50 text-amber-800 border-amber-200" },
  confirmee: { label: "Confirmée", icon: CheckCircle2, classes: "bg-emerald-50 text-emerald-800 border-emerald-200" },
  rejetee: { label: "Rejetée", icon: XCircle, classes: "bg-red-50 text-red-800 border-red-200" },
  terminee: { label: "Terminée", icon: CheckCheck, classes: "bg-slate-100 text-slate-600 border-slate-200" },
  annulee: { label: "Annulée", icon: CircleSlash, classes: "bg-slate-100 text-slate-500 border-slate-200" },
};
</script>

<template>
  <span
    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border"
    :class="config[statut]?.classes || 'bg-slate-100 text-slate-600 border-slate-200'"
  >
    <component :is="config[statut]?.icon" class="w-3.5 h-3.5" />
    {{ config[statut]?.label || statut }}
  </span>
</template>
