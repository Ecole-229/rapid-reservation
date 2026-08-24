<script setup>

  import NavBar from './layouts/NavBar.vue';


</script>

<template>
  <NavBar :class="$route.name === 'login' || $route.name === 'register' ? 'hidden' : 'block'" />
  <RouterView/>
</template>