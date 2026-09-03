<script setup>
import {
    Plus,
    MapPin,
    Package,
    Banknote,
    ArrowUpRight,
    Monitor,
    Volume2,
    Wifi,
} from 'lucide-vue-next'

const equipments = [
    {
        id: 1,
        name: 'Vidéoprojecteur Epson',
        image: '/images/equipements/equipement-1.png',
        age: 'Disponible',
        status: 'Disponible',
        location: 'Cotonou, Bénin',
        quantity: 8,
        price: '15 000',
        type: 'Vidéoprojecteur',
    },

    {
        id: 2,
        name: 'Système de sonorisation',
        image: '/images/equipements/equipement-2.png',
        age: 'Disponible',
        status: 'Disponible',
        location: 'Abomey-Calavi, Bénin',
        quantity: 5,
        price: '20 000',
        type: 'Audio',
    },

    {
        id: 3,
        name: 'Écran de projection',
        image: '/images/equipements/equipement-3.png',
        age: 'Disponible',
        status: 'Disponible',
        location: 'Cotonou, Bénin',
        quantity: 10,
        price: '10 000',
        type: 'Affichage',
    },

    {
        id: 4,
        name: 'Microphone sans fil',
        image: '/images/equipements/equipement-4.png',
        age: 'Disponible',
        status: 'Disponible',
        location: 'Porto-Novo, Bénin',
        quantity: 12,
        price: '7 500',
        type: 'Audio',
    }
]
</script>

