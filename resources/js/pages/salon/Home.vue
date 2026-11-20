<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';

const heroImage = '/images/hero.jpeg';
const OwnerImage = '/images/owner.jpeg';

defineOptions({ layout: SalonLayout });
defineProps<{
    services: Array<{ id: number; name: string; slug: string; description: string; duration_minutes: number; price: string; image_url?: string; category?: { name: string } }>;
    stylists: Array<{ id: number; name: string; title: string; image_url?: string }>;
    gallery: Array<{ id: number; title: string; image_url: string; before_image_url?: string; after_image_url?: string; alt_text?: string }>;
    testimonials: Array<{ id: number; customer_name: string; quote: string; rating: number }>;
    settings: Record<string, string>;
}>();
</script>

<template>

    <Head title="Signature by Farah | Beauty Salon in Faisalabad" />
    <section class="relative min-h-[620px] overflow-hidden bg-[#40352e] md:min-h-[690px]">
        <img :src="heroImage" alt="A sunlit beauty studio"
            class="absolute inset-0 h-full w-full object-cover object-center opacity-60">
        <div class="absolute inset-0 bg-gradient-to-r from-[#28123f]/90 via-[#563375]/65 to-[#7651a5]/15"></div>
        <div class="relative mx-auto flex min-h-[620px] max-w-7xl items-center px-6 py-20 md:min-h-[690px] lg:px-10">
            <div class="max-w-2xl text-white">
                <p class="mb-7 text-[10px] tracking-[0.3em] text-[#e4c69b]">YOUR OWN LITTLE BEAUTY RITUAL</p>
                <h1 class="font-serif text-5xl leading-[1.08] font-normal md:text-7xl">{{ settings.home_heading ||
                    'Feel like yourself, only softer.' }}</h1>
                <p class="mt-7 max-w-lg text-sm leading-7 text-white/80 md:text-base">{{ settings.home_description ||
                    `Thoughtful hair, skin and beauty rituals in a calm little corner of the city. Come as you are.
                    Leave feeling quietly radiant.` }}</p>
                <div class="mt-10 flex flex-wrap items-center gap-5">
                    <Link :href="route('bookings.create')"
                        class="bg-[#bd9662] px-7 py-4 text-[10px] tracking-[0.2em] text-white transition hover:bg-[#a98150]">
                        FIND YOUR MOMENT <span class="ml-3">↗</span></Link>
                    <Link :href="route('salon.services')"
                        class="text-[10px] tracking-[0.18em] text-white/90 underline decoration-white/40 underline-offset-8 hover:text-[#e4c69b]">
                        DISCOVER OUR RITUALS</Link>
                </div>
            </div>
        </div>
        <div
            class="absolute right-8 bottom-8 hidden text-[9px] tracking-[0.25em] text-white/70 [writing-mode:vertical-rl] md:block">
            FAISALABAD · EST. 2016</div>
    </section>

    <section class="bg-[#e9e1d7] px-6 py-10 lg:px-10">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-5 md:flex-row md:items-center">
            <div>
                <p class="text-[9px] tracking-[0.22em] text-[#8b6842]">A LITTLE SOMETHING EXTRA</p>
                <h2 class="mt-2 font-serif text-3xl">{{ settings.offer_title || 'A little something extra' }}</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#72695f]">
                    {{ settings.offer_description ||
                        'Ask our team about a little extra care when you book your next visit.' }}</p>
            </div>
            <Link :href="route('bookings.create')"
                class="shrink-0 border border-[#a98150] px-6 py-4 text-[9px] tracking-[0.17em] text-[#765b38] transition hover:bg-[#a98150] hover:text-white">
                BOOK YOUR VISIT ↗</Link>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-10 px-6 py-20 md:grid-cols-12 md:items-center md:py-28 lg:px-10">
        <div class="md:col-span-5">
            <p class="text-[10px] tracking-[0.25em] text-[#a98150]">A PLACE TO EXHALE</p>
            <h2 class="mt-5 font-serif text-4xl leading-tight font-normal md:text-5xl">Beauty should feel<br><em>like
                    coming home.</em></h2>
        </div>
        <div class="md:col-span-6 md:col-start-7">
            <p class="text-sm leading-7 text-[#72695f]">At Signature by Farah, every detail is an invitation to slow
                down. Our artists take the time to understand what makes you feel good, then make something beautiful
                with it — no rush, no rules, just thoughtful care.</p>
            <Link :href="route('salon.about')"
                class="mt-6 inline-block border-b border-[#a98150] pb-2 text-[10px] tracking-[0.18em] text-[#8b6842]">
                OUR STORY <span class="ml-2">↗</span></Link>
        </div>
    </section>

    <section class="bg-[#f1ece4] px-6 py-20 md:py-24 lg:px-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-wrap items-end justify-between gap-5">
                <div>
                    <p class="text-[10px] tracking-[0.25em] text-[#a98150]">A FEW FAVOURITES</p>
                    <h2 class="mt-4 font-serif text-4xl font-normal md:text-5xl">Little rituals, big feeling.</h2>
                </div>
                <Link :href="route('salon.services')"
                    class="text-[10px] tracking-[0.18em] text-[#8b6842] underline underline-offset-8">ALL SERVICES ↗
                </Link>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="service in services" :key="service.id" class="group bg-[#fbf8f3]">
                    <Link :href="route('salon.service', service.slug)" class="block overflow-hidden">
                        <div class="relative aspect-[4/3] overflow-hidden bg-[#ddd2c4]">
                            <img :src="service.image_url || `https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=900&q=85`"
                                :alt="service.name"
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                            <span
                                class="absolute top-4 left-4 bg-[#fbf8f3]/90 px-3 py-2 text-[9px] tracking-[0.16em] text-[#725d43]">{{
                                    service.category?.name || 'BEAUTY RITUAL' }}</span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-serif text-2xl">{{ service.name }}</h3><span
                                    class="pt-1 text-sm text-[#8b6842]">{{ formatPkr(service.price) }}</span>
                            </div>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-[#82796f]">{{ service.description }}</p>
                            <p class="mt-5 text-[9px] tracking-[0.16em] text-[#a49a8e]">{{ service.duration_minutes }}
                                MINUTES <span class="float-right text-[#8b6842]">EXPLORE ↗</span></p>
                        </div>
                    </Link>
                </article>
                <div v-if="services.length === 0"
                    class="col-span-full rounded bg-[#fbf8f3] p-8 text-center text-sm text-[#82796f]">Our service menu
                    is being refreshed. Please check back soon.</div>
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-12 px-6 py-20 md:grid-cols-2 md:items-center md:py-28 lg:px-10">
        <div class="relative mx-auto w-full max-w-lg">
            <img :src="OwnerImage" alt="A moment of quiet confidence" class="aspect-[4/5] w-full object-cover">
            <div
                class="absolute -right-4 -bottom-5 bg-[#e8d9c4] px-6 py-5 font-serif text-xl italic text-[#4c4034] sm:-right-8">
                Made with care, always.</div>
        </div>
        <div class="md:pl-10">
            <p class="text-[10px] tracking-[0.25em] text-[#a98150]">THE SIGNATURE EXPERIENCE</p>
            <h2 class="mt-5 font-serif text-4xl leading-tight font-normal md:text-5xl">A good appointment<br>changes
                your whole day.</h2>
            <p class="mt-6 text-sm leading-7 text-[#72695f]">We believe the best beauty work starts with a conversation.
                Our small team of specialists pairs considered technique with a genuinely personal touch — so the result
                feels unmistakably yours.</p>
            <div class="mt-8 grid grid-cols-2 gap-6 border-t border-[#ded5c9] pt-7">
                <div><span class="font-serif text-3xl text-[#a98150]">01</span>
                    <p class="mt-2 text-[10px] tracking-[0.12em]">LISTEN FIRST, ALWAYS</p>
                </div>
                <div><span class="font-serif text-3xl text-[#a98150]">02</span>
                    <p class="mt-2 text-[10px] tracking-[0.12em]">THOUGHTFUL BY DESIGN</p>
                </div>
            </div>
            <Link :href="route('salon.about')"
                class="mt-8 inline-block bg-[#29251f] px-6 py-4 text-[10px] tracking-[0.18em] text-white hover:bg-[#51473d]">
                GET TO KNOW US ↗</Link>
        </div>
    </section>

    <section class="bg-[#e9e1d7] px-6 py-20 lg:px-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-[10px] tracking-[0.25em] text-[#a98150]">THE PEOPLE BEHIND THE MAGIC</p>
                    <h2 class="mt-4 font-serif text-4xl font-normal md:text-5xl">Good hands, good hearts.</h2>
                </div>
                <Link :href="route('salon.team')"
                    class="text-[10px] tracking-[0.18em] text-[#8b6842] underline underline-offset-8">MEET THE WHOLE
                    TEAM ↗</Link>
            </div>
            <div class="mt-12 grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="stylist in stylists" :key="stylist.id" class="group">
                    <div class="overflow-hidden bg-[#d7c8b7]"><img
                            :src="stylist.image_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=900&q=85'"
                            :alt="stylist.name"
                            class="aspect-[4/4.5] w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <h3 class="mt-5 font-serif text-2xl">{{ stylist.name }}</h3>
                    <p class="mt-1 text-[10px] tracking-[0.15em] text-[#8b6842]">{{ stylist.title }}</p>
                </article>
                <p v-if="stylists.length === 0" class="text-sm text-[#72695f]">Meet our friendly team when you visit the
                    studio.</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-20 lg:px-10">
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div>
                <p class="text-[10px] tracking-[0.25em] text-[#a98150]">A LITTLE LOOK AROUND</p>
                <h2 class="mt-4 font-serif text-4xl font-normal md:text-5xl">The Signature feeling.</h2>
            </div>
            <Link :href="route('salon.gallery')"
                class="text-[10px] tracking-[0.18em] text-[#8b6842] underline underline-offset-8">MORE FROM THE STUDIO ↗
            </Link>
        </div>
        <div class="mt-10 grid grid-cols-2 gap-3 md:grid-cols-4">
            <Link v-for="(image, index) in gallery" :key="image.id" :href="route('salon.gallery')"
                class="overflow-hidden" :class="index === 1 ? 'md:mt-10' : ''">
                <div v-if="image.before_image_url && image.after_image_url" class="grid grid-cols-2"><img
                        :src="image.before_image_url" :alt="`${image.title} before`"
                        class="aspect-[4/5] h-full w-full object-cover"><img :src="image.after_image_url"
                        :alt="`${image.title} after`" class="aspect-[4/5] h-full w-full object-cover"></div><img v-else
                    :src="image.image_url" :alt="image.alt_text || image.title"
                    class="aspect-[4/5] w-full object-cover transition duration-700 hover:scale-105">
            </Link>
        </div>
    </section>

    <section class="bg-[#29251f] px-6 py-20 text-[#f3ede4] lg:px-10">
        <div class="mx-auto max-w-7xl">
            <div class="text-center">
                <p class="text-[10px] tracking-[0.25em] text-[#c6a575]">KIND WORDS, KEPT CLOSE</p>
                <h2 class="mt-4 font-serif text-4xl font-normal md:text-5xl">We’ll remember you, too.</h2>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <article v-for="review in testimonials" :key="review.id" class="border border-white/15 p-7">
                    <p class="text-sm tracking-[0.2em] text-[#c6a575]">{{ '★'.repeat(review.rating) }}</p>
                    <p class="mt-5 font-serif text-xl leading-8 italic text-[#f1e9df]">“{{ review.quote }}”</p>
                    <p class="mt-7 text-[10px] tracking-[0.15em] text-[#c6a575]">{{ review.customer_name.toUpperCase()
                        }}</p>
                </article>
                <p v-if="testimonials.length === 0" class="col-span-full text-center text-sm text-[#c6a575]">Lovely
                    words from our guests are coming soon.</p>
            </div>
        </div>
    </section>

    <section class="px-6 py-20 text-center md:py-24">
        <p class="text-[10px] tracking-[0.25em] text-[#a98150]">YOUR CHAIR IS WAITING</p>
        <h2 class="mx-auto mt-5 max-w-2xl font-serif text-4xl leading-tight font-normal md:text-6xl">Take a
            breath.<br><em>We’ll take it from here.</em></h2>
        <Link :href="route('bookings.create')"
            class="mt-9 inline-block bg-[#a98150] px-8 py-4 text-[10px] tracking-[0.2em] text-white hover:bg-[#84643c]">
            BOOK A VISIT ↗</Link>
    </section>
</template>
