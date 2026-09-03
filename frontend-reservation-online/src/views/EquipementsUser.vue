<script setup>

import NavBar from '@/layouts/NavBar.vue'


import Footer from '@/layouts/Footer.vue'
import Quality from '@/layouts/equipements/Quality.vue'
import NosEquipement from '@/layouts/equipements/NosEquipement.vue';
import FaqSection from '@/layouts/FaqSection.vue';
import HeroSection from '@/layouts/equipements/HeroSection.vue';


</script>

<template>

    <NavBar :class="$route.name === 'login' || $route.name === 'register' ? 'hidden' : 'block'" />
    <HeroSection/>
    <Quality/>
    <NosEquipement/>
    <FaqSection/>
    <Footer/>


</template>


<style scoped>
/*
|--------------------------------------------------------------------------
| Animation changement d'image
|--------------------------------------------------------------------------
*/

.image-fade-enter-active,
.image-fade-leave-active {
    transition: opacity 0.6s ease;
}

.image-fade-enter-from,
.image-fade-leave-to {
    opacity: 0;
}

.image-fade-enter-to,
.image-fade-leave-from {
    opacity: 1;
}
</style>
