<script setup lang="ts">
import axios from 'axios';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';

defineOptions({ layout: SalonLayout });

const props = defineProps<{
    services: Array<{ id: number; name: string; duration_minutes: number; price: string }>;
    stylists: Array<{ id: number; name: string; title: string }>;
    customer: { name: string; email: string; phone?: string } | null;
    selectedService?: string | null;
}>();

const form = useForm({
    service_id: props.selectedService ? Number(props.selectedService) : '',
    stylist_id: '',
    appointment_date: '',
    appointment_time: '',
    customer_name: props.customer?.name || '',
    customer_email: props.customer?.email || '',
    customer_phone: props.customer?.phone || '',
    notes: '',
});
const times = ref<string[]>([]);
const loadingTimes = ref(false);
const availabilityError = ref('');
const today = new Date().toISOString().slice(0, 10);
const selectedService = computed(() => props.services.find((service) => service.id === Number(form.service_id)));

watch(
    () => [form.service_id, form.stylist_id, form.appointment_date],
    async ([serviceId, stylistId, date], _oldValue, onCleanup) => {
        times.value = [];
        form.appointment_time = '';
        availabilityError.value = '';
        if (!serviceId || !stylistId || !date) return;

        const controller = new AbortController();
        onCleanup(() => controller.abort());
        loadingTimes.value = true;
        try {
            const response = await axios.get<{ times: string[] }>(route('bookings.availability'), {
                params: { service_id: serviceId, stylist_id: stylistId, date },
                signal: controller.signal,
            });
            times.value = response.data.times;
            if (times.value.length === 0) availabilityError.value = 'There are no openings for this combination. Try another date or stylist.';
        } catch (error) {
            if (!axios.isCancel(error)) availabilityError.value = 'We could not load openings. Please try again.';
        } finally {
            if (!controller.signal.aborted) loadingTimes.value = false;
        }
    },
);

function submit() {
    form.post(route('bookings.store'));
}
</script>

