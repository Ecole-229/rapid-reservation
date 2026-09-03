""
<script setup>
import { ref } from 'vue'

const currentYear = new Date().getFullYear()

const mainLinks = [
  { name: 'Home', href: '#' },
  { name: 'About', href: '#' },
  { name: 'Services', href: '#' },
  { name: 'Projects', href: '#' },
  { name: 'Blog', href: '#' },
  { name: 'Review', href: '#' },
  { name: 'Contact', href: '#' },
]

const serviceLinks = [
  { name: 'SEO', href: '#' },
  { name: 'Content Marketing', href: '#' },
  { name: 'Website Design', href: '#' },
  { name: 'Social Media Marketing', href: '#' },
]
</script>

<template>
  <footer class="bg-[#F8FAFC] text-slate-800 p-4 sm:p-8 font-sans">

    <div class="max-w-7xl mx-auto space-y-4">

      <!-- BLOC PRINCIPAL DE FOOTER -->
      <div
        class="bg-white rounded-3xl p-6 sm:p-10
               border border-slate-200/80 shadow-sm
               grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
      >

        <!-- COLONNE GAUCHE : Cartes de contact -->
        <div class="lg:col-span-7 space-y-3">

          <!-- Carte Email -->
          <div
            class="bg-[#F1F5F9]
                   border border-slate-200/60
                   p-4 sm:p-5 rounded-2xl
                   flex items-center gap-4
                   transition-all duration-300
                   hover:bg-indigo-50
                   hover:border-indigo-200
                   hover:shadow-sm"
          >
            <div
              class="text-slate-600 shrink-0
                     transition-colors duration-300
                     group-hover:text-indigo-600"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
            </div>

            <span
              class="text-slate-700 font-medium text-sm sm:text-base
                     transition-colors duration-300
                     hover:text-indigo-700"
            >
              agencee@email.com
            </span>
          </div>


          <!-- Carte Téléphone -->
          <div
            class="bg-[#F1F5F9]
                   border border-slate-200/60
                   p-4 sm:p-5 rounded-2xl
                   flex items-center gap-4
                   transition-all duration-300
                   hover:bg-indigo-50
                   hover:border-indigo-200
                   hover:shadow-sm"
          >
            <div class="text-slate-600 shrink-0">
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                />
              </svg>
            </div>

            <span
              class="text-slate-700 font-medium text-sm sm:text-base
                     transition-colors duration-300
                     hover:text-indigo-700"
            >
              + 54 2541 22 55 66
            </span>
          </div>


          <!-- Carte Adresse -->
          <div
            class="bg-[#F1F5F9]
                   border border-slate-200/60
                   p-4 sm:p-5 rounded-2xl
                   flex items-center gap-4
                   transition-all duration-300
                   hover:bg-indigo-50
                   hover:border-indigo-200
                   hover:shadow-sm"
          >
            <div class="text-slate-600 shrink-0">
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
            </div>

            <span
              class="text-slate-700 font-medium text-sm sm:text-base
                     transition-colors duration-300
                     hover:text-indigo-700"
            >
              123 Main Street Anytown, USA, 2141
            </span>
          </div>

        </div>


        <!-- COLONNE DROITE : Navigation -->
        <div class="lg:col-span-5 grid grid-cols-2 gap-8 pt-2">

          <!-- Navigation Principale -->
          <div class="space-y-3">

            <h3 class="text-sm font-semibold text-slate-800 mb-4">
              Navigation
            </h3>

            <a
              v-for="item in mainLinks"
              :key="item.name"
              :href="item.href"
              class="block text-sm font-medium
                     text-slate-600
                     hover:text-slate-800
                     transition-colors duration-200"
            >
              {{ item.name }}
            </a>

          </div>


          <!-- Services -->
          <div class="space-y-3">

            <h3 class="text-sm font-semibold text-slate-800 mb-4">
              Services
            </h3>

            <a
              v-for="item in serviceLinks"
              :key="item.name"
              :href="item.href"
              class="block text-sm font-medium
                     text-slate-600
                     hover:text-slate-800
                     transition-colors duration-200"
            >
              {{ item.name }}
            </a>

          </div>

        </div>

      </div>


      <!-- BLOC RÉSEAUX SOCIAUX -->
      <div
        class="bg-white rounded-3xl p-8
               border border-slate-200/80
               shadow-sm
               flex flex-col items-center justify-center
               gap-4 text-center"
      >

        <span
          class="text-xs font-bold uppercase tracking-wider
                 text-slate-500"
        >
          Follow us:
        </span>

        <div class="flex items-center justify-center gap-3">

          <!-- Facebook -->
          <a
            href="#"
            class="w-10 h-10 rounded-full
                   bg-[#F1F5F9]
                   text-slate-700
                   border border-slate-200/80
                   flex items-center justify-center
                   transition-all duration-300
                   hover:bg-indigo-50
                   hover:border-indigo-200
                  text-slate-700
                   hover:-translate-y-0.5
                   hover:shadow-md"
          >
            <svg
              class="w-4 h-4 fill-current"
              viewBox="0 0 24 24"
            >
              <path
                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
              />
            </svg>
          </a>


          <!-- Twitter / X -->
          <a
            href="#"
            class="w-10 h-10 rounded-full
                   bg-[#F1F5F9]
                   text-slate-700
                   border border-slate-200/80
                   flex items-center justify-center
                   transition-all duration-300
                   hover:bg-indigo-50
                   hover:border-indigo-200
                  text-slate-700
                   hover:-translate-y-0.5
                   hover:shadow-md"
          >
            <svg
              class="w-4 h-4 fill-current"
              viewBox="0 0 24 24"
            >
              <path
                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"
              />
            </svg>
          </a>


          <!-- LinkedIn -->
          <a
            href="#"
            class="w-10 h-10 rounded-full
                   bg-[#F1F5F9]
                   text-slate-700
                   border border-slate-200/80
                   flex items-center justify-center
                   transition-all duration-300
                   hover:bg-indigo-50
                   hover:border-indigo-200
                  text-slate-700
                   hover:-translate-y-0.5
                   hover:shadow-md"
          >
            <svg
              class="w-4 h-4 fill-current"
              viewBox="0 0 24 24"
            >
              <path
                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.762 2.239 5 5 5h14c2.762 0 5-2.238 5-5v-14c0-2.761-2.238-5-5-5zM8 19H5V8h3v11zM6.5 6.732A1.764 1.764 0 114.736 4.968 1.764 1.764 0 016.5 6.732zM20 19h-3v-5.604c0-3.368-4-3.113-4 0V19h-3V8h3v1.765c1.396-2.586 7-2.777 7 2.476V19z"
              />
            </svg>
          </a>


          <!-- Instagram -->
          <a
            href="#"
            class="w-10 h-10 rounded-full
                   bg-[#F1F5F9]
                   text-slate-700
                   border border-slate-200/80
                   flex items-center justify-center
                   transition-all duration-300
                   hover:bg-indigo-50
                   hover:border-indigo-200
                  text-slate-700
                   hover:-translate-y-0.5
                   hover:shadow-md"
          >
            <svg
              class="w-4 h-4 fill-current"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.204-.013-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
              />
            </svg>
          </a>

        </div>
      </div>


      <!-- BARRE DE COPYRIGHT -->
      <div
        class="pt-2 px-2
               flex flex-wrap items-center justify-between
               gap-2 text-xs font-medium text-slate-500"
      >
        <div>
          © {{ currentYear }} RoomBook
        </div>

        <div>
          Tous droits réservés
        </div>
      </div>

    </div>
  </footer>
</template>

