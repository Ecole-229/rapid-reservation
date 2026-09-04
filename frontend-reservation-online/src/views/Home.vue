<script setup>

import NavBar from '@/layouts/NavBar.vue'
import Exprerience from '@/layouts/Exprerience.vue'

import FaqSection from '@/layouts/FaqSection.vue'
import HeroSection from '@/layouts/HeroSection.vue'
import Marketing from '@/layouts/Marketing.vue'

import Temoignage from '@/layouts/Temoignage.vue'
import Footer from '@/layouts/Footer.vue'
import TrueSections from '@/layouts/TrueSections.vue'


</script>

<template>

    <NavBar :class="$route.name === 'login' || $route.name === 'register' ? 'hidden' : 'block'" />
    <HeroSection/>
    <TrueSections/>
    <Marketing/>
    <Temoignage/>
    <Exprerience/>
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
