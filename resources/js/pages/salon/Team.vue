<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';

defineOptions({ layout: SalonLayout });
defineProps<{ stylists: Array<{ id: number; name: string; title: string; bio?: string; image_url?: string }> }>();
</script>

<template>
    <Head title="Meet Our Beauty Team | Signature by Farah Faisalabad" />
    <section class="bg-[#eee6dc] px-6 py-20 text-center md:py-28"><p class="text-[10px] tracking-[0.25em] text-[#a98150]">GOOD HANDS, GOOD HEARTS</p><h1 class="mt-5 font-serif text-5xl font-normal md:text-6xl">The people who get you</h1><p class="mx-auto mt-5 max-w-xl text-sm leading-7 text-[#72695f]">A small team of brilliant artists, kind listeners and excellent tea makers.</p></section>
    <section class="mx-auto grid max-w-7xl gap-8 px-6 py-16 sm:grid-cols-2 lg:grid-cols-3 lg:px-10">
        <article v-for="stylist in stylists" :key="stylist.id" class="group"><div class="overflow-hidden bg-[#e1d5c7]"><img :src="stylist.image_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=900&q=85'" :alt="stylist.name" class="aspect-[4/4.5] w-full object-cover transition duration-700 group-hover:scale-105"></div><h2 class="mt-5 font-serif text-2xl">{{ stylist.name }}</h2><p class="mt-1 text-[10px] tracking-[0.15em] text-[#8b6842]">{{ stylist.title }}</p><p class="mt-4 text-sm leading-6 text-[#82796f]">{{ stylist.bio }}</p></article>
        <p v-if="stylists.length === 0" class="text-sm text-[#82796f]">Our lovely team will be introduced here soon.</p>
    </section>
    <div class="pb-20 text-center"><Link :href="route('bookings.create')" class="inline-block bg-[#29251f] px-7 py-4 text-[10px] tracking-[0.18em] text-white">MEET US IN THE STUDIO ↗</Link></div>
</template>
