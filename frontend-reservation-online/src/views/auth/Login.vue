<script setup>
import Auth from '@/layouts/Auth.vue';
import axiosClient from '@/plugins/axios';
import { ref } from 'vue';

const data = ref({
  email : '' , mot_de_passe : ''
})

const login =  async (payload) => {
   await axiosClient.get('/sanctum/csrf-cookie' ,  {baseURL : 'http://localhost:8000'});
   try {
      await axiosClient.post('/login' , payload) ;
   } catch (error) {
      return error
   }
}


</script>

<template>
  <Auth>
    <div>

      <h1 class="mb-8 text-center text-[32px] font-extrabold tracking-[-1.5px] text-[#111111]"> Connexion </h1>


      <div class="mb-9 grid grid-cols-2 gap-4">

        <button class="flex h-[37px] items-center justify-center gap-2 rounded-full bg-[#e6f0f6]" >

          <span>Connecte toi</span>
        </button>

        <button class="flex h-[37px] items-center justify-center gap-2 rounded-full bg-[#e6f0f6]">

          <span>Reserve à temps</span>
        </button>

      </div>


      <form class="space-y-4" @submit.prevent="login(data)">

        <div>
          <label class="mb-1.5 block text-[14px]">
            E-mail <span class="text-red-500">*</span>
          </label>

          <input type="email" v-model="data.email" class="h-[38px] w-full rounded-[8px] border border-[#c9c9c9] px-3 outline-none" />
        </div>

        <div>
          <label class="mb-1.5 block text-[14px]">
            Mot de passe <span class="text-red-500">*</span>
          </label>

          <input type="mot_de_passe" v-model="data.mot_de_passe"  class="h-[38px] w-full rounded-[8px] border border-[#c9c9c9] px-3 outline-none" />
        </div>

        <div class="flex justify-end">
          <RouterLink to="/forgot-password" class="text-[14px] text-[#3158d4]" > Mot de passe oublié ? </RouterLink>
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" />

          <span class="text-[14px]">
            Rester connecté
          </span>
        </div>

        <button
            type="submit"
            class=" hover:cursor-pointer mt-2 h-[38px] w-full rounded-full bg-[#111111] text-[14px] font-medium text-white transition hover:bg-black"
          >
            Connecte toi
          </button>

      </form>

      <div class="mt-9 text-center text-[14px]">
        <p>
          Vous n'êtes pas encore client chez Lodgify ?
        </p>

        <RouterLink
          :to="{name : 'register'}"
          class="text-[#3158d4]"
        >
          Inscrivez-vous
        </RouterLink>
      </div>

    </div>
  </Auth>
</template>