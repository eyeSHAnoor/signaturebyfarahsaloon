<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';

defineOptions({ layout: SalonLayout });
defineProps<{
    appointment: {
        reference: string;
        customer_name: string;
        appointment_date: string;
        start_time: string;
        status: string;
        price: string;
        service: { name: string; duration_minutes: number };
        stylist?: { name: string } | null;
    };
}>();
</script>

<template>
    <Head title="Your visit is booked | Signature by Farah" />
    <section class="mx-auto max-w-2xl px-6 py-20 text-center md:py-28">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-[#c6a575] font-serif text-3xl text-[#a98150]">✓</div>
        <p class="mt-7 text-[10px] tracking-[0.25em] text-[#a98150]">YOUR MOMENT IS ON THE CALENDAR</p>
        <h1 class="mt-4 font-serif text-5xl font-normal">We can’t wait to see you.</h1>
        <p class="mt-5 text-sm leading-7 text-[#72695f]">Thanks, {{ appointment.customer_name }}. Your appointment request is saved and ready for our team.</p>
        <div class="mt-10 border border-[#e2d9ce] bg-white p-7 text-left">
            <div class="flex justify-between gap-5 border-b border-[#e9e1d7] pb-5"><div><p class="text-[9px] tracking-[0.15em] text-[#a49a8e]">YOUR VISIT</p><p class="mt-2 font-serif text-2xl">{{ appointment.service.name }}</p></div><p class="font-serif text-xl text-[#8b6842]">{{ formatPkr(appointment.price) }}</p></div>
            <div class="mt-5 grid grid-cols-2 gap-5 text-sm"><div><p class="text-[9px] tracking-[0.15em] text-[#a49a8e]">WHEN</p><p class="mt-2">{{ new Date(`${appointment.appointment_date}T00:00:00`).toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }) }}</p><p class="mt-1">{{ appointment.start_time.slice(0, 5) }} · {{ appointment.service.duration_minutes }} minutes</p></div><div><p class="text-[9px] tracking-[0.15em] text-[#a49a8e]">WITH</p><p class="mt-2">{{ appointment.stylist?.name || 'Our Signature by Farah team' }}</p><p class="mt-1 capitalize text-[#82796f]">{{ appointment.status }}</p></div></div>
            <p class="mt-6 border-t border-[#e9e1d7] pt-4 text-[10px] tracking-[0.12em] text-[#82796f]">REFERENCE · {{ appointment.reference.slice(0, 8).toUpperCase() }}</p>
        </div>
        <div class="mt-8 flex flex-wrap justify-center gap-4"><Link :href="route('home')" class="border border-[#d8cfc3] px-6 py-4 text-[10px] tracking-[0.15em]">BACK TO SIGNATURE BY FARAH</Link><Link v-if="$page.props.auth?.user" :href="route('dashboard')" class="bg-[#a98150] px-6 py-4 text-[10px] tracking-[0.15em] text-white">VIEW MY VISITS ↗</Link></div>
    </section>
</template>
