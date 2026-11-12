<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';

defineOptions({ layout: SalonLayout });
defineProps<{
    appointments: Array<{
        id: number; reference: string; customer_name: string; appointment_date: string; start_time: string;
        status: string; price: string; service: { name: string; duration_minutes: number }; stylist?: { name: string } | null;
    }>;
    upcomingCount: number;
}>();
</script>

<template>
    <Head title="My visits | Signature by Farah" />
    <section class="bg-[#eee6dc] px-6 py-14 md:py-20"><div class="mx-auto max-w-6xl"><p class="text-[10px] tracking-[0.25em] text-[#a98150]">A LITTLE HOME FOR YOUR VISITS</p><h1 class="mt-4 font-serif text-5xl font-normal">Hello, {{ $page.props.auth?.user?.name }}.</h1><p class="mt-3 text-sm text-[#72695f]">You have {{ upcomingCount }} upcoming {{ upcomingCount === 1 ? 'visit' : 'visits' }}.</p></div></section>
    <section class="mx-auto max-w-6xl px-6 py-12">
        <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-[10px] tracking-[0.2em] text-[#a98150]">YOUR APPOINTMENTS</p><h2 class="mt-3 font-serif text-3xl">The moments on your calendar</h2></div><Link :href="route('bookings.create')" class="bg-[#a98150] px-5 py-3 text-[10px] tracking-[0.15em] text-white">BOOK ANOTHER ↗</Link></div>
        <div v-if="appointments.length" class="mt-8 space-y-4">
            <article v-for="appointment in appointments" :key="appointment.id" class="grid gap-5 border border-[#e4dbcf] bg-white p-6 sm:grid-cols-[1fr_auto] sm:items-center">
                <div><p class="text-[9px] tracking-[0.15em] text-[#a98150]">{{ new Date(`${appointment.appointment_date}T00:00:00`).toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }) }} · {{ appointment.start_time.slice(0, 5) }}</p><h3 class="mt-2 font-serif text-2xl">{{ appointment.service.name }}</h3><p class="mt-1 text-sm text-[#82796f]">{{ appointment.service.duration_minutes }} minutes with {{ appointment.stylist?.name || 'Signature by Farah team' }}</p><p class="mt-2 text-[10px] capitalize tracking-[0.12em] text-[#8b6842]">{{ appointment.status }} · Ref {{ appointment.reference.slice(0, 8).toUpperCase() }}</p></div>
                <div class="text-left sm:text-right"><p class="font-serif text-2xl">{{ formatPkr(appointment.price) }}</p><Link :href="route('bookings.confirmation', appointment.reference)" class="mt-2 inline-block text-[9px] tracking-[0.14em] text-[#8b6842] underline underline-offset-4">VIEW DETAILS</Link></div>
            </article>
        </div>
        <div v-else class="mt-8 border border-dashed border-[#d8cfc3] px-6 py-16 text-center"><p class="font-serif text-2xl">Your calendar is wonderfully open.</p><p class="mt-3 text-sm text-[#82796f]">Whenever you’re ready, we’ll be here.</p><Link :href="route('bookings.create')" class="mt-6 inline-block bg-[#29251f] px-6 py-4 text-[10px] tracking-[0.15em] text-white">BOOK YOUR FIRST VISIT ↗</Link></div>
    </section>
</template>
