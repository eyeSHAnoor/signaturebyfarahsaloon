<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import ValidationErrors from '@/components/ValidationErrors.vue';

const page = usePage<{
    service?: { name: string; description: string; image_url?: string; price: string; active: boolean };
    salonCategories?: Array<{ id: number; name: string; slug: string }>;
    settings?: Record<string, string>;
}>();
const sidebarOpen = ref(false);
const user = page.props.auth?.user;
const isAdmin = Array.isArray(user?.roles) && user.roles.includes('super_admin');
const pathname = computed(() => new URL(page.url, page.props.seo.baseUrl).pathname);
const canonicalUrl = computed(() => new URL(pathname.value, `${page.props.seo.baseUrl}/`).href);
const siteName = 'Signature by Farah';
const salonAddress = computed(
    () => page.props.settings?.address || 'Signature by Farah, 887/B, Peoples Colony No. 1, near Al-Fateh Sports Complex, Chen One Road, Faisalabad',
);
const mapUrl = computed(() => {
    const url = new URL('https://www.google.com/maps/search/');
    url.searchParams.set('api', '1');
    url.searchParams.set('query', salonAddress.value);

    return url.href;
});
const mapEmbedUrl = computed(() => {
    const url = new URL('https://www.google.com/maps');
    url.searchParams.set('q', salonAddress.value);
    url.searchParams.set('output', 'embed');

    return url.href;
});

const pageMetadata: Record<string, { title: string; description: string }> = {
    '/': {
        title: 'Signature by Farah | Beauty Salon in Faisalabad',
        description: 'Thoughtful hair, skin and bridal beauty at Signature by Farah in Faisalabad. Explore our services, meet the team and book your visit.',
    },
    '/about': {
        title: 'Our Story | Signature by Farah Faisalabad',
        description: 'Discover Signature by Farah’s approach to thoughtful beauty, personal care and a slower kind of luxury in Faisalabad.',
    },
    '/services': {
        title: 'Hair, Skin & Beauty Services in Faisalabad | Signature by Farah',
        description: 'Explore hair, skin, makeup and bridal beauty services at Signature by Farah in Faisalabad. Find treatment details, durations and prices, then book a visit.',
    },
    '/team': {
        title: 'Meet Our Beauty Team | Signature by Farah Faisalabad',
        description: 'Meet the friendly stylists and beauty artists at Signature by Farah in Faisalabad, and find the right person for your next visit.',
    },
    '/gallery': {
        title: 'Bridal & Beauty Gallery | Signature by Farah Faisalabad',
        description: 'Browse Pakistani bridal beauty, hair transformations and studio moments from Signature by Farah in Faisalabad.',
    },
    '/pricing': {
        title: 'Beauty Salon Prices in Faisalabad | Signature by Farah',
        description: 'View current hair, skin, makeup and bridal beauty prices at Signature by Farah in Faisalabad.',
    },
    '/contact': {
        title: 'Contact & Location | Signature by Farah Faisalabad',
        description: 'Contact Signature by Farah at Peoples Colony No. 1, Faisalabad, for directions and help planning your visit.',
    },
    '/book': {
        title: 'Book a Beauty Appointment in Faisalabad | Signature by Farah',
        description: 'Choose a service, stylist and available time to book your visit at Signature by Farah in Faisalabad.',
    },
};

