<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import {
    ChevronLeft,
    ChevronRight,
} from 'lucide-vue-next'

const experiences = [
    {
        title: 'Salle de conférence moderne',
        location: 'Cotonou, Bénin',
        image: '/images/experiences/salle-1.png',
    },
    {
        title: 'Salle de réunion professionnelle',
        location: 'Abomey-Calavi, Bénin',
        image: '/images/experiences/salle-2.png',
    },
    {
        title: 'Espace de travail confortable',
        location: 'Porto-Novo, Bénin',
        image: '/images/experiences/salle-3.png',
    },
    {
        title: 'Salle événementielle',
        location: 'Cotonou, Bénin',
        image: '/images/experiences/salle-4.png',
    },

    {
        title: 'Salle événementielle',
        location: 'Cotonou, Bénin',
        image: '/images/experiences/salle-5.png',
    },
    {
        title: 'Salle événementielle',
        location: 'Cotonou, Bénin',
        image: '/images/experiences/salle-6.png',
    }

]

const currentIndex = ref(0)

let interval = null

/*
|--------------------------------------------------------------------------
| Nombre de cartes visibles
|--------------------------------------------------------------------------
*/

const visibleCount = 3

/*
|--------------------------------------------------------------------------
| Défilement suivant
|--------------------------------------------------------------------------
*/

const next = () => {
    currentIndex.value =
        (currentIndex.value + 1) % experiences.length
}

/*
|--------------------------------------------------------------------------
| Défilement précédent
|--------------------------------------------------------------------------
*/

const previous = () => {
    currentIndex.value =
        currentIndex.value === 0
            ? experiences.length - 1
            : currentIndex.value - 1
}

/*
|--------------------------------------------------------------------------
| Défilement automatique
|--------------------------------------------------------------------------
*/

onMounted(() => {
    interval = setInterval(() => {
        next()
    }, 3000)
})

onBeforeUnmount(() => {
    clearInterval(interval)
})
</script>

