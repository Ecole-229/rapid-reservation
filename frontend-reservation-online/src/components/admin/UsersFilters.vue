<script setup>
import { ref } from 'vue'
import {
    Search,
    ChevronDown,
    ArrowDownWideNarrow,
    ArrowUpWideNarrow,
} from 'lucide-vue-next'

const emit = defineEmits([
    'search',
    'role-change',
    'sort-change',
])

const search = ref('')
const selectedRole = ref('')
const isRoleOpen = ref(false)
const sortDescending = ref(true)

const roles = [
    {
        label: 'Tous les rôles',
        value: '',
    },
    {
        label: 'User',
        value: 'user',
    },
    {
        label: 'Admin',
        value: 'admin',
    },
    {
        label: 'Responsable',
        value: 'responsable',
    },
]

const handleSearch = () => {
    emit('search', search.value)
}

const selectRole = (role) => {
    selectedRole.value = role.value
    isRoleOpen.value = false

    emit('role-change', role.value)
}

const toggleSort = () => {
    sortDescending.value = !sortDescending.value

    emit('sort-change', sortDescending.value)
}
</script>

<template>
    <div
        class="w-full rounded-[16px] border border-[#E2E8F0]
               bg-white p-4
               shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
    >
        <div
            class="flex w-full items-center gap-4"
        >
            <!-- ========================= -->
            <!-- RECHERCHE -->
            <!-- ========================= -->
            <div class="relative flex-1">
                <Search
                    :size="18"
                    :stroke-width="1.8"
                    class="absolute left-4 top-1/2
                           -translate-y-1/2 text-[#64748B]"
                />

                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher un utilisateur..."
                    class="h-[44px] w-full rounded-[10px]
                           border border-[#E2E8F0]
                           bg-white pl-11 pr-4
                           text-[14px] text-[#0F172A]
                           outline-none
                           placeholder:text-[#94A3B8]
                           transition
                           focus:border-[#4F46E5]
                           focus:ring-4 focus:ring-[#4F46E5]/10"
                    @input="handleSearch"
                />
            </div>

            <!-- ========================= -->
            <!-- FILTRE ROLE -->
            <!-- ========================= -->
            <div class="relative w-[190px]">
                <button
                    type="button"
                    class="flex h-[44px] w-full items-center
                           justify-between rounded-[10px]
                           border border-[#E2E8F0]
                           bg-white px-4
                           text-[14px] font-medium
                           text-[#0F172A]
                           outline-none
                           transition-colors duration-200
                           hover:bg-[#F8FAFC]"
                    @click="isRoleOpen = !isRoleOpen"
                >
                    <span>
                        {{
                            roles.find(
                                role => role.value === selectedRole
                            )?.label
                        }}
                    </span>

                    <ChevronDown
                        :size="17"
                        :stroke-width="1.8"
                        class="text-[#64748B] transition-transform duration-200"
                        :class="{
                            'rotate-180': isRoleOpen
                        }"
                    />
                </button>

                <!-- DROPDOWN -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 -translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-1"
                >
                    <div
                        v-if="isRoleOpen"
                        class="absolute left-0 top-[50px] z-50
                               w-full overflow-hidden
                               rounded-[10px]
                               border border-[#E2E8F0]
                               bg-white
                               shadow-[0_4px_20px_-4px_rgba(15,23,42,0.12)]"
                    >
                        <button
                            v-for="role in roles"
                            :key="role.value"
                            type="button"
                            class="flex h-[44px] w-full items-center
                                   px-4 text-left text-[14px]
                                   transition-colors duration-200
                                   hover:bg-[#EEF2FF]"
                            :class="
                                selectedRole === role.value
                                    ? 'bg-[#EEF2FF] font-medium text-[#3730A3]'
                                    : 'text-[#475569]'
                            "
                            @click="selectRole(role)"
                        >
                            {{ role.label }}
                        </button>
                    </div>
                </Transition>
            </div>

            <!-- ========================= -->
            <!-- TRI -->
            <!-- ========================= -->
            <button
                type="button"
                class="flex h-[44px] min-w-[205px]
                       items-center justify-between
                       rounded-[10px]
                       border border-[#E2E8F0]
                       bg-white px-4
                       text-[14px] font-medium
                       text-[#0F172A]
                       transition-colors duration-200
                       hover:bg-[#F8FAFC]
                       active:scale-[0.98]"
                @click="toggleSort"
            >
                <div class="flex items-center gap-3">
                    <ArrowDownWideNarrow
                        v-if="sortDescending"
                        :size="18"
                        :stroke-width="1.8"
                        class="bg-[#EEF2FF]"
                    />

                    <ArrowUpWideNarrow
                        v-else
                        :size="18"
                        :stroke-width="1.8"
                        class="bg-[#EEF2FF]"
                    />

                    <span>
                        {{
                            sortDescending
                                ? 'Ordre décroissant'
                                : 'Ordre croissant'
                        }}
                    </span>
                </div>

                <ChevronDown
                    :size="17"
                    :stroke-width="1.8"
                    class="text-[#64748B]"
                />
            </button>
        </div>
    </div>
</template>
