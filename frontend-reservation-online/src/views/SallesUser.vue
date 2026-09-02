<script setup>

import NavBar from '@/layouts/NavBar.vue'
import Footer from '@/layouts/Footer.vue'
import Confort from '@/layouts/salle/Confort.vue'
import NosSalle from '@/layouts/salle/NosSalle.vue'
import HeroSection from '@/layouts/salle/HeroSection.vue';


</script>

<template>

    <NavBar :class="$route.name === 'login' || $route.name === 'register' ? 'hidden' : 'block'" />
    <HeroSection/>
    <Confort/>
    <NosSalle/>
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
