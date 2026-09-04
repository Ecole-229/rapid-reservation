<script setup>
import { ref } from 'vue'
import { Plus, X } from 'lucide-vue-next'

// Premier élément ouvert par défaut (index 0)
const openIndex = ref(0)

const faqs = [
  {
    question: 'Comment puis-je réserver une salle ou un équipement ?',
    answer: 'Il vous suffit de sélectionner la salle ou l’équipement souhaité, de choisir la date et les créneaux horaires disponibles, puis de valider votre demande en quelques clics.',
  },
  {
    question: 'Mes informations de réservation sont-elles sécurisées ?',
    answer: 'Oui, nous prenons la confidentialité et la sécurité des données très au sérieux. Toutes vos informations personnelles et vos réservations sont chiffrées et stockées de manière sécurisée.',
  },
  {
    question: 'Puis-je modifier ou annuler une réservation à tout moment ?',
    answer: 'Oui, vous pouvez gérer vos réservations depuis votre espace personnel. Les annulations sont possibles selon les conditions définies par l’établissement.',
  },
  {
    question: 'Comment savoir si une salle est disponible immédiatement ?',
    answer: 'Le calendrier en ligne affiche les disponibilités en temps réel. Un créneau disponible est marqué comme tel dès que vous sélectionnez l’espace.',
  },
  {
    question: 'Recevrai-je une confirmation après ma réservation ?',
    answer: 'Absolument. Un e-mail de confirmation contenant tous les détails (heure, lieu, équipements inclus) vous est automatiquement envoyé dès la validation.',
  },
  {
    question: 'Que faire si je rencontre un problème lors de l’utilisation d’une salle ?',
    answer: 'Un service d’assistance est joignable directement depuis la plateforme pour vous aider en cas de problème technique ou d’accès à la salle.',
  },
]
</script>

<template>
  <section class="min-h-[80vh] bg-white text-gray-900 px-6 py-20 flex items-center justify-center">
    <div class="max-w-7xl w-full mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

      <!-- Colonne Gauche : Titre & Sous-titre -->
      <div class="lg:col-span-5 space-y-4 pt-4">
        <span class="text-xl font-semibold tracking-[0.2em] text-slate-900 uppercase">
          Avez-vous des questions ?
        </span>
        <h2 class="text-4xl sm:text-5xl font-semibold tracking-tight text-slate-900 mt-10">
          Our Faqs
        </h2>
      </div>

      <!-- Colonne Droite : Carte Accordéon sur fond blanc/gris très léger -->
      <div class="lg:col-span-7 bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
        <div class="divide-y divide-slate-200">

          <div
            v-for="(faq, index) in faqs"
            :key="index"
            class="py-5 first:pt-0 last:pb-0 transition-colors duration-200"
            @mouseenter="openIndex = index"
          >
            <!-- En-tête de la question (Déclencheur au survol) -->
            <div
              class="w-full flex items-center justify-between gap-4 text-left cursor-pointer group"
            >
              <span
                class="text-base sm:text-lg font-medium transition-colors duration-200"
                :class="openIndex === index ? 'text-indigo-600' : 'text-slate-800 group-hover:text-slate-950'"
              >
                {{ faq.question }}
              </span>

              <!-- Icône + / X -->
              <span
                class="transition-colors duration-200 shrink-0"
                :class="openIndex === index ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700'"
              >
                <X v-if="openIndex === index" class="w-5 h-5" />
                <Plus v-else class="w-5 h-5" />
              </span>
            </div>

            <!-- Contenu de la réponse qui s'ouvre au survol -->
            <div
              class="grid transition-all duration-300 ease-in-out"
              :class="openIndex === index ? 'grid-rows-[1fr] opacity-100 pt-3' : 'grid-rows-[0fr] opacity-0'"
            >
              <div class="overflow-hidden">
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed pr-6">
                  {{ faq.answer }}
                </p>
              </div>
            </div>

          </div>

        </div>
      </div>

    </div>
  </section>
</template>
