<script setup>
import { useToast } from "@/composables/useToast";
import { CheckCircle2, XCircle } from "@lucide/vue";

const { toasts } = useToast();
</script>

<template>
  <div class="fixed top-4 right-4 z-[100] space-y-2">
    <TransitionGroup name="toast">
      <div v-for="toast in toasts" :key="toast.id"
        class="flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium min-w-[240px]"
        :class="toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'">
        <CheckCircle2 v-if="toast.type === 'success'" class="w-4 h-4 shrink-0" />
        <XCircle v-else class="w-4 h-4 shrink-0" />
        {{ toast.message }}
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.2s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
