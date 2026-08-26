<script setup lang="ts">
import AppAdmin from '@/components/admin/AppAdmin.vue';

</script>

<template>
  <AppAdmin>
  <div class="mx-auto max-w-7xl">

    <!-- En-tête -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">
        Créer un utilisateur
      </h1>

      <p class="mt-1 text-sm text-gray-500">
        Ajoutez un nouvel utilisateur à la plateforme.
      </p>
    </div>

    <!-- Formulaire -->
    <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">

      <form @submit.prevent>

        <!-- 5 champs sur une ligne -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">

          <!-- Nom -->
          <div>
            <label
              for="name"
              class="mb-2 block text-sm font-medium text-gray-700"
            >
              Nom
            </label>

            <input
              id="name"
              type="text"
              placeholder="Nom"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <!-- Email -->
          <div>
            <label
              for="email"
              class="mb-2 block text-sm font-medium text-gray-700"
            >
              Email
            </label>

            <input
              id="email"
              type="email"
              placeholder="Email"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <!-- Téléphone -->
          <div>
            <label
              for="phone"
              class="mb-2 block text-sm font-medium text-gray-700"
            >
              Téléphone
            </label>

            <input
              id="phone"
              type="tel"
              placeholder="Téléphone"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <!-- Rôle -->
          <div>
            <label
              for="role"
              class="mb-2 block text-sm font-medium text-gray-700"
            >
              Rôle
            </label>

            <select
              id="role"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            >
              <option value="" disabled selected>
                Rôle
              </option>

              <option value="user">
                Utilisateur
              </option>

              <option value="admin">
                Administrateur
              </option>

              <option value="responsable">
                Responsable
              </option>
            </select>
          </div>

          <!-- Mot de passe -->
          <div>
            <label
              for="password"
              class="mb-2 block text-sm font-medium text-gray-700"
            >
              Mot de passe
            </label>

            <input
              id="password"
              type="password"
              placeholder="Mot de passe"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            />
          </div>

        </div>

        <!-- Confirmation -->


        <!-- Boutons -->
        <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-6">

          <RouterLink :to="{name : 'admin-users'}"
            class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
          >
            Annuler
          </RouterLink>

          <button
            type="submit"
            class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 hover:bg-blue-700"
          >
            Créer le compte
          </button>

        </div>

      </form>
    </div>
  </div>
  </AppAdmin>
</template>
