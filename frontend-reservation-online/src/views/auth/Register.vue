<script setup>
import Auth from '@/layouts/Auth.vue'

</script>

<template>
  <Auth>
        <div>
        <!-- Titre -->
        <h1
          class="mb-8 text-center text-[32px] font-extrabold tracking-[-1.5px] text-[#111111]"
        >
          Créer un compte
        </h1>

        <!-- Google / Apple -->
        <div class="mb-8 grid grid-cols-2 gap-4">
          <button
            type="button"
            class="flex h-[37px] items-center justify-center gap-2 rounded-full bg-[#e6f0f6] text-[14px] text-[#111111] transition hover:bg-[#dceaf2]"
          >

            <span>Inscrire toi</span>
          </button>

          <button
            type="button"
            class="flex h-[37px] items-center justify-center gap-2 rounded-full bg-[#e6f0f6] text-[14px] text-[#111111] transition hover:bg-[#dceaf2]"
          >

            <span>Et Reserve à temps</span>
          </button>
        </div>

        <!-- Formulaire -->
        <form
          class="space-y-4"
          @submit.prevent="register"
        >
          <!-- Nom -->
          <div>
            <label
              for="nom"
              class="mb-1.5 block text-[14px] font-medium text-[#111111]"
            >
              Nom <span class="text-red-500">*</span>
            </label>

            <input
              id="nom"
              type="text"
              required
              class="h-[38px] w-full rounded-[8px] border border-[#c9c9c9] bg-white px-3 outline-none transition focus:border-[#999999]"
            />
          </div>

          <!-- Email -->
          <div>
            <label
              for="email"
              class="mb-1.5 block text-[14px] font-medium text-[#111111]"
            >
              E-mail <span class="text-red-500">*</span>
            </label>

            <input
              id="email"
              type="email"
              required
              class="h-[38px] w-full rounded-[8px] border border-[#c9c9c9] bg-white px-3 outline-none transition focus:border-[#999999]"
            />
          </div>

          <!-- Mot de passe -->
          <div>
            <label
              for="password"
              class="mb-1.5 block text-[14px] font-medium text-[#111111]"
            >
              Mot de passe <span class="text-red-500">*</span>
            </label>

            <input
              id="password"
              type="password"
              required
              class="h-[38px] w-full rounded-[8px] border border-[#c9c9c9] bg-white px-3 outline-none transition focus:border-[#999999]"
            />
          </div>

          <!-- Répéter mot de passe -->
          <div>
            <label
              for="password_confirmation"
              class="mb-1.5 block text-[14px] font-medium text-[#111111]"
            >
              Répéter le mot de passe <span class="text-red-500">*</span>
            </label>

            <input
              id="password_confirmation"
              type="password"
              required
              class="h-[38px] w-full rounded-[8px] border border-[#c9c9c9] bg-white px-3 outline-none transition focus:border-[#999999]"
            />
          </div>

          <!-- Bouton -->
          <button
            type="submit"
            class="cursor-pointer mt-2 h-[38px] w-full rounded-full bg-[#111111] text-[14px] font-medium text-white transition hover:bg-black"
          >
            S'inscrire
          </button>
        </form>

        <!-- Connexion -->
        <div class="mt-7 text-center text-[14px]">
          <p class="text-[#111111]">
            Vous avez déjà un compte ?
          </p>

          <RouterLink
            :to="{name : 'login'}"
            class="text-[#3158d4] hover:underline"
          >
            Se connecter
          </RouterLink>
        </div>
      </div>
  </Auth>

</template>