<script setup>
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue'
</script>

<template>
    <div class="min-h-screen bg-[#F8FAFC] text-[#0F172A]">

        <!-- SIDEBAR -->
        <Sidebar />

        <!-- ZONE PRINCIPALE -->
        <div class="ml-[280px] min-h-screen">

            <!-- NAVBAR -->
            <Navbar />

            <!-- CONTENU DE LA PAGE -->
            <main class="min-h-[calc(100vh-84px)] p-10">
                <slot />
            </main>

        </div>
    </div>
</template>