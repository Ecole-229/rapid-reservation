<template>
  <nav class="w-full bg-white">
    <div
      class="mx-auto flex h-[76px] max-w-[1110px] items-center justify-between px-4">
      <!-- Logo -->
      <RouterLink
        to="/"
        class="text-[24px] font-black tracking-[-1.5px] text-black"
      >
        Lodgify
      </RouterLink>

      <!-- Navigation -->
      <div class="flex items-center gap-7">

        <RouterLink to="/" class="flex cursor-pointer items-center gap-1 text-[14px] font-semibold text-black">
          <span>Acceuil</span>
        </RouterLink>


        <RouterLink to="" class="flex cursor-pointer items-center gap-1 text-[14px] font-semibold text-black">
          <span>Salle</span>
        </RouterLink >


        <RouterLink to="" class="flex cursor-pointer items-center gap-1 text-[14px] font-semibold text-black">
          <span>Equipements</span>
        </RouterLink>

      </div>

      <div class="flex items-center gap-7">
        <RouterLink
          to="/auth/login"
          class="rounded-xl bg-[#e5f1f8] px-5 py-3 text-[14px] font-bold text-black transition hover:bg-[#d9eaf3]"
        >
          Se connecter
        </RouterLink>


        <RouterLink
          to="/auth/register"
          class="rounded-xl bg-[#111111] px-5 py-3 text-[14px] font-bold text-white transition hover:bg-black"
        >
          S'incrire
        </RouterLink>
      </div>

    </div>
  </nav>
</template>