<template>
    <section
        class="relative overflow-hidden
               bg-[#F8FAFC]
               px-6 py-16
               lg:px-10 lg:py-10"
    >

        <!-- ===================================================== -->
        <!-- CONTENEUR -->
        <!-- ===================================================== -->

        <div
            class="mx-auto max-w-[1400px]"
        >

            <div
                class="grid grid-cols-1
                       items-center
                       gap-12
                       lg:grid-cols-[310px_1fr]
                       lg:gap-12"
            >

                <!-- ================================================= -->
                <!-- COLONNE GAUCHE -->
                <!-- ================================================= -->

                <div
                    class="relative z-10"
                >

                    <!-- PETIT TITRE -->
                    <p
                        class="mb-7
                               text-[12px]
                               font-bold
                               uppercase
                               tracking-[4px]
                               text-[#0F172A]"
                    >
                        EXPÉRIENCES
                    </p>


                    <!-- GRAND TITRE -->
                    <h2
                        class="max-w-[320px]
                               font-serif
                               text-[42px]
                               font-normal
                               leading-[1.08]
                               tracking-[-1.5px]
                               text-[#0F172A]
                               sm:text-[48px]"
                    >
                        Découvrez
                        <span
                            class="italic"
                        >
                            nos espaces
                        </span>
                    </h2>


                    <!-- DESCRIPTION -->
                    <p
                        class="mt-6
                               max-w-[300px]
                               text-[14px]
                               leading-[1.6]
                               text-[#475569]"
                    >
                        Explorez nos salles et espaces soigneusement
                        sélectionnés pour répondre à vos besoins
                        professionnels et événementiels.
                    </p>


                    <!-- ================================================= -->
                    <!-- NAVIGATION -->
                    <!-- ================================================= -->

                    <div
                        class="mt-8 flex items-center gap-3"
                    >

                        <!-- PREVIOUS -->
                        <button
                            type="button"
                            aria-label="Expérience précédente"
                            class="flex h-[42px] w-[42px]
                                   items-center justify-center
                                   rounded-full
                                   border border-[#E2E8F0]
                                   bg-white
                                   text-[#0F172A]
                                   shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]
                                   transition-all duration-200
                                   hover:border-[#4F46E5]
                                   hover:bg-[#EEF2FF]
                                   hover:text-[#4F46E5]
                                   active:scale-[0.96]"
                            @click="previous"
                        >
                            <ChevronLeft
                                :size="19"
                                :stroke-width="1.8"
                            />
                        </button>


                        <!-- NEXT -->
                        <button
                            type="button"
                            aria-label="Expérience suivante"
                            class="flex h-[42px] w-[42px]
                                   items-center justify-center
                                   rounded-full
                                   border border-[#E2E8F0]
                                   bg-white
                                   text-[#0F172A]
                                   shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]
                                   transition-all duration-200
                                   hover:border-[#4F46E5]
                                   hover:bg-[#EEF2FF]
                                   hover:text-[#4F46E5]
                                   active:scale-[0.96]"
                            @click="next"
                        >
                            <ChevronRight
                                :size="19"
                                :stroke-width="1.8"
                            />
                        </button>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- CARROUSEL -->
                <!-- ================================================= -->

                <div
                    class="relative min-w-0 overflow-hidden"
                >

                    <!-- PISTE -->
                    <div
                        class="flex gap-5
                               transition-transform
                               duration-700
                               ease-in-out"
                        :style="{
                            transform: `translateX(calc(-${currentIndex} * (33.333% + 6.66px)))`
                        }"
                    >

                        <!-- CARTE -->
                        <article
                            v-for="(experience, index) in experiences"
                            :key="index"
                            class="group
                                   w-[calc((100%-40px)/3)]
                                   min-w-[calc((100%-40px)/3)]
                                   shrink-0"
                        >

                            <!-- IMAGE -->
                            <div
                                class="relative
                                       aspect-[0.72]
                                       overflow-hidden
                                       bg-[#E2E8F0]"
                            >

                                <img
                                    :src="experience.image"
                                    :alt="experience.title"
                                    class="h-full w-full
                                           object-cover
                                           transition-transform
                                           duration-700
                                           ease-out
                                           group-hover:scale-[1.03]"
                                />

                            </div>


                            <!-- INFORMATIONS -->
                            <div
                                class="pt-5"
                            >

                                <h3
                                    class="font-serif
                                           text-[19px]
                                           leading-tight
                                           text-[#0F172A]"
                                >
                                    {{ experience.title }}
                                </h3>

                                <p
                                    class="mt-1
                                           text-[12px]
                                           text-[#64748B]"
                                >
                                    {{ experience.location }}
                                </p>


                                <!-- BOUTON -->
                                <button
                                    type="button"
                                    class="mt-5
                                           bg-[#0F172A]
                                           px-5 py-3
                                           text-[10px]
                                           font-bold
                                           tracking-[2px]
                                           text-white
                                           transition-colors
                                           duration-200
                                           hover:bg-[#020617]
                                           active:scale-[0.98]"
                                >
                                    DÉCOUVRIR
                                </button>

                            </div>

                        </article>

                    </div>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- INDICATEURS -->
        <!-- ===================================================== -->

        <div
            class="mx-auto mt-10
                   flex max-w-[1400px]
                   justify-end
                   lg:pr-2"
        >

            <div
                class="flex items-center gap-2"
            >

                <button
                    v-for="(_, index) in experiences"
                    :key="index"
                    type="button"
                    class="h-[3px]
                           rounded-full
                           transition-all duration-300"
                    :class="
                        currentIndex === index
                            ? 'w-8 bg-[#0F172A]'
                            : 'w-3 bg-[#CBD5E1]'
                    "
                    @click="currentIndex = index"
                ></button>

            </div>

        </div>

    </section>
</template>


<style scoped>
.font-serif {
    font-family:
        Georgia,
        'Times New Roman',
        serif;
}
</style>