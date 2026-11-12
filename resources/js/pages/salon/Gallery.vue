<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SalonLayout from '@/layouts/SalonLayout.vue';

defineOptions({ layout: SalonLayout });
defineProps<{ gallery: Array<{ id: number; title: string; category?: string; image_url: string; before_image_url?: string; after_image_url?: string; alt_text?: string }> }>();
</script>

<template>

    <Head title="Bridal & Beauty Gallery | Signature by Farah Lahore" />
    <section class="bg-[#eee6dc] px-6 py-20 text-center md:py-28">
        <p class="text-[10px] tracking-[0.25em] text-[#a98150]">A FEW FAVOURITE MOMENTS</p>
        <h1 class="mt-5 font-serif text-5xl font-normal md:text-6xl">The Signature feeling</h1>
        <p class="mx-auto mt-5 max-w-xl text-sm leading-7 text-[#72695f]">Beautiful work, happy faces and the little
            details that make a visit feel special.</p>
    </section>

    <section
        class="mx-auto grid max-w-7xl grid-cols-2 gap-3 px-5 py-12 md:grid-cols-3 md:gap-5 lg:grid-cols-4 lg:px-10">
        <article v-for="(image, index) in gallery" :key="image.id" class="overflow-hidden"
            :class="index % 5 === 1 ? 'md:mt-10' : ''">
            <div v-if="image.before_image_url && image.after_image_url" class="grid grid-cols-2">
                <div class="relative"><img :src="image.before_image_url"
                        :alt="`${image.alt_text || image.title} before`" class="aspect-[4/5] w-full object-cover"><span
                        class="absolute bottom-2 left-2 bg-[#29251f]/80 px-2 py-1 text-[8px] tracking-widest text-white">BEFORE</span>
                </div>
                <div class="relative"><img :src="image.after_image_url" :alt="`${image.alt_text || image.title} after`"
                        class="aspect-[4/5] w-full object-cover"><span
                        class="absolute bottom-2 left-2 bg-[#a98150]/90 px-2 py-1 text-[8px] tracking-widest text-white">AFTER</span>
                </div>
            </div><img v-else :src="image.image_url" :alt="image.alt_text || image.title"
                class="aspect-[4/5] w-full object-cover transition duration-700 hover:scale-105">
            <div class="bg-white px-4 py-3">
                <p class="font-serif text-lg">{{ image.title }}</p>
                <p v-if="image.category" class="mt-1 text-[9px] tracking-[0.15em] text-[#a98150]">{{
                    image.category.toUpperCase() }}</p>
            </div>
        </article>
        <p v-if="gallery.length === 0" class="col-span-full py-16 text-center text-sm text-[#82796f]">Our gallery will
            be filled with lovely moments soon.</p>
    </section>
</template>
