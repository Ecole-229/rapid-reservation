<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { ref, computed } from 'vue'
import UsersFilters from '@/components/admin/UsersFilters.vue'

const authStore = useAuthStore()

const handleLogout = () => {
  authStore.logout()
}


const users = ref([
    {
        id: 1,
        name: 'Jean',
        email: 'jean@gmail.com',
        role: 'user',
    },
    {
        id: 2,
        name: 'Paul',
        email: 'paul@gmail.com',
        role: 'admin',
    },
    {
        id: 3,
        name: 'Marie',
        email: 'marie@gmail.com',
        role: 'responsable',
    },
])

const search = ref('')
const role = ref('')
const descending = ref(true)

const filteredUsers = computed(() => {
    let result = [...users.value]

    // Recherche
    if (search.value) {
        const value = search.value.toLowerCase()

        result = result.filter(user =>
            user.name.toLowerCase().includes(value) ||
            user.email.toLowerCase().includes(value)
        )
    }

    // Filtre rôle
    if (role.value) {
        result = result.filter(
            user => user.role === role.value
        )
    }

    // Tri
    result.sort((a, b) => {
        return descending.value
            ? b.id - a.id
            : a.id - b.id
    })

    return result
})

const handleSearch = (value) => {
    search.value = value
}

const handleRoleChange = (value) => {
    role.value = value
}

const handleSortChange = (value) => {
    descending.value = value
}
</script>

<template>
  <AppAdmin>
    <UsersFilters/>
    <div class="min-h-screen bg-[#F8FAFC] ">

        <!-- TITRE -->
        <div class="mb-6 mt-4">
            <h1
                class="text-[30px] font-bold tracking-[-0.8px]
                       text-[#0F172A]"
            >
                Nos Utilisateurs
            </h1>


        </div>

        <!-- FILTRES -->
        <UserFilters
            @search="handleSearch"
            @role-change="handleRoleChange"
            @sort-change="handleSortChange"
        />

        <!-- TABLE -->
        <div
            class="mt-6 overflow-hidden rounded-[16px]
                   border border-[#E2E8F0]
                   bg-white
                   shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
        >
            <table class="w-full">
                <thead>
                    <tr
                        class="border-b border-[#E2E8F0]
                               bg-[#F8FAFC]"
                    >
                        <th
                            class="px-6 py-4 text-left text-[12px]
                                   font-semibold uppercase
                                   tracking-wide text-[#64748B]"
                        >
                            Nom
                        </th>

                        <th
                            class="px-6 py-4 text-left text-[12px]
                                   font-semibold uppercase
                                   tracking-wide text-[#64748B]"
                        >
                            Email
                        </th>

                        <th
                            class="px-6 py-4 text-left text-[12px]
                                   font-semibold uppercase
                                   tracking-wide text-[#64748B]"
                        >
                            Rôle
                        </th>
                        <th
                            class="px-6 py-4 text-left text-[12px]
                                   font-semibold uppercase
                                   tracking-wide text-[#64748B]"
                        >
                            Date Inscription
                        </th>
                        <th
                            class="px-6 py-4 text-left text-[12px]
                                   font-semibold uppercase
                                   tracking-wide text-[#64748B]"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="border-b border-[#E2E8F0]
                               last:border-0
                               transition-colors duration-200
                               hover:bg-[#F8FAFC]"
                    >
                        <td
                            class="px-6 py-4 text-[14px]
                                   font-medium text-[#0F172A]"
                        >
                            {{ user.name }}
                        </td>

                        <td
                            class="px-6 py-4 text-[14px]
                                   text-[#64748B]"
                        >
                            {{ user.email }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full
                                       bg-[#EEF2FF]
                                       px-3 py-1
                                       text-[12px] font-medium
                                       text-[#3730A3]"
                            >
                                {{ user.role }}
                            </span>
                        </td>


                        <td
                            class="px-6 py-4 text-[14px]
                                   text-[#64748B]"
                        >
                            {{ user.email }}
                        </td>


                        <td
                            class="px-6 py-4 text-[14px]
                                   text-[#64748B]"
                        >
                            <RouterLink :to="{name : 'info-user' , params : {id : user.id} }">Voir</RouterLink>
                            <RouterLink :to="{name : 'update-user' , params : {id : user.id}}">Modifier</RouterLink>
                            <RouterLink :to="{name : 'delete-user', params : { id : user.id}}">Supprimer</RouterLink>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
  </AppAdmin>
</template>



