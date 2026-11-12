<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';

defineOptions({ layout: SalonLayout });
defineProps<{ categories: Array<{ id: number; name: string; services: Array<{ id: number; name: string; slug: string; description: string; duration_minutes: number; price: string }> }> }>();
</script>

<template>
    <Head title="Beauty Salon Prices in Faisalabad | Signature by Farah" />
    <section class="bg-[#eee6dc] px-6 py-20 text-center md:py-28"><p class="text-[10px] tracking-[0.25em] text-[#a98150]">GOOD TO KNOW BEFORE YOU GO</p><h1 class="mt-5 font-serif text-5xl font-normal md:text-6xl">The little details</h1><p class="mx-auto mt-5 max-w-xl text-sm leading-7 text-[#72695f]">Clear pricing, no surprises. Every service begins with a personal consultation.</p></section>
    <section class="mx-auto max-w-4xl space-y-12 px-6 py-16">
        <div v-for="category in categories" :key="category.id"><h2 class="border-b border-[#d8cfc3] pb-4 font-serif text-3xl">{{ category.name }}</h2><div v-for="service in category.services" :key="service.id" class="border-b border-[#e8dfd3] py-5"><div class="flex items-baseline justify-between gap-4"><Link :href="route('salon.service', service.slug)" class="font-serif text-xl hover:text-[#a98150]">{{ service.name }}</Link><span class="font-serif text-xl text-[#8b6842]">{{ formatPkr(service.price) }}</span></div><div class="mt-2 flex justify-between gap-4 text-xs text-[#82796f]"><span>{{ service.description }}</span><span class="shrink-0">{{ service.duration_minutes }} min</span></div></div></div>
    </section>
    <p class="pb-16 text-center text-sm text-[#82796f]">Not sure what’s right for you? <Link :href="route('salon.contact')" class="text-[#8b6842] underline underline-offset-4">We’re happy to help.</Link></p>
</template>
