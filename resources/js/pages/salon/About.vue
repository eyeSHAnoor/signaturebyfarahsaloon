<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';

defineOptions({ layout: SalonLayout });
defineProps<{ settings: Record<string, string> }>();
</script>

<template>
    <Head title="Our Story | Signature by Farah Faisalabad" />
    <section class="grid min-h-[520px] bg-[#eee6dc] md:grid-cols-2">
        <div class="flex items-center px-7 py-16 md:px-12 lg:px-20">
            <div class="max-w-xl"><p class="text-[10px] tracking-[0.25em] text-[#a98150]">A SALON WITH SOUL</p><h1 class="mt-5 font-serif text-5xl leading-tight font-normal md:text-6xl">We believe beauty<br><em>should feel like you.</em></h1><p class="mt-7 max-w-lg text-sm leading-7 text-[#72695f]">Signature by Farah began with one simple thought: what if your salon felt less like an appointment, and more like being welcomed into a friend's home?</p><Link :href="route('bookings.create')" class="mt-8 inline-block bg-[#a98150] px-6 py-4 text-[10px] tracking-[0.18em] text-white">COME AND MEET US ↗</Link></div>
        </div>
        <img src="https://images.unsplash.com/photo-1600948836101-f9ffda59d250?auto=format&fit=crop&w=1400&q=85" alt="The Signature by Farah salon" class="h-full min-h-[360px] w-full object-cover">
    </section>
    <section class="mx-auto grid max-w-6xl gap-12 px-6 py-20 md:grid-cols-2 md:py-28">
        <div><p class="text-[10px] tracking-[0.25em] text-[#a98150]">A DIFFERENT KIND OF APPOINTMENT</p><h2 class="mt-5 font-serif text-4xl leading-tight">A little more listening.<br>A little less rushing.</h2></div>
        <div class="space-y-5 text-sm leading-7 text-[#72695f]"><p>We’re a close-knit team of hair, skin and beauty specialists who care just as much about how you feel as how you look. That means starting with a real conversation, using considered products, and making time for the details.</p><p>Our studio is a place to put your phone down, settle in and let us take care of you. Whether it’s your regular trim or the look you’ve been dreaming of for months, you’ll always find a warm welcome here.</p><p>We’re proud to be a neighborhood salon, building lasting relationships one thoughtful visit at a time.</p></div>
    </section>
    <section class="bg-[#29251f] px-6 py-16 text-center text-[#f3ede4]"><p class="font-serif text-3xl italic">“The best version of beautiful is the one that feels like you.”</p><p class="mt-5 text-[10px] tracking-[0.2em] text-[#c6a575]">THE SIGNATURE EXPERIENCE</p></section>
</template>