const servicePage = computed(() => page.component === 'salon/ServiceDetail' && page.props.service);
const isPrivatePage = computed(
    () => ['/dashboard', '/admin', '/booking/confirmed'].some((path) => pathname.value.startsWith(path)),
);
const metadata = computed(() =>
    servicePage.value
        ? {
            title: `${servicePage.value.name} | Signature by Farah Faisalabad`,
            description: servicePage.value.description,
        }
        : (pageMetadata[pathname.value] ?? {
            title: siteName,
            description: 'Visit Signature by Farah in Faisalabad for thoughtful hair, skin and bridal beauty. Explore our services and book your appointment.',
        }),
);
const metaDescription = computed(() => {
    const homeDescription = pathname.value === '/' ? page.props.settings?.home_description : undefined;
    const description = (homeDescription || metadata.value.description).replace(/\s+/g, ' ').trim();

    return description.length > 160 ? `${description.slice(0, 157).trimEnd()}...` : description;
});
const shareImage = computed(() => new URL('/images/full-logo.png', `${page.props.seo.baseUrl}/`).href);
const structuredData = computed(() => {
    const settings = page.props.settings ?? {};
    const business = {
        '@context': 'https://schema.org',
        '@type': 'BeautySalon',
        name: siteName,
        url: new URL('/', `${page.props.seo.baseUrl}/`).href,
        image: shareImage.value,
        logo: shareImage.value,
        description: metaDescription.value,
        telephone: settings.phone || '03336601912',
        email: settings.email || 'Signaturebyfarah@gmail.com',
        address: {
            '@type': 'PostalAddress',
            streetAddress: salonAddress.value,
            addressLocality: 'Faisalabad',
            addressRegion: 'Punjab',
            addressCountry: 'PK',
        },
        openingHoursSpecification: [
            {
                '@type': 'OpeningHoursSpecification',
                dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'].map((day) => `https://schema.org/${day}`),
                opens: '10:30',
                closes: '20:00',
            },
        ],
        areaServed: { '@type': 'City', name: 'Faisalabad' },
        sameAs: settings.instagram ? [settings.instagram] : [],
    };

    return servicePage.value
        ? [
            business,
            {
                '@context': 'https://schema.org',
                '@type': 'Service',
                name: servicePage.value.name,
                description: servicePage.value.description,
                image: servicePage.value.image_url || shareImage.value,
                provider: { '@type': 'BeautySalon', name: siteName, url: business.url },
                offers: {
                    '@type': 'Offer',
                    price: servicePage.value.price,
                    priceCurrency: 'PKR',
                    availability: 'https://schema.org/InStock',
                    url: canonicalUrl.value,
                },
            },
        ]
        : [business];
});
const structuredDataJson = computed(() => JSON.stringify(structuredData.value).replace(/</g, '\\u003c'));

const links = [
    { label: 'About', route: 'salon.about' },
    { label: 'Services', route: 'salon.services' },
    { label: 'Our team', route: 'salon.team' },
    { label: 'Gallery', route: 'salon.gallery' },
    { label: 'Pricing', route: 'salon.pricing' },
    { label: 'Contact', route: 'salon.contact' },
];

// ---- Flash toast state ----
type Toast = { type: 'success' | 'error'; message: string };
const toast = ref<Toast | null>(null);
const toastVisible = ref(false);
let hideTimer: ReturnType<typeof setTimeout> | null = null;
let clearTimer: ReturnType<typeof setTimeout> | null = null;

function showToast(type: 'success' | 'error', message: string) {
    if (hideTimer) clearTimeout(hideTimer);
    if (clearTimer) clearTimeout(clearTimer);

    toast.value = { type, message };
    requestAnimationFrame(() => (toastVisible.value = true));

    hideTimer = setTimeout(() => {
        toastVisible.value = false;
        clearTimer = setTimeout(() => (toast.value = null), 300);
    }, 4000);
}

function dismissToast() {
    if (hideTimer) clearTimeout(hideTimer);
    if (clearTimer) clearTimeout(clearTimer);
    toastVisible.value = false;
    clearTimer = setTimeout(() => (toast.value = null), 300);
}

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) showToast('success', flash.success);
        else if (flash?.error) showToast('error', flash.error);
    },
    { immediate: true, deep: true },
);

// ---- Mobile sidebar ----
function openSidebar() {
    sidebarOpen.value = true;
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    sidebarOpen.value = false;
    document.body.style.overflow = '';
}

// ---- Desktop Mega Menu State ----
const megaMenuOpen = ref(false);
let megaMenuCloseTimer: ReturnType<typeof setTimeout> | null = null;

function openMegaMenu() {
    if (megaMenuCloseTimer) clearTimeout(megaMenuCloseTimer);
    megaMenuOpen.value = true;
}

function scheduleCloseMegaMenu() {
    if (megaMenuCloseTimer) clearTimeout(megaMenuCloseTimer);
    megaMenuCloseTimer = setTimeout(() => (megaMenuOpen.value = false), 150);
}

function toggleMegaMenu() {
    megaMenuOpen.value = !megaMenuOpen.value;
}

// Close on Escape
function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') {
        megaMenuOpen.value = false;
        if (sidebarOpen.value) closeSidebar();
    }
}

onMounted(() => window.addEventListener('keydown', onKeydown));

// Close menus automatically on route change
watch(pathname, () => {
    megaMenuOpen.value = false;
    if (sidebarOpen.value) closeSidebar();
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    if (hideTimer) clearTimeout(hideTimer);
    if (clearTimer) clearTimeout(clearTimer);
    if (megaMenuCloseTimer) clearTimeout(megaMenuCloseTimer);
    document.body.style.overflow = '';
});
</script>

