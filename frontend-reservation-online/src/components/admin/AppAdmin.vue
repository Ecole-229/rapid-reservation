<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './Sidebar.vue'
import Navbar from './Navbar.vue'

const route = useRoute()
const isSidebarOpen = ref(false)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = () => {
    isSidebarOpen.value = false
}

// Ferme la sidebar mobile automatiquement lors d'une navigation
watch(
    () => route.path,
    () => {
        isSidebarOpen.value = false
    }
)
</script>

<template>
    <div class="relative min-h-screen bg-[#F8FAFC] text-[#0F172A]">

        <!-- OVERLAY BACKDROP POUR MOBILE/TABLETTE -->
        <Transition name="fade">
            <div
                v-if="isSidebarOpen"
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden"
                @click="closeSidebar"
                aria-hidden="true"
            ></div>
        </Transition>

        <!-- SIDEBAR (DRAWER EN MOBILE, FIXE EN DESKTOP) -->
        <Sidebar
            :is-open="isSidebarOpen"
            @close="closeSidebar"
        />

        <!-- ZONE PRINCIPALE (PLEINE LARGEUR EN MOBILE, DÉCALÉE EN DESKTOP) -->
        <div class="min-h-screen transition-all duration-300 ml-0 lg:ml-[280px]">

            <!-- NAVBAR -->
            <Navbar
                @toggle-sidebar="toggleSidebar"
            />

            <!-- CONTENU DE LA PAGE -->
            <main class="min-h-[calc(100vh-84px)] p-4 sm:p-6 lg:p-8">
                <slot />
            </main>

        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>