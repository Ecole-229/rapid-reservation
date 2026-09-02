<script setup>
import {
    Plus,
    Heart,
    Share2,
    MapPin,
    Star,
    Users,
    Banknote,
    ArrowUpRight,
    Maximize,
} from 'lucide-vue-next'

const rooms = [
    {
        id: 1,
        name: 'Salle Atlantique',
        image: '/images/rooms/salle-1.jpg',
        age: '2 ans',
        status: 'Disponible',
        location: 'Cotonou, Bénin',
        capacity: 25,
        price: '25 000',
        rating: '4.8',
        reviews: 22,
    },
    {
        id: 2,
        name: 'Salle Horizon',
        image: '/images/rooms/salle-2.jpg',
        age: '1 an',
        status: 'Disponible',
        location: 'Abomey-Calavi, Bénin',
        capacity: 40,
        price: '35 000',
        rating: '4.9',
        reviews: 18,
    },
    {
        id: 3,
        name: 'Salle Élite',
        image: '/images/rooms/salle-3.jpg',
        age: '3 ans',
        status: 'Occupée',
        location: 'Porto-Novo, Bénin',
        capacity: 60,
        price: '50 000',
        rating: '4.7',
        reviews: 31,
    },
    {
        id: 4,
        name: 'Salle Prestige',
        image: '/images/rooms/salle-4.jpg',
        age: '1 an',
        status: 'Disponible',
        location: 'Cotonou, Bénin',
        capacity: 80,
        price: '65 000',
        rating: '5.0',
        reviews: 14,
    },
    {
        id: 5,
        name: 'Salle Business',
        image: '/images/rooms/salle-5.jpg',
        age: '2 ans',
        status: 'Disponible',
        location: 'Cotonou, Bénin',
        capacity: 20,
        price: '20 000',
        rating: '4.6',
        reviews: 11,
    },
    {
        id: 6,
        name: 'Salle Émeraude',
        image: '/images/rooms/salle-6.jpg',
        age: '1 an',
        status: 'Disponible',
        location: 'Abomey-Calavi, Bénin',
        capacity: 35,
        price: '30 000',
        rating: '4.9',
        reviews: 27,
    },
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
                        Des salles pensées
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
                        Découvrez nos espaces disponibles et choisissez
                        celui qui correspond le mieux à vos besoins.
                    </p>
                </div>

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
                    Voir toutes les salles

                    <ArrowUpRight
                        :size="16"
                        :stroke-width="1.8"
                    />
                </button>

            </div>


            <!-- ================================================= -->
            <!-- GRILLE 3 COLONNES -->
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
                    v-for="room in rooms"
                    :key="room.id"
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
                            :src="room.image"
                            :alt="room.name"
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
                        <!-- BADGE AGE -->
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
                            {{ room.age }}
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
                                       cursor-pointer
                                       active:scale-[0.95]"
                                aria-label="Voir les détails"
                            >
                                <Plus
                                    :size="19"
                                    :stroke-width="1.8"
                                    :xlink-title="Reserver"
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
                                    {{ room.price }}
                                    <span
                                        class="ml-1 text-[12px]
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
                                    room.status === 'Disponible'
                                        ? 'bg-[#EEF2FF] text-[#3730A3]'
                                        : 'bg-[#F1F5F9] text-[#64748B]'
                                "
                            >
                                {{ room.status }}
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
                                {{ room.name }}
                            </h3>


                            <div
                                class="mt-2 flex items-center
                                       gap-2"
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
                                    {{ room.location }}
                                </span>

                                <span
                                    class="ml-auto flex
                                           items-center gap-1
                                           text-[12px]
                                           font-medium
                                           text-[#0F172A]"
                                >

                                </span>

                                <button
                                type="button"
                                class="group flex items-center
                                       gap-2
                                       rounded-full
                                       bg-[#0F172A]
                                       py-2 pl-4 pr-2
                                       text-[11px]
                                       font-semibold
                                       text-white
                                       transition-all duration-200
                                       hover:bg-[#020617]
                                       active:scale-[0.97]"
                            >

                                <span>
                                    Voir
                                </span>

                                <span
                                    class="flex h-8 w-8
                                           items-center
                                           justify-center
                                           rounded-full
                                           bg-white
                                           text-[#0F172A]
                                           transition-transform
                                           duration-200
                                           group-hover:translate-x-0.5"
                                >
                                    <ArrowUpRight
                                        :size="15"
                                        :stroke-width="1.8"
                                    />
                                </span>

                            </button>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- INFOS BAS -->
                        <!-- ================================================= -->

                        <div
                            class="my-5 h-px
                                   bg-[#E2E8F0]"
                        ></div>


                        <div
                            class="grid grid-cols-2
                                   gap-3"
                        >

                            <!-- NOM / TYPE -->
                            <div
                                class="flex items-center
                                       gap-3"
                            >
                                <div
                                    class="flex h-9 w-9
                                           items-center
                                           justify-center
                                           rounded-[9px]
                                           bg-[#EEF2FF]
                                           text-[#4F46E5]"
                                >
                                    <Users
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
                                        Capacité
                                    </p>

                                    <p
                                        class="mt-0.5
                                               text-[11px]
                                               text-[#64748B]"
                                    >
                                        {{ room.capacity }} personnes
                                    </p>
                                </div>
                            </div>


                            <!-- PRIX -->
                            <div
                                class="flex items-center
                                       gap-3"
                            >
                                <div
                                    class="flex h-9 w-9
                                           items-center
                                           justify-center
                                           rounded-[9px]
                                           bg-[#EEF2FF]
                                           text-[#4F46E5]"
                                >
                                    <Banknote
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
                                        Tarif
                                    </p>

                                    <p
                                        class="mt-0.5
                                               text-[11px]
                                               text-[#64748B]"
                                    >
                                        Par réservation
                                    </p>
                                </div>
                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- BAS : RESERVER -->
                        <!-- ================================================= -->

                        <div
                            class="mt-5 flex items-center
                                   justify-end"
                        >

                            <!-- bouton façon image 2 -->


                        </div>

                    </div>

                </article>

            </div>

        </div>
    </section>
</template>
