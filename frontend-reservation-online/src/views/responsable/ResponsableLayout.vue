<script setup>
import { onMounted } from "vue";
import { RouterView } from "vue-router";
import SidebarResponsable from "@/components/responsable/SidebarResponsable.vue";
import ToastContainer from "@/components/ui/ToastContainer.vue";
import { useResponsableReservationsStore } from "@/store/responsableReservations";

const store = useResponsableReservationsStore();

onMounted(() => {
  store.fetchAll();
});
</script>

<template>
  <div class="flex min-h-screen bg-slate-50">
    <SidebarResponsable />
    <main class="flex-1 px-8 py-8 max-w-6xl mx-auto w-full">
      <RouterView />
    </main>
    <ToastContainer />
  </div>
</template>