<template>
    <Head title="Book a Beauty Appointment in Faisalabad | Signature by Farah" />
    <section class="bg-[#eee6dc] px-6 py-14 text-center md:py-20"><p class="text-[10px] tracking-[0.25em] text-[#a98150]">A LITTLE TIME, JUST FOR YOU</p><h1 class="mt-4 font-serif text-5xl font-normal md:text-6xl">Let’s make it yours.</h1><p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-[#72695f]">Pick a ritual, find your person, and choose a time that feels right.</p></section>
    <section class="mx-auto grid max-w-6xl gap-10 px-6 py-12 md:grid-cols-[1fr_320px] md:py-16">
        <form class="space-y-9" @submit.prevent="submit">
            <section>
                <div class="mb-5 flex items-center gap-4"><span class="font-serif text-3xl text-[#a98150]">01</span><div><h2 class="font-serif text-2xl">Choose your ritual</h2><p class="mt-1 text-xs text-[#82796f]">We’ll take care of the rest.</p></div></div>
                <select v-model="form.service_id" required class="w-full border border-[#ded5c9] bg-white px-4 py-4 text-sm outline-none focus:border-[#a98150]"><option value="" disabled>Select a service</option><option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }} · {{ formatPkr(service.price) }} · {{ service.duration_minutes }} min</option></select>
                <p v-if="form.errors.service_id" class="mt-2 text-sm text-red-700">{{ form.errors.service_id }}</p>
            </section>
            <section>
                <div class="mb-5 flex items-center gap-4"><span class="font-serif text-3xl text-[#a98150]">02</span><div><h2 class="font-serif text-2xl">Choose your person</h2><p class="mt-1 text-xs text-[#82796f]">Someone lovely will look after you.</p></div></div>
                <select v-model="form.stylist_id" required class="w-full border border-[#ded5c9] bg-white px-4 py-4 text-sm outline-none focus:border-[#a98150]"><option value="" disabled>Select a stylist</option><option v-for="stylist in stylists" :key="stylist.id" :value="stylist.id">{{ stylist.name }} — {{ stylist.title }}</option></select>
                <p v-if="form.errors.stylist_id" class="mt-2 text-sm text-red-700">{{ form.errors.stylist_id }}</p>
            </section>
            <section>
                <div class="mb-5 flex items-center gap-4"><span class="font-serif text-3xl text-[#a98150]">03</span><div><h2 class="font-serif text-2xl">Find a time</h2><p class="mt-1 text-xs text-[#82796f]">We’re here Tuesday to Saturday, 9am–7pm.</p></div></div>
                <input v-model="form.appointment_date" type="date" :min="today" required class="w-full border border-[#ded5c9] bg-white px-4 py-4 text-sm outline-none focus:border-[#a98150]">
                <p v-if="form.errors.appointment_date" class="mt-2 text-sm text-red-700">{{ form.errors.appointment_date }}</p>
                <div v-if="form.service_id && form.stylist_id && form.appointment_date" class="mt-5">
                    <p v-if="loadingTimes" class="text-sm text-[#82796f]">Finding a little opening for you…</p>
                    <p v-else-if="availabilityError" class="text-sm text-[#82796f]">{{ availabilityError }}</p>
                    <div v-else class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                        <button v-for="time in times" :key="time" type="button" class="border px-3 py-3 text-xs transition" :class="form.appointment_time === time ? 'border-[#a98150] bg-[#a98150] text-white' : 'border-[#ded5c9] bg-white hover:border-[#a98150]'" @click="form.appointment_time = time">{{ time }}</button>
                    </div>
                </div>
                <p v-if="form.errors.appointment_time" class="mt-2 text-sm text-red-700">{{ form.errors.appointment_time }}</p>
            </section>
            <section>
                <div class="mb-5 flex items-center gap-4"><span class="font-serif text-3xl text-[#a98150]">04</span><div><h2 class="font-serif text-2xl">A few details</h2><p class="mt-1 text-xs text-[#82796f]">So we know who to welcome.</p></div></div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-2 text-xs text-[#655c52]">Your name<input v-model="form.customer_name" required autocomplete="name" class="border border-[#ded5c9] bg-white px-4 py-3 text-sm"></label>
                    <label class="grid gap-2 text-xs text-[#655c52]">Phone number<input v-model="form.customer_phone" required autocomplete="tel" class="border border-[#ded5c9] bg-white px-4 py-3 text-sm"></label>
                    <label class="grid gap-2 text-xs text-[#655c52] sm:col-span-2">Email address<input v-model="form.customer_email" required type="email" autocomplete="email" class="border border-[#ded5c9] bg-white px-4 py-3 text-sm"></label>
                    <label class="grid gap-2 text-xs text-[#655c52] sm:col-span-2">Anything you’d like us to know? <span class="text-[#9b9186]">(optional)</span><textarea v-model="form.notes" rows="3" class="resize-y border border-[#ded5c9] bg-white px-4 py-3 text-sm"></textarea></label>
                </div>
                <p v-if="form.errors.customer_name || form.errors.customer_phone || form.errors.customer_email" class="mt-3 text-sm text-red-700">{{ form.errors.customer_name || form.errors.customer_phone || form.errors.customer_email }}</p>
            </section>
            <button type="submit" :disabled="form.processing || !form.appointment_time" class="w-full bg-[#a98150] px-6 py-4 text-[10px] tracking-[0.2em] text-white transition hover:bg-[#84643c] disabled:cursor-not-allowed disabled:opacity-50">{{ form.processing ? 'SAVING YOUR VISIT…' : 'CONFIRM MY VISIT ↗' }}</button>
        </form>
        <aside class="h-fit bg-[#f0e9df] p-6 md:sticky md:top-8">
            <p class="text-[9px] tracking-[0.2em] text-[#a98150]">A NOTE BEFORE YOU BOOK</p><h2 class="mt-4 font-serif text-2xl">We’ll be ready for you.</h2><p class="mt-3 text-sm leading-6 text-[#72695f]">Your booking is confirmed as soon as you submit. We’ll use your contact details if we need to follow up about your visit.</p>
            <div v-if="selectedService" class="mt-7 border-t border-[#d9cfc1] pt-5"><p class="text-[9px] tracking-[0.16em] text-[#a49a8e]">YOUR RITUAL</p><p class="mt-2 font-serif text-xl">{{ selectedService.name }}</p><p class="mt-1 text-sm text-[#82796f]">{{ selectedService.duration_minutes }} minutes · {{ formatPkr(selectedService.price) }}</p></div>
            <p class="mt-7 border-t border-[#d9cfc1] pt-5 text-xs leading-6 text-[#82796f]">Need to change plans? Call us at <a class="text-[#8b6842] underline" href="tel:03336601912">03336601912</a>.</p>
            <p class="mt-3 text-xs text-[#82796f]">Already have an account? <Link :href="route('login')" class="text-[#8b6842] underline">Sign in</Link> before booking to see your visit in your dashboard.</p>
        </aside>
    </section>
</template>
