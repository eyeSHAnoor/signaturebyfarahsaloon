<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';

defineOptions({ layout: SalonLayout });
defineProps<{
    categories: Array<{
        id: number;
        name: string;
        description?: string;
        services: Array<{ id: number; name: string; slug: string; description: string; duration_minutes: number; price: string; image_url?: string }>;
    }>;
    categoryCards: Array<{ id: number; name: string; slug: string; image_url?: string; services_count: number }>;
}>();
const page = usePage<{ salonCategories?: Array<{ id: number; name: string; slug: string }> }>();
const selectedCategory = computed(() => {
    const category = new URL(page.url, page.props.seo.baseUrl).searchParams.get('category');

    return page.props.salonCategories?.some((item) => item.slug === category) ? category : null;
});
</script>

<template>

    <Head title="Hair, Skin & Beauty Services in Faisalabad | Signature by Farah" />

    <section class="bg-[#eee6dc] px-6 py-20 text-center md:py-28">
        <p class="text-[10px] tracking-[0.25em] text-[#a98150]">A LITTLE SOMETHING FOR YOU</p>
        <h1 class="mt-5 font-serif text-5xl font-normal md:text-6xl">Our beauty rituals</h1>
        <p class="mx-auto mt-5 max-w-xl text-sm leading-7 text-[#72695f]">Thoughtful treatments, lovely people and a
            little time to yourself. Find your next favourite.</p>
    </section>

    <!-- Thin, transparent scrolling category filter -->


    <section v-if="!selectedCategory" class="mx-auto grid max-w-7xl grid-cols-2 gap-4 px-6 py-10 md:gap-6 lg:px-10">
        <Link v-for="category in categoryCards" :key="category.id"
            :href="route('salon.services', { category: category.slug })"
            class="group relative aspect-[0.78] overflow-hidden rounded-[1.5rem] bg-[#29251f] sm:aspect-[0.9]">
            <img v-if="category.image_url" :src="category.image_url" :alt="category.name"
                class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-4 text-white sm:p-7">
                <h2 class="font-serif text-xl sm:text-3xl">{{ category.name }}</h2>
                <p class="mt-2 text-xs tracking-wide sm:text-base">{{ category.services_count }} services <span
                        aria-hidden="true">→</span></p>
            </div>
        </Link>
        <p v-if="categoryCards.length === 0" class="col-span-2 py-16 text-center text-sm text-[#82796f]">
            Our menu is being refreshed. Please check back soon.
        </p>
    </section>

    <section v-else class="mx-auto max-w-7xl space-y-20 px-6 py-16 lg:px-10">
        <Link :href="route('salon.services')" class="text-sm text-[#8b6842]">All
            categories</Link>
        <div v-for="category in categories" :key="category.id">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-[#e4dbcf] pb-5">
                <div>
                    <p class="text-[10px] tracking-[0.2em] text-[#a98150]">THE MENU</p>
                    <h2 class="mt-3 font-serif text-3xl md:text-4xl">{{ category.name }}</h2>
                </div>
                <p v-if="category.description" class="max-w-md text-sm leading-6 text-[#82796f]">{{ category.description
                }}</p>
            </div>
            <div class="mt-7 grid gap-6 sm:grid-cols-4 grid-col-2">
                <article v-for="service in category.services" :key="service.id" class="group bg-white">
                    <Link :href="route('salon.service', service.slug)" class="block">
                        <div class="aspect-[4/3] overflow-hidden bg-[#e8dfd3]"><img v-if="service.image_url"
                                :src="service.image_url" :alt="service.name"
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-105"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="font-serif text-2xl">{{ service.name }}</h3>
                                <span class="text-sm text-[#8b6842]">{{ formatPkr(service.price) }}</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-[#82796f]">{{ service.description }}</p>
                            <p class="mt-5 text-[9px] tracking-[0.14em] text-[#a49a8e]">{{ service.duration_minutes }}
                                MINUTES <span class="float-right text-[#8b6842]">DETAILS ↗</span></p>
                        </div>
                    </Link>
                </article>
                <p v-if="category.services.length === 0" class="text-sm text-[#82796f]">New rituals are coming soon.</p>
            </div>
        </div>
        <div v-if="categories.length === 0" class="py-16 text-center text-sm text-[#82796f]">No services in this
            category yet.</div>
    </section>

    <section class="bg-[#29251f] px-6 py-16 text-center text-white">
        <h2 class="font-serif text-3xl md:text-4xl">Found the one?</h2>
        <Link :href="route('bookings.create')"
            class="mt-6 inline-block bg-[#bd9662] px-7 py-4 text-[10px] tracking-[0.18em]">MAKE IT YOURS ↗</Link>
    </section>
</template>