<script setup>
const partners = [
    {
        name: 'Partenaire 1',
        logo: '/images/partners/partner-1.png',
    },
    {
        name: 'Partenaire 2',
        logo: '/images/partners/partner-2.png',

    },
]
</script>

<template>
    <section
        class="relative overflow-hidden bg-[#F8FAFC]
               px-6 py-24 sm:px-10 lg:px-20 lg:py-16"
    >

        <div
            class="mx-auto max-w-[1200px]"
        >

            <!-- CONTENU -->
            <div
                class="grid grid-cols-1
                       items-center
                       gap-16
                       lg:grid-cols-2
                       lg:gap-20"
            >

                <!-- ========================================= -->
                <!-- GAUCHE -->
                <!-- ========================================= -->

                <div>

                    <!-- PETIT LABEL -->
                    <p
                        class="mb-7
                               text-[12px]
                               font-semibold
                               uppercase
                               tracking-[2.5px]
                               text-[#64748B]"
                    >
                        UNE SOLUTION LOCALE
                    </p>


                    <!-- TITRE -->
                    <h2
                        class="max-w-[540px]
                               text-[42px]
                               font-semibold
                               leading-[1.08]
                               tracking-[-1.8px]
                               text-[#0F172A]
                               sm:text-[50px]
                               lg:text-[56px]"
                    >
                        Développé au
                        <span class="text-[#4F46E5]">
                            Bénin.
                        </span>

                        <br />

                        Fiable
                        <span class="italic">
                            partout.
                        </span>
                    </h2>


                    <!-- DESCRIPTION -->
                    <p
                        class="mt-7
                               max-w-[510px]
                               text-[15px]
                               leading-[1.7]
                               text-[#475569]"
                    >
                        Notre plateforme est pensée et développée
                        au Bénin pour simplifier la réservation
                        de salles et d'équipements.
                        <br />

                        Une solution simple, moderne et fiable pour
                        les entreprises, établissements et organisations.
                    </p>

                </div>


                <!-- ========================================= -->
                <!-- DROITE : PARTENAIRES -->
                <!-- ========================================= -->

                <div>

                    <!-- LABEL -->
                    <p
                        class="mb-7
                               text-[11px]
                               font-semibold
                               uppercase
                               tracking-[2px]
                               text-[#94A3B8]"
                    >
                        Ils nous font confiance
                    </p>


                    <!-- LOGOS -->
                    <div
                        class="flex flex-wrap
                               items-center
                               justify-center
                               gap-x-5
                               gap-y-7
                               lg:justify-start"
                    >

                        <div
                            v-for="partner in partners"
                            :key="partner.name"
                            class="flex h-[92px]
                                   w-[92px]
                                   items-center
                                   justify-center
                                   rounded-full
                                   border
                                   border-[#E2E8F0]
                                   bg-white
                                   p-4
                                   shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]
                                   transition-all
                                   duration-300
                                   hover:-translate-y-1
                                   hover:border-[#4F46E5]
                                   hover:shadow-[0_8px_25px_-5px_rgba(79,70,229,0.15)]"
                        >

                            <img
                                :src="partner.logo"
                                :alt="partner.name"
                                class="max-h-full
                                       max-w-full
                                       object-contain
                                       grayscale
                                       opacity-70
                                       transition-all
                                       duration-300
                                       hover:grayscale-0
                                       hover:opacity-100"
                            />

                        </div>

                    </div>


                    <!-- TEXTE SOUS LES LOGOS -->
                    <p
                        class="mt-8
                               max-w-[430px]
                               text-[13px]
                               leading-[1.6]
                               text-[#64748B]"
                    >
                        Des entreprises et organisations nous font
                        confiance pour simplifier la gestion de leurs
                        espaces et réservations.
                    </p>

                </div>

            </div>


            <!-- ========================================= -->
            <!-- BAS DE SECTION -->
            <!-- ========================================= -->



        </div>

    </section>
</template>