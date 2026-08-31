<script setup>
import { ref } from 'vue'
import { ArrowRight } from 'lucide-vue-next'

const activeFaq = ref(null)

const faqs = [
    {
        question: 'Qu’est-ce que RoomBook ?',
        answer:
            'RoomBook est une plateforme de réservation qui permet de consulter les disponibilités et de réserver facilement des salles et des équipements.',
    },
    {
        question: 'À qui s’adresse RoomBook ?',
        answer:
            'RoomBook s’adresse aux entreprises, établissements, organisations et particuliers qui souhaitent réserver des espaces ou des équipements.',
    },
    {
        question: 'Comment réserver une salle ?',
        answer:
            'Choisissez une salle, sélectionnez la date et l’heure souhaitées, indiquez le nombre de personnes puis confirmez votre réservation.',
    },
    {
        question: 'Puis-je réserver des équipements ?',
        answer:
            'Oui. Vous pouvez réserver les équipements disponibles avec une salle ou effectuer une réservation selon les équipements proposés.',
    },
    {
        question: 'Comment savoir si une salle est disponible ?',
        answer:
            'Le calendrier de disponibilité vous permet de consulter les créneaux disponibles avant de confirmer votre réservation.',
    },
    {
        question: 'Puis-je annuler une réservation ?',
        answer:
            'Oui, une réservation peut être annulée selon les conditions définies par l’établissement ou le responsable de la salle.',
    },
]

const toggleFaq = (index) => {
    activeFaq.value =
        activeFaq.value === index
            ? null
            : index
}
</script>

<template>
    <section
        class="bg-white
               px-6 py-20
               sm:px-10
               lg:px-20 lg:py-28"
    >

        <!-- ========================================= -->
        <!-- CONTENEUR -->
        <!-- ========================================= -->

        <div
            class="mx-auto max-w-[1000px]"
        >

            <!-- ========================================= -->
            <!-- TITRE -->
            <!-- ========================================= -->

            <div
                class="mb-16 text-center"
            >
                <h2
                    class="text-[38px]
                           font-bold
                           uppercase
                           leading-none
                           tracking-[-1.5px]
                           text-[#0F172A]
                           sm:text-[46px]
                           lg:text-[52px]"
                >
                    Encore des questions ?
                </h2>

                <p
                    class="mx-auto mt-5
                           max-w-[560px]
                           text-[14px]
                           leading-[1.7]
                           text-[#64748B]"
                >
                    Retrouvez les réponses aux questions les plus
                    fréquentes concernant la réservation de salles
                    et d'équipements.
                </p>
            </div>


            <!-- ========================================= -->
            <!-- FAQ -->
            <!-- ========================================= -->

            <div
                class="space-y-5"
            >

                <div
                    v-for="(faq, index) in faqs"
                    :key="faq.question"
                    class="overflow-hidden
                           rounded-[22px]
                           bg-[#F8FAFC]
                           transition-all duration-300
                           hover:bg-[#F1F5F9]"
                    :class="
                        activeFaq === index
                            ? 'ring-1 ring-[#E2E8F0]'
                            : ''
                    "
                >

                    <!-- ================================= -->
                    <!-- QUESTION -->
                    <!-- ================================= -->

                    <button
                        type="button"
                        class="flex w-full
                               items-center
                               justify-between
                               gap-6
                               px-6 py-7
                               text-left
                               sm:px-7"
                        @click="toggleFaq(index)"
                    >

                        <!-- QUESTION -->
                        <span
                            class="text-[15px]
                                   font-semibold
                                   leading-[1.4]
                                   text-[#0F172A]
                                   sm:text-[16px]"
                        >
                            {{ faq.question }}
                        </span>


                        <!-- BOUTON -->
                        <span
                            class="flex h-[34px]
                                   w-[34px]
                                   shrink-0
                                   items-center
                                   justify-center
                                   rounded-full
                                   bg-[#0F172A]
                                   text-white
                                   transition-all
                                   duration-300"
                            :class="
                                activeFaq === index
                                    ? 'rotate-90 bg-[#4F46E5]'
                                    : 'rotate-0'
                            "
                        >
                            <ArrowRight
                                :size="17"
                                :stroke-width="2"
                            />
                        </span>

                    </button>


                    <!-- ================================= -->
                    <!-- REPONSE -->
                    <!-- ================================= -->

                    <div
                        class="grid transition-all
                               duration-300 ease-in-out"
                        :class="
                            activeFaq === index
                                ? 'grid-rows-[1fr] opacity-100'
                                : 'grid-rows-[0fr] opacity-0'
                        "
                    >

                        <div
                            class="overflow-hidden"
                        >

                            <div
                                class="border-t
                                       border-[#E2E8F0]
                                       px-6 pb-7 pt-5
                                       sm:px-7"
                            >

                                <p
                                    class="max-w-[780px]
                                           text-[14px]
                                           leading-[1.7]
                                           text-[#64748B]"
                                >
                                    {{ faq.answer }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ========================================= -->
            <!-- FOOTER FAQ -->
            <!-- ========================================= -->

            <div
                class="mt-12 text-center"
            >

                <p
                    class="text-[13px]
                           text-[#64748B]"
                >
                    Vous ne trouvez pas la réponse à votre question ?
                </p>

                <button
                    type="button"
                    class="mt-3
                           text-[13px]
                           font-semibold
                           text-[#4F46E5]
                           transition-colors
                           duration-200
                           hover:text-[#4338CA]"
                >
                    Contactez-nous →
                </button>

            </div>

        </div>

    </section>
</template>