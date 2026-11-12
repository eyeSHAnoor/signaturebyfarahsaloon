<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';

defineOptions({ layout: SalonLayout });
defineProps<{
    service: { id: number; name: string; slug: string; description: string; duration_minutes: number; price: string; image_url?: string; category?: { name: string } };
    services: Array<{ id: number; name: string; slug: string; price: string; image_url?: string }>;
}>();
</script>

<template>
    <Head :title="`${service.name} | Signature by Farah Lahore`" />
    <section class="mx-auto grid max-w-7xl gap-10 px-6 py-12 md:grid-cols-2 md:items-center md:py-20 lg:px-10">
        <div class="overflow-hidden bg-[#e6ddd1]"><img :src="service.image_url || 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1200&q=85'" :alt="service.name" class="aspect-[4/3] w-full object-cover"></div>
        <div class="md:pl-8"><p class="text-[10px] tracking-[0.25em] text-[#a98150]">{{ service.category?.name || 'A SIGNATURE RITUAL' }}</p><h1 class="mt-5 font-serif text-5xl leading-tight font-normal md:text-6xl">{{ service.name }}</h1><p class="mt-7 text-sm leading-7 text-[#72695f]">{{ service.description }}</p><div class="mt-8 flex gap-10 border-y border-[#e4dbcf] py-5"><div><p class="text-[9px] tracking-[0.18em] text-[#a49a8e]">TIME TO TAKE</p><p class="mt-2 font-serif text-xl">{{ service.duration_minutes }} minutes</p></div><div><p class="text-[9px] tracking-[0.18em] text-[#a49a8e]">A LITTLE INVESTMENT</p><p class="mt-2 font-serif text-xl">{{ formatPkr(service.price) }}</p></div></div><Link :href="route('bookings.create', { service: service.id })" class="mt-8 inline-block bg-[#a98150] px-7 py-4 text-[10px] tracking-[0.18em] text-white">BOOK THIS RITUAL ↗</Link></div>
    </section>
    <section v-if="services.length" class="bg-[#f1ece4] px-6 py-16"><div class="mx-auto max-w-7xl"><h2 class="font-serif text-3xl">You might also love</h2><div class="mt-7 grid gap-5 sm:grid-cols-3"><Link v-for="item in services" :key="item.id" :href="route('salon.service', item.slug)" class="bg-[#fbf8f3]"><img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="aspect-[4/3] w-full object-cover"><div class="flex items-center justify-between p-4"><span class="font-serif text-xl">{{ item.name }}</span><span class="text-sm text-[#8b6842]">{{ formatPkr(item.price) }}</span></div></Link></div></div></section>
</template>