<template>

    <Head>
        <meta name="description" :content="metaDescription" head-key="description" />
        <meta name="robots" :content="isPrivatePage ? 'noindex, nofollow' : 'index, follow'" head-key="robots" />
        <link rel="canonical" :href="canonicalUrl" head-key="canonical" />
        <meta property="og:type" content="website" head-key="og:type" />
        <meta property="og:site_name" :content="siteName" head-key="og:site_name" />
        <meta property="og:title" :content="metadata.title" head-key="og:title" />
        <meta property="og:description" :content="metaDescription" head-key="og:description" />
        <meta property="og:url" :content="canonicalUrl" head-key="og:url" />
        <meta property="og:image" :content="shareImage" head-key="og:image" />
        <meta property="og:locale" content="en_PK" head-key="og:locale" />
        <meta name="twitter:card" content="summary" head-key="twitter:card" />
        <meta name="twitter:title" :content="metadata.title" head-key="twitter:title" />
        <meta name="twitter:description" :content="metaDescription" head-key="twitter:description" />
        <meta name="twitter:image" :content="shareImage" head-key="twitter:image" />
        <component :is="'script'" type="application/ld+json" head-key="salon-structured-data"
            v-html="structuredDataJson" />
        <link rel="icon" href="/images/favicon.png" type="image/png" />
    </Head>
    <div class="salon-site min-h-screen bg-[#fbf8f3] text-[#29251f]">
        <!-- Flash toast notification -->
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-4">
            <div v-if="toast && toastVisible" role="status" aria-live="polite"
                class="fixed left-1/2 top-6 z-[60] w-[min(92vw,420px)] -translate-x-1/2">
                <div class="flex items-start gap-3 border px-4 py-3 shadow-lg backdrop-blur" :class="toast.type === 'success'
                    ? 'border-[#c6a575]/50 bg-[#efe6d8]/95 text-[#59472e]'
                    : 'border-red-200 bg-red-50/95 text-red-800'">
                    <span class="mt-0.5 shrink-0 text-base leading-none">
                        <template v-if="toast.type === 'success'">✓</template>
                        <template v-else>!</template>
                    </span>
                    <p class="flex-1 text-sm leading-6">{{ toast.message }}</p>
                    <button type="button" class="-mr-1 shrink-0 rounded p-1 text-current/60 hover:text-current"
                        aria-label="Dismiss notification" @click="dismissToast">
                        ✕
                    </button>
                </div>
            </div>
        </Transition>

        <div
            class="salon-topline hidden bg-[#29251f] px-6 py-2 text-center text-[11px] tracking-[0.16em] text-[#eee6d9] sm:block">
            A LITTLE TIME FOR YOU — BOOK YOUR NEXT VISIT
        </div>

        <header class="relative z-30 border-b border-[#e9e1d7] bg-[#fbf8f3]/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 sm:py-5 py-2 lg:px-10">
                <Link :href="route('home')" class="flex items-center gap-3 text-[#352b25]">
                    <img :src="'/images/full-logo.png'" alt="" class="h-11 w-11 object-contain">
                    <span class="font-serif text-[14px] sm:text-[20px] leading-none tracking-[0.08em]">SIGNATURE<span
                            class="ml-1 text-[#a98150]">.</span><span
                            class="mt-1 block font-sans text-[9px] tracking-[0.35em] text-[#857a6d]">BY
                            FARAH</span></span>
                </Link>

                <nav class="hidden items-center gap-7 lg:flex">
                    <!-- Desktop Mega Menu Trigger -->
                    <div v-if="page.props.salonCategories?.length" class="relative" @mouseenter="openMegaMenu"
                        @mouseleave="scheduleCloseMegaMenu">
                        <button type="button"
                            class="flex items-center gap-1.5 text-[13px] font-medium tracking-wide transition hover:text-[#a98150]"
                            :class="megaMenuOpen ? 'text-[#a98150]' : 'text-[#655c52]'" :aria-expanded="megaMenuOpen"
                            aria-haspopup="true" @click="toggleMegaMenu">
                            All Categories
                            <svg class="h-3.5 w-3.5 transition-transform duration-200"
                                :class="{ 'rotate-180': megaMenuOpen }" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <Link v-for="item in links" :key="item.route" :href="route(item.route)"
                        class="text-[13px] tracking-wide text-[#655c52] transition hover:text-[#a98150]">{{ item.label
                        }}</Link>
                </nav>

                <div class="hidden items-center gap-5 lg:flex">
                    <template v-if="user">
                        <Link :href="route('dashboard')" class="text-[13px] text-[#655c52] hover:text-[#a98150]">My
                            visits</Link>
                        <Link v-if="isAdmin" :href="route('admin.dashboard')"
                            class="text-[13px] text-[#655c52] hover:text-[#a98150]">Studio</Link>
                        <Link :href="route('logout')" method="post" as="button"
                            class="salon-signout text-[13px] text-[#655c52] hover:text-[#a98150]">Sign out</Link>
                    </template>
                    <Link v-else :href="route('login')" class="text-[13px] text-[#655c52] hover:text-[#a98150]">Sign in
                    </Link>
                    <Link :href="route('bookings.create')"
                        class="bg-[#7651a5] px-5 py-3 text-[11px] font-medium tracking-[0.14em] text-white transition hover:bg-[#62418e]">
                        BOOK A VISIT</Link>
                </div>

                <!-- Mobile hamburger -->
                <button class="relative z-50 flex h-10 w-10 items-center justify-center text-[#7651a5] lg:hidden"
                    aria-label="Open menu" :aria-expanded="sidebarOpen" @click="openSidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>
            </div>

            <!-- Desktop Mega Menu Dropdown (50% Width, Centered) -->
            <Transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2">
                <div v-if="megaMenuOpen && page.props.salonCategories?.length"
                    class="absolute left-1/2 top-full z-40 w-1/2 min-w-[700px] -translate-x-1/2 border-x border-b border-[#e9e1d7] bg-white shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hidden lg:block"
                    @mouseenter="openMegaMenu" @mouseleave="scheduleCloseMegaMenu">

                    <div class="px-8 py-10">
                        <!-- Categories Grid - 2 Columns to fit the narrower space -->
                        <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                            <div v-for="category in page.props.salonCategories" :key="category.id">
                                <Link :href="route('salon.services', { category: category.slug })"
                                    class="cursor-pointertext-[14px] hover:text-[#7651a5] text-[#29251f] mb-3">
                                    {{ category.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Bottom Quick Links -->
                        <div class="mt-10 pt-6 border-t border-[#f0eadd] flex items-center justify-between">
                            <div class="flex gap-6">
                                <Link :href="route('salon.services')"
                                    class="text-[13px] font-medium text-[#7651a5] hover:text-[#62418e] transition flex items-center gap-1">
                                    View All Services <span aria-hidden="true">→</span>
                                </Link>
                                <Link :href="route('salon.pricing')"
                                    class="text-[13px] text-[#655c52] hover:text-[#7651a5] transition">
                                    Price List
                                </Link>
                            </div>
                            <Link :href="route('bookings.create')"
                                class="text-[13px] font-medium text-[#29251f] hover:text-[#7651a5] transition">
                                Book an Appointment
                            </Link>
                        </div>
                    </div>
                </div>
            </Transition>
        </header>

        <!-- Mobile sidebar backdrop -->
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" aria-hidden="true"
                @click="closeSidebar"></div>
        </Transition>

        <!-- Mobile sidebar -->
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="translate-x-full"
            enter-to-class="translate-x-0" leave-active-class="transition duration-250 ease-in"
            leave-from-class="translate-x-0" leave-to-class="translate-x-full">
            <aside v-if="sidebarOpen"
                class="fixed right-0 top-0 z-50 flex h-full w-[min(90vw,380px)] flex-col bg-[#fbf8f3] shadow-2xl lg:hidden"
                role="dialog" aria-modal="true" aria-label="Menu">
                <!-- Sidebar header -->
                <div class="flex items-center justify-between border-b border-[#e9e1d7] px-5 py-4">
                    <Link :href="route('home')" class="flex items-center gap-2.5" @click="closeSidebar">
                        <img :src="'/images/full-logo.png'" alt="" class="h-9 w-9 object-contain">
                        <span class="font-serif text-base leading-none tracking-[0.08em] text-[#352b25]">
                            SIGNATURE<span class="ml-0.5 text-[#a98150]">.</span>
                            <span class="mt-1 block font-sans text-[8px] tracking-[0.3em] text-[#857a6d]">BY
                                FARAH</span>
                        </span>
                    </Link>
                    <button
                        class="flex h-9 w-9 items-center justify-center rounded-full text-[#655c52] transition hover:bg-[#efe6d8] hover:text-[#7651a5]"
                        aria-label="Close menu" @click="closeSidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar nav links -->
                <nav class="flex-1 overflow-y-auto px-5 py-5">

                    <!-- Mobile Categories Pill Grid -->
                    <div v-if="page.props.salonCategories?.length" class="mb-6">
                        <p class="text-[10px] font-semibold tracking-[0.18em] text-[#a98150] uppercase mb-3">Browse by
                            Category
                        </p>

                        <!-- Pill Container -->
                        <div class="flex flex-wrap gap-2">
                            <!-- All Services Pill -->
                            <Link :href="route('salon.services')"
                                class="inline-flex items-center justify-center rounded-full bg-[#f0eadd] px-4 py-2.5 text-[13px] font-medium text-[#59472e] transition hover:bg-[#7651a5] hover:text-white"
                                @click="closeSidebar">
                                All Services
                            </Link>

                            <!-- Individual Category Pills -->
                            <Link v-for="category in page.props.salonCategories" :key="category.id"
                                :href="route('salon.services', { category: category.slug })"
                                class="inline-flex items-center justify-center gap-2 rounded-full border border-[#e9e1d7] bg-white px-4 py-2.5 text-[13px] text-[#4a4238] shadow-sm transition hover:border-[#7651a5] hover:bg-[#efe6d8] hover:text-[#7651a5]"
                                @click="closeSidebar">
                                {{ category.name }}
                            </Link>
                        </div>
                    </div>

                    <div class="my-6 border-t border-[#e9e1d7]"></div>

                    <!-- Main Nav Links -->
                    <div class="grid gap-0.5">
                        <p class="text-[10px] font-semibold tracking-[0.18em] text-[#a98150] uppercase mb-2 px-1">
                            Explore</p>
                        <Link v-for="item in links" :key="item.route" :href="route(item.route)"
                            class="rounded-lg px-3 py-2.5 text-[15px] text-[#4a4238] transition hover:bg-[#efe6d8] hover:text-[#7651a5]"
                            @click="closeSidebar">
                            {{ item.label }}
                        </Link>
                    </div>

                    <div class="my-6 border-t border-[#e9e1d7]"></div>

                    <!-- Account Links -->
                    <div class="grid gap-0.5 pb-4">
                        <p class="text-[10px] font-semibold tracking-[0.18em] text-[#a98150] uppercase mb-2 px-1">
                            Account</p>
                        <template v-if="user">
                            <Link :href="route('dashboard')"
                                class="rounded-lg px-3 py-2.5 text-[15px] text-[#4a4238] transition hover:bg-[#efe6d8] hover:text-[#7651a5]"
                                @click="closeSidebar">
                                My visits
                            </Link>
                            <Link v-if="isAdmin" :href="route('admin.dashboard')"
                                class="rounded-lg px-3 py-2.5 text-[15px] text-[#4a4238] transition hover:bg-[#efe6d8] hover:text-[#7651a5]"
                                @click="closeSidebar">
                                Studio
                            </Link>
                            <Link :href="route('logout')" method="post" as="button"
                                class="salon-signout rounded-lg px-3 py-2.5 text-left text-[15px] text-[#4a4238] transition hover:bg-[#efe6d8] hover:text-[#7651a5]"
                                @click="closeSidebar">
                                Sign out
                            </Link>
                        </template>
                        <Link v-else :href="route('login')"
                            class="rounded-lg px-3 py-2.5 text-[15px] text-[#4a4238] transition hover:bg-[#efe6d8] hover:text-[#7651a5]"
                            @click="closeSidebar">
                            Sign in
                        </Link>
                    </div>
                </nav>

                <!-- Sidebar footer CTA -->
                <div class="border-t border-[#e9e1d7] p-5 bg-white">
                    <Link :href="route('bookings.create')"
                        class="block w-full bg-[#7651a5] px-4 py-3.5 text-center text-[11px] font-medium tracking-[0.14em] text-white transition hover:bg-[#62418e]"
                        @click="closeSidebar">
                        BOOK A VISIT
                    </Link>
                </div>
            </aside>
        </Transition>

        <main>
            <div class="mx-auto max-w-7xl px-5 sm:pt-4 pt-0 lg:px-10">
                <ValidationErrors />
            </div>
            <slot />
        </main>
        <footer class="bg-[#29251f] text-[#f3ede4]">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 py-16 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 lg:px-10">

                <!-- Brand & About Column -->
                <div class="sm:col-span-2 md:col-span-4 lg:col-span-2">
                    <Link :href="route('home')" class="inline-flex items-center gap-3">
                        <img :src="'/images/full-logo.png'" alt="Signature by Farah Logo"
                            class="h-14 w-14 object-contain">
                        <span class="font-serif text-2xl tracking-[0.08em]">
                            SIGNATURE<span class="text-[#c6a575]">.</span>
                            <span class="mt-1 block font-sans text-[9px] tracking-[0.3em] text-[#b8afa4]">BY
                                FARAH</span>
                        </span>
                    </Link>
                    <p class="mt-6 max-w-md text-sm leading-7 text-[#b8afa4]">
                        A warm, thoughtful beauty salon for feeling a little more like yourself. Good hair, good skin,
                        and a slower kind of luxury.
                    </p>
                </div>

                <!-- Contact Info Column -->
                <div class="flex flex-col">
                    <h3 class="text-[10px] font-semibold tracking-[0.2em] text-[#c6a575] uppercase">Come Say Hello</h3>
                    <div class="mt-6 flex flex-col gap-3 text-sm leading-6 text-[#d3cbc0]">
                        <a :href="`tel:${$page.props.settings?.phone || '03336601912'}`"
                            class="transition hover:text-white">
                            {{ $page.props.settings?.phone || '03336601912' }}
                        </a>
                        <a :href="`mailto:${$page.props.settings?.email || 'Signaturebyfarah@gmail.com'}`"
                            class="transition hover:text-white break-all">
                            {{ $page.props.settings?.email || 'Signaturebyfarah@gmail.com' }}
                        </a>
                        <span class="leading-relaxed">{{ salonAddress }}</span>
                        <div class="mt-2">
                            <p class="text-[9px] tracking-[0.15em] text-[#c6a575]">OPENING HOURS</p>
                            <p class="mt-1">Every day · 10:30am–8pm</p>
                        </div>
                        <a :href="$page.props.settings?.instagram || 'https://instagram.com/'" target="_blank"
                            rel="noreferrer"
                            class="mt-1 inline-flex items-center gap-1 text-[#c6a575] transition hover:text-white">
                            Instagram <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                </div>

                <!-- Map Column -->
                <div class="flex flex-col sm:col-span-2 md:col-span-1 lg:col-span-2">
                    <h3 class="text-[10px] font-semibold tracking-[0.2em] text-[#c6a575] uppercase">Find Us</h3>
                    <div class="mt-6 flex flex-col gap-3">
                        <iframe :src="mapEmbedUrl" title="Map to Signature by Farah in Faisalabad" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="h-48 w-full border border-white/10 grayscale transition duration-500 hover:grayscale-0">
                        </iframe>
                        <a :href="mapUrl" target="_blank" rel="noreferrer"
                            class="inline-flex items-center gap-1 text-sm text-[#d3cbc0] transition hover:text-white">
                            Get directions <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="border-t border-white/10 px-6 py-6 text-center text-[10px] tracking-[0.12em] text-[#8f867b]">
                © {{ new Date().getFullYear() }} SIGNATURE BY FARAH · MADE FOR YOUR MOMENT
            </div>
        </footer>
        <a href="https://wa.me/923336601912?text=Hello%20Signature%20by%20Farah%2C%20I%27d%20like%20to%20ask%20about%20your%20services."
            target="_blank" rel="noopener noreferrer"
            aria-label="Message Signature by Farah on WhatsApp at +92 333 6601912" title="WhatsApp +92 333 6601912"
            class="fixed right-5 bottom-5 z-[35] flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition hover:scale-105 hover:bg-[#20bd5a] focus-visible:ring-4 focus-visible:ring-[#25D366]/40 focus-visible:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-7 w-7" aria-hidden="true">
                <path d="M20.5 11.8a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.3-5a8.4 8.4 0 1 1 16.2-3.7Z" stroke="currentColor"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                <path
                    d="M9 8.2c.2-.4.4-.4.7-.4h.5c.2 0 .4.1.5.4l.7 1.7c.1.2.1.4 0 .6l-.5.6c-.2.2-.2.4-.1.6.5.9 1.2 1.6 2.1 2.1.2.1.4.1.6-.1l.6-.6c.2-.2.4-.2.6-.1l1.6.8c.3.1.4.3.4.5 0 .4-.2 1.1-.6 1.5-.4.4-1.1.7-1.8.6-1-.1-2.4-.7-3.9-2-1.2-1.1-2.1-2.5-2.3-3.5-.2-.9.1-1.8.5-2.3Z"
                    fill="currentColor" />
            </svg>
        </a>
    </div>
</template>