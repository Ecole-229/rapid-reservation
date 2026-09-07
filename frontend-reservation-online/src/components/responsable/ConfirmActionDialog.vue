<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from "@headlessui/vue";

defineProps({
  open: Boolean,
  titre: String,
  message: String,
  labelConfirmer: { type: String, default: "Confirmer" },
  variant: { type: String, default: "primaire" },
});

const emit = defineEmits(["confirmer", "annuler"]);
</script>

<template>
  <TransitionRoot appear :show="open" as="template">
    <Dialog as="div" class="relative z-50" @close="emit('annuler')">
      <TransitionChild
        enter="duration-200 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-150 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 flex items-center justify-center px-4">
        <TransitionChild
          enter="duration-200 ease-out"
          enter-from="opacity-0 scale-95"
          enter-to="opacity-100 scale-100"
          leave="duration-150 ease-in"
          leave-from="opacity-100 scale-100"
          leave-to="opacity-0 scale-95"
        >
          <DialogPanel class="w-full max-w-sm bg-white rounded-xl shadow-xl p-6">
            <DialogTitle class="text-base font-semibold text-slate-900">
              {{ titre }}
            </DialogTitle>
            <p class="mt-2 text-sm text-slate-600">{{ message }}</p>

            <div class="mt-6 flex justify-end gap-3">
              <button
                type="button"
                class="px-4 py-2 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition"
                @click="emit('annuler')"
              >
                Annuler
              </button>
              <button
                type="button"
                class="px-4 py-2 text-sm font-medium text-white rounded-lg transition"
                :class="variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'"
                @click="emit('confirmer')"
              >
                {{ labelConfirmer }}
              </button>
            </div>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
