<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="grid min-h-screen bg-[#fbf8f3] lg:grid-cols-2">
        <aside class="relative hidden min-h-screen overflow-hidden bg-[#40352e] lg:block">
            <img src="https://images.unsplash.com/photo-1600948836101-f9ffda59d250?auto=format&fit=crop&w=1400&q=85" alt="Signature by Farah salon" class="absolute inset-0 h-full w-full object-cover opacity-65">
            <div class="absolute inset-0 bg-gradient-to-t from-[#211d19]/85 via-[#211d19]/20 to-[#211d19]/30"></div>
            <Link :href="route('home')" class="absolute top-10 left-10 z-10 flex items-center gap-3 text-white"><img :src="'/images/full-logo.png'" alt="" class="h-12 w-12 object-contain"><span class="font-serif text-2xl tracking-[0.12em]">SIGNATURE<span class="text-[#e4c69b]">.</span><span class="mt-1 block font-sans text-[9px] tracking-[0.35em]">BY FARAH</span></span></Link>
            <div class="absolute bottom-14 left-10 z-10 max-w-md text-white">
                <p class="text-[9px] tracking-[0.25em] text-[#e4c69b]">A LITTLE TIME FOR YOU</p>
                <p class="mt-4 font-serif text-4xl leading-tight">A warm welcome, whenever you’re ready.</p>
                <p class="mt-4 text-sm leading-6 text-white/75">Come as you are. Your next favourite moment is just around the corner.</p>
            </div>
        </aside>
        <main class="flex min-h-screen flex-col px-6 py-7 sm:px-10 lg:px-16">
            <Link :href="route('home')" class="flex items-center gap-2 font-serif text-lg tracking-[0.12em] text-[#352b25] lg:hidden"><img :src="'/images/full-logo.png'" alt="" class="h-10 w-10 object-contain">SIGNATURE<span class="text-[#a98150]">.</span><span class="text-[9px]">BY FARAH</span></Link>
            <div class="flex flex-1 items-center justify-center py-10">
                <div class="w-full max-w-[410px]">
                    <Link :href="route('home')" class="hidden text-[9px] tracking-[0.18em] text-[#8b6842] hover:text-[#5c452b] lg:inline-block">← BACK TO SIGNATURE BY FARAH</Link>
                    <div class="mb-9 mt-5 text-center lg:mt-8">
                        <p class="text-[9px] tracking-[0.25em] text-[#a98150]">A GOOD PLACE TO BEGIN</p>
                        <h1 v-if="title" class="mt-4 font-serif text-4xl font-normal text-[#352b25]">{{ title }}</h1>
                        <p v-if="description" class="mt-3 text-sm leading-6 text-[#82796f]">{{ description }}</p>
                    </div>
                    <slot />
                    <Link :href="route('home')" class="mt-8 block text-center text-[9px] tracking-[0.15em] text-[#9b9186] hover:text-[#8b6842] lg:hidden">BACK TO THE STUDIO</Link>
                </div>
            </div>
            <p class="py-2 text-center text-[9px] tracking-[0.14em] text-[#9b9186]">© {{ new Date().getFullYear() }} SIGNATURE BY FARAH · HERE FOR YOUR MOMENT</p>
        </main>
    </div>
</template>
