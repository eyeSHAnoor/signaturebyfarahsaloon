<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';

defineOptions({ layout: SalonLayout });
defineProps<{ settings: Record<string, string> }>();
</script>

<template>
    <Head title="Contact & Location | Signature by Farah Lahore" />
    <section class="grid min-h-[550px] md:grid-cols-2">
        <div class="flex items-center bg-[#eee6dc] px-7 py-16 md:px-12 lg:px-20"><div><p class="text-[10px] tracking-[0.25em] text-[#a98150]">WE’RE EASY TO FIND</p><h1 class="mt-5 font-serif text-5xl leading-tight font-normal md:text-6xl">Come say<br><em>a little hello.</em></h1><p class="mt-6 max-w-md text-sm leading-7 text-[#72695f]">Questions about a service, your first visit, or anything else? We’re always happy to chat.</p><div class="mt-9 space-y-5 text-sm text-[#554c43]"><p><span class="block text-[9px] tracking-[0.15em] text-[#a98150]">CALL US</span><a :href="`tel:${settings.phone || '+923001234567'}`" class="mt-1 inline-block">{{ settings.phone || '+92 300 1234567' }}</a></p><p><span class="block text-[9px] tracking-[0.15em] text-[#a98150]">WRITE TO US</span><a :href="`mailto:${settings.email || 'hello@farahsalon.pk'}`" class="mt-1 inline-block">{{ settings.email || 'hello@farahsalon.pk' }}</a></p><p><span class="block text-[9px] tracking-[0.15em] text-[#a98150]">FIND THE STUDIO</span><span class="mt-1 inline-block">{{ settings.address || 'Gulberg III, Lahore, Pakistan' }}</span></p><p><span class="block text-[9px] tracking-[0.15em] text-[#a98150]">OPENING HOURS</span><span class="mt-1 inline-block">Tuesday–Saturday · 9am–7pm</span></p></div><Link :href="route('bookings.create')" class="mt-8 inline-block bg-[#29251f] px-6 py-4 text-[10px] tracking-[0.18em] text-white">BOOK A VISIT ↗</Link></div></div>
        <div class="relative min-h-[350px] bg-[#ddd2c4]"><img src="https://images.unsplash.com/photo-1600948836101-f9ffda59d250?auto=format&fit=crop&w=1200&q=85" alt="Signature by Farah salon interior" class="absolute inset-0 h-full w-full object-cover"><div class="absolute bottom-7 left-7 bg-[#fbf8f3] px-5 py-4"><p class="font-serif text-lg">Your new favourite corner.</p><a :href="`https://maps.google.com/?q=${encodeURIComponent(settings.address || 'Gulberg III Lahore Pakistan')}`" target="_blank" rel="noreferrer" class="mt-2 inline-block text-[9px] tracking-[0.15em] text-[#8b6842]">GET DIRECTIONS ↗</a></div></div>
    </section>
</template>
