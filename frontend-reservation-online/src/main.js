import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import { scrollReveal } from '@/directives/scrollReveal'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Directive globale : v-scroll-reveal
app.directive('scroll-reveal', scrollReveal)

app.mount('#app')