<template>
    <section
        class="w-full bg-[#F8FAFC]
               px-4 py-14
               sm:px-6
               lg:px-8 lg:py-5"
    >
        <div class="mx-auto max-w-[1400px]">

            <!-- ================================================= -->
            <!-- TITRE -->
            <!-- ================================================= -->

            <div class="mb-10 flex items-end justify-between gap-6">

                <div>

                    <h2
                        class="text-[36px]
                               font-semibold
                               leading-[1.05]
                               tracking-[-1.5px]
                               text-[#0F172A]
                               sm:text-[44px]"
                    >
                        Des équipements pensés

                        <span class="font-normal italic">
                            pour vous.
                        </span>
                    </h2>

                    <p
                        class="mt-4 max-w-[620px]
                               text-[14px]
                               leading-[1.7]
                               text-[#64748B]"
                    >
                        Découvrez nos équipements disponibles et
                        choisissez ceux qui correspondent le mieux
                        à vos besoins.
                    </p>

                </div>


                <!-- VOIR TOUS -->

                <button
                    type="button"
                    class="hidden shrink-0 items-center gap-2
                           rounded-full
                           border border-[#E2E8F0]
                           bg-white
                           px-5 py-3
                           text-[13px]
                           font-medium
                           text-[#0F172A]
                           shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]
                           transition-all duration-200
                           hover:border-[#4F46E5]
                           hover:bg-[#EEF2FF]
                           hover:text-[#3730A3]
                           sm:flex"
                >
                    Voir tous les équipements

                    <ArrowUpRight
                        :size="16"
                        :stroke-width="1.8"
                    />
                </button>

            </div>


            <!-- ================================================= -->
            <!-- GRILLE -->
            <!-- ================================================= -->

            <div
                class="grid grid-cols-1
                       gap-5
                       md:grid-cols-2
                       xl:grid-cols-3"
            >

                <!-- ================================================= -->
                <!-- CARD -->
                <!-- ================================================= -->

                <article
                    v-for="equipment in equipments"
                    :key="equipment.id"
                    class="group relative overflow-hidden
                           rounded-[22px]
                           border border-[#E2E8F0]
                           bg-white
                           shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]
                           transition-all duration-300
                           hover:-translate-y-1
                           hover:shadow-[0_12px_35px_-10px_rgba(15,23,42,0.14)]"
                >

                    <!-- ================================================= -->
                    <!-- IMAGE -->
                    <!-- ================================================= -->

                    <div
                        class="relative
                               aspect-[1.2]
                               overflow-hidden
                               bg-[#E2E8F0]"
                    >

                        <img
                            :src="equipment.image"
                            :alt="equipment.name"
                            class="h-full w-full
                                   object-cover
                                   transition-transform
                                   duration-700
                                   ease-out
                                   group-hover:scale-[1.035]"
                        />


                        <!-- OVERLAY -->

                        <div
                            class="pointer-events-none
                                   absolute inset-0
                                   bg-gradient-to-t
                                   from-black/20
                                   via-transparent
                                   to-black/5"
                        ></div>


                        <!-- ================================================= -->
                        <!-- BADGE -->
                        <!-- ================================================= -->

                        <div
                            class="absolute left-4 top-4
                                   rounded-full
                                   bg-[#0F172A]/75
                                   px-3.5 py-2
                                   text-[11px]
                                   font-medium
                                   text-white
                                   backdrop-blur-md"
                        >
                            {{ equipment.age }}
                        </div>


                        <!-- ================================================= -->
                        <!-- ACTIONS -->
                        <!-- ================================================= -->

                        <div
                            class="absolute right-4 top-4
                                   flex flex-col gap-2"
                        >

                            <!-- PLUS -->

                            <button
                                type="button"
                                class="flex h-10 w-10
                                       items-center justify-center
                                       rounded-full
                                       bg-[#0F172A]/75
                                       text-white
                                       backdrop-blur-md
                                       transition-all duration-200
                                       hover:bg-[#4F46E5]
                                       active:scale-[0.95]"
                                aria-label="Voir les détails"
                            >
                                <Plus
                                    :size="19"
                                    :stroke-width="1.8"
                                />
                            </button>

                        </div>


                        <!-- ================================================= -->
                        <!-- INDICATEUR -->
                        <!-- ================================================= -->

                        <div
                            class="absolute bottom-4
                                   left-1/2
                                   flex -translate-x-1/2
                                   items-center gap-1.5
                                   rounded-full
                                   bg-[#0F172A]/55
                                   px-3 py-1.5
                                   backdrop-blur-md"
                        >

                            <span
                                class="h-1.5 w-1.5
                                       rounded-full
                                       bg-white"
                            ></span>

                            <span
                                class="h-1.5 w-1.5
                                       rounded-full
                                       bg-white/40"
                            ></span>

                            <span
                                class="h-1.5 w-1.5
                                       rounded-full
                                       bg-white/40"
                            ></span>

                            <span
                                class="h-1.5 w-1.5
                                       rounded-full
                                       bg-white/40"
                            ></span>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- CONTENU -->
                    <!-- ================================================= -->

                    <div class="p-5">

                        <!-- PRIX + STATUS -->

                        <div
                            class="flex items-start
                                   justify-between gap-4"
                        >

                            <div>

                                <p
                                    class="text-[21px]
                                           font-bold
                                           tracking-[-0.6px]
                                           text-[#0F172A]"
                                >
                                    {{ equipment.price }}

                                    <span
                                        class="ml-1
                                               text-[12px]
                                               font-medium
                                               text-[#64748B]"
                                    >
                                        FCFA
                                    </span>
                                </p>

                            </div>


                            <!-- STATUS -->

                            <span
                                class="inline-flex
                                       shrink-0
                                       rounded-full
                                       px-3 py-1.5
                                       text-[11px]
                                       font-semibold"
                                :class="
                                    equipment.status === 'Disponible'
                                        ? 'bg-[#EEF2FF] text-[#3730A3]'
                                        : 'bg-[#F1F5F9] text-[#64748B]'
                                "
                            >
                                {{ equipment.status }}
                            </span>

                        </div>


                        <!-- ================================================= -->
                        <!-- NOM + LOCALISATION -->
                        <!-- ================================================= -->

                        <div class="mt-4">

                            <h3
                                class="text-[18px]
                                       font-semibold
                                       tracking-[-0.3px]
                                       text-[#0F172A]"
                            >
                                {{ equipment.name }}
                            </h3>


                            <div
                                class="mt-2 flex items-center gap-2"
                            >

                                <MapPin
                                    :size="15"
                                    :stroke-width="1.8"
                                    class="text-[#4F46E5]"
                                />

                                <span
                                    class="text-[12px]
                                           text-[#64748B]"
                                >
                                    {{ equipment.location }}
                                </span>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- SEPARATEUR -->
                        <!-- ================================================= -->

                        <div
                            class="my-5 h-px
                                   bg-[#E2E8F0]"
                        ></div>


                        <!-- ================================================= -->
                        <!-- INFORMATIONS -->
                        <!-- ================================================= -->

                        <div
                            class="grid grid-cols-2
                                   gap-3"
                        >

                            <!-- QUANTITE -->

                            <div
                                class="flex items-center gap-3"
                            >

                                <div
                                    class="flex h-9 w-9
                                           items-center
                                           justify-center
                                           rounded-[9px]
                                           bg-[#EEF2FF]
                                           text-[#4F46E5]"
                                >
                                    <Package
                                        :size="16"
                                        :stroke-width="1.8"
                                    />
                                </div>

                                <div>

                                    <p
                                        class="text-[11px]
                                               font-medium
                                               text-[#0F172A]"
                                    >
                                        Quantité
                                    </p>

                                    <p
                                        class="mt-0.5
                                               text-[11px]
                                               text-[#64748B]"
                                    >
                                        {{ equipment.quantity }}
                                        disponibles
                                    </p>

                                </div>

                            </div>


                            <!-- TYPE -->

                            <div
                                class="flex items-center gap-3"
                            >

                                <div
                                    class="flex h-9 w-9
                                           items-center
                                           justify-center
                                           rounded-[9px]
                                           bg-[#EEF2FF]
                                           text-[#4F46E5]"
                                >
                                    <Monitor
                                        :size="16"
                                        :stroke-width="1.8"
                                    />
                                </div>

                                <div>

                                    <p
                                        class="text-[11px]
                                               font-medium
                                               text-[#0F172A]"
                                    >
                                        Type
                                    </p>

                                    <p
                                        class="mt-0.5
                                               text-[11px]
                                               text-[#64748B]"
                                    >
                                        {{ equipment.type }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- BOUTON RÉSERVER -->
                        <!-- ================================================= -->

                        <div
                            class="mt-5 flex items-center
                                   justify-end"
                        >

                            <button
                                type="button"
                                aria-label="Réserver cet équipement"
                                class="flex h-[48px] w-[48px]
                                       items-center justify-center
                                       rounded-full
                                       bg-[#0F172A]
                                       text-white
                                       shadow-[0_6px_20px_-5px_rgba(15,23,42,0.25)]
                                       transition-all duration-200
                                       hover:scale-105
                                       hover:bg-[#4F46E5]
                                       active:scale-95"
                            >

                                <ArrowUpRight
                                    :size="19"
                                    :stroke-width="1.8"
                                />

                            </button>

                        </div>

                    </div>

                </article>

            </div>

        </div>
    </section>
</template>