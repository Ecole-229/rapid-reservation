<script setup>
import AppAdmin from '@/components/admin/AppAdmin.vue';
import {
    Mail,
    Phone,
    ShieldCheck,
    CalendarDays,
    UserRound,
    Pencil,
    MapPin,
} from 'lucide-vue-next'

const user = {
    firstName: 'Tinho',
    lastName: 'Pinho',
    email: 'admin@email.com',
    phone: '+229 97 00 00 00',
    role: 'Administrateur',
    status: 'Actif',
    location: 'Cotonou, Bénin',
    createdAt: '12 août 2026',
    avatar: '/images/avatar.png',
}
</script>

<template>
  <AppAdmin>
    <div class="min-h-screen bg-[#F8FAFC] p-6 lg:p-10">

        <!-- HEADER -->
        <div class="mb-8">
            <h1
                class="text-[30px] font-bold tracking-[-0.8px]
                       text-[#0F172A]"
            >
                Profil
            </h1>

            <p class="mt-1 text-[14px] text-[#64748B]">
                Consultez et gérez vos informations personnelles.
            </p>
        </div>

        <!-- PROFILE CARD -->
        <div
            class="overflow-hidden rounded-[20px]
                   border border-[#E2E8F0]
                   bg-white
                   shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
        >

            <!-- TOP -->
            <div
                class="border-b border-[#E2E8F0]
                       px-6 py-5 lg:px-8"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <h2
                            class="text-[18px] font-semibold
                                   text-[#0F172A]"
                        >
                            Informations du profil
                        </h2>

                        <p
                            class="mt-1 text-[13px]
                                   text-[#64748B]"
                        >
                            Vos informations personnelles et votre compte.
                        </p>
                    </div>

                    <!-- EDIT -->
                    <button
                        class="flex h-[40px] items-center gap-2
                               rounded-[10px]
                               bg-[#0F172A]
                               px-4 text-[13px] font-medium
                               text-white
                               transition-all duration-150
                               hover:bg-[#020617]
                               active:scale-[0.98]"
                    >
                        <Pencil
                            :size="16"
                            :stroke-width="1.8"
                        />

                        Modifier
                    </button>
                </div>
            </div>

            <!-- CONTENT -->
            <div
                class="grid grid-cols-1
                       lg:grid-cols-[280px_1fr]"
            >

                <!-- ========================= -->
                <!-- PHOTO -->
                <!-- ========================= -->
                <div
                    class="flex flex-col items-center
                           justify-center
                           border-b border-[#E2E8F0]
                           p-8
                           lg:border-b-0
                           lg:border-r"
                >

                    <!-- PHOTO -->
                    <div
                        class="relative h-[180px] w-[180px]
                               overflow-hidden rounded-full
                               border-[6px] border-[#EEF2FF]
                               bg-[#F1F5F9]"
                    >
                        <img
                            :src="user.avatar"
                            :alt="`${user.firstName} ${user.lastName}`"
                            class="h-full w-full object-cover"
                        />
                    </div>

                    <!-- NAME -->
                    <h3
                        class="mt-5 text-[20px] font-bold
                               text-[#0F172A]"
                    >
                        {{ user.firstName }} {{ user.lastName }}
                    </h3>

                    <!-- ROLE -->
                    <span
                        class="mt-2 inline-flex items-center
                               rounded-full
                               bg-[#EEF2FF]
                               px-3 py-1
                               text-[12px] font-semibold
                               text-[#3730A3]"
                    >
                        {{ user.role }}
                    </span>

                    <!-- STATUS -->
                    <div class="mt-4 flex items-center gap-2">
                        <span
                            class="h-2 w-2 rounded-full
                                   bg-emerald-500"
                        ></span>

                        <span
                            class="text-[12px]
                                   font-medium text-[#64748B]"
                        >
                            {{ user.status }}
                        </span>
                    </div>
                </div>

                <!-- ========================= -->
                <!-- INFORMATIONS -->
                <!-- ========================= -->
                <div class="p-6 lg:p-8">

                    <div
                        class="mb-6 flex items-center gap-3"
                    >
                        <div
                            class="flex h-9 w-9 items-center
                                   justify-center rounded-[9px]
                                   bg-[#EEF2FF]"
                        >
                            <UserRound
                                :size="18"
                                :stroke-width="1.8"
                                class="text-[#4F46E5]"
                            />
                        </div>

                        <div>
                            <h3
                                class="text-[15px] font-semibold
                                       text-[#0F172A]"
                            >
                                Informations personnelles
                            </h3>

                            <p
                                class="text-[12px]
                                       text-[#94A3B8]"
                            >
                                Détails associés à votre compte
                            </p>
                        </div>
                    </div>

                    <!-- INFOS GRID -->
                    <div
                        class="grid grid-cols-1
                               gap-x-8
                               gap-y-6
                               md:grid-cols-2"
                    >

                        <!-- PRENOM -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Prénom
                            </p>

                            <p
                                class="text-[14px] font-medium
                                       text-[#0F172A]"
                            >
                                {{ user.firstName }}
                            </p>
                        </div>

                        <!-- NOM -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Nom
                            </p>

                            <p
                                class="text-[14px] font-medium
                                       text-[#0F172A]"
                            >
                                {{ user.lastName }}
                            </p>
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Adresse email
                            </p>

                            <div class="flex items-center gap-2">
                                <Mail
                                    :size="16"
                                    :stroke-width="1.8"
                                    class="text-[#64748B]"
                                />

                                <p
                                    class="text-[14px]
                                           font-medium
                                           text-[#0F172A]"
                                >
                                    {{ user.email }}
                                </p>
                            </div>
                        </div>

                        <!-- TELEPHONE -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Téléphone
                            </p>

                            <div class="flex items-center gap-2">
                                <Phone
                                    :size="16"
                                    :stroke-width="1.8"
                                    class="text-[#64748B]"
                                />

                                <p
                                    class="text-[14px]
                                           font-medium
                                           text-[#0F172A]"
                                >
                                    {{ user.phone }}
                                </p>
                            </div>
                        </div>

                        <!-- ROLE -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Rôle
                            </p>

                            <div class="flex items-center gap-2">
                                <ShieldCheck
                                    :size="17"
                                    :stroke-width="1.8"
                                    class="text-[#4F46E5]"
                                />

                                <span
                                    class="rounded-full
                                           bg-[#EEF2FF]
                                           px-3 py-1
                                           text-[12px]
                                           font-semibold
                                           text-[#3730A3]"
                                >
                                    {{ user.role }}
                                </span>
                            </div>
                        </div>

                        <!-- LOCALISATION -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Localisation
                            </p>

                            <div class="flex items-center gap-2">
                                <MapPin
                                    :size="16"
                                    :stroke-width="1.8"
                                    class="text-[#64748B]"
                                />

                                <p
                                    class="text-[14px]
                                           font-medium
                                           text-[#0F172A]"
                                >
                                    {{ user.location }}
                                </p>
                            </div>
                        </div>

                        <!-- DATE -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Membre depuis
                            </p>

                            <div class="flex items-center gap-2">
                                <CalendarDays
                                    :size="16"
                                    :stroke-width="1.8"
                                    class="text-[#64748B]"
                                />

                                <p
                                    class="text-[14px]
                                           font-medium
                                           text-[#0F172A]"
                                >
                                    {{ user.createdAt }}
                                </p>
                            </div>
                        </div>

                        <!-- STATUT -->
                        <div>
                            <p
                                class="mb-2 text-[12px]
                                       font-medium uppercase
                                       tracking-wide
                                       text-[#94A3B8]"
                            >
                                Statut du compte
                            </p>

                            <div class="flex items-center gap-2">
                                <span
                                    class="h-2 w-2 rounded-full
                                           bg-emerald-500"
                                ></span>

                                <span
                                    class="text-[14px]
                                           font-medium
                                           text-[#0F172A]"
                                >
                                    {{ user.status }}
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- SEPARATOR -->
                    <div
                        class="my-8 h-px bg-[#E2E8F0]"
                    ></div>

                    <!-- ACCOUNT SECURITY -->
                    <div
                        class="flex flex-col gap-4
                               rounded-[12px]
                               border border-[#E2E8F0]
                               bg-[#F8FAFC]
                               p-5
                               sm:flex-row
                               sm:items-center
                               sm:justify-between"
                    >
                        <div>
                            <h4
                                class="text-[14px]
                                       font-semibold
                                       text-[#0F172A]"
                            >
                                Sécurité du compte
                            </h4>

                            <p
                                class="mt-1 text-[12px]
                                       text-[#64748B]"
                            >
                                Gérez votre mot de passe et vos paramètres
                                de sécurité.
                            </p>
                        </div>

                        <button
                            class="rounded-[9px]
                                   border border-[#E2E8F0]
                                   bg-white
                                   px-4 py-2
                                   text-[13px] font-medium
                                   text-[#0F172A]
                                   transition-colors duration-200
                                   hover:bg-[#EEF2FF]
                                   hover:text-[#3730A3]"
                        >
                            Modifier le mot de passe
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
  </Appadmin>
</template>
