<script setup lang="ts">
import ImagePreview from '@/components/ImagePreview.vue';
import SalonLayout from '@/layouts/SalonLayout.vue';
import { formatPkr } from '@/lib/currency';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

defineOptions({ layout: SalonLayout });

type Category = { id: number; name: string; description?: string; sort_order: number; active: boolean };
type Service = {
    id: number;
    service_category_id?: number | null;
    name: string;
    description: string;
    duration_minutes: number;
    price: string;
    image_url?: string;
    featured: boolean;
    active: boolean;
    category?: Category | null;
};
type Stylist = { id: number; name: string; title: string; bio?: string; image_url?: string; active: boolean };
type Appointment = {
    id: number;
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    appointment_date: string;
    start_time: string;
    status: string;
    service: { name: string; duration_minutes: number };
    stylist?: { name: string } | null;
};
type GalleryItem = {
    id: number;
    title: string;
    category?: string;
    image_url: string;
    before_image_url?: string;
    after_image_url?: string;
    alt_text?: string;
    active: boolean;
    sort_order: number;
};
type Testimonial = {
    id: number;
    customer_name: string;
    customer_title?: string;
    quote: string;
    rating: number;
    image_url?: string;
    active: boolean;
};
type Customer = { id: number; name: string; email: string; phone?: string; status: string; created_at: string };

const props = defineProps<{
    stats: { appointments: number; pending: number; completed: number; customers: number; revenue: number };
    appointments: Appointment[];
    categories: Category[];
    services: Service[];
    stylists: Stylist[];
    gallery: GalleryItem[];
    testimonials: Testimonial[];
    customers: Customer[];
    settings: Record<string, string>;
}>();

const activeTab = ref('overview');
const editingService = ref<number | null>(null);
const editingStylist = ref<number | null>(null);
const editingCategory = ref<number | null>(null);
const editingGalleryItem = ref<number | null>(null);
const editingTestimonial = ref<number | null>(null);
const serviceForm = useForm({
    service_category_id: '',
    name: '',
    description: '',
    duration_minutes: 60,
    price: '',
    image_url: '',
    image: null as File | null,
    featured: false,
    active: true,
});
const stylistForm = useForm({ name: '', title: 'Senior Stylist', bio: '', image_url: '', image: null as File | null, active: true });
const categoryForm = useForm({ name: '', description: '', sort_order: 0, active: true });
const galleryForm = useForm({
    title: '',
    category: '',
    image_url: '',
    image: null as File | null,
    before_image_url: '',
    before_image: null as File | null,
    after_image_url: '',
    after_image: null as File | null,
    alt_text: '',
    sort_order: 0,
    active: true,
});
const testimonialForm = useForm({
    customer_name: '',
    customer_title: '',
    quote: '',
    rating: 5,
    image_url: '',
    image: null as File | null,
    active: true,
});
const settingsForm = useForm({
    phone: props.settings.phone || '',
    email: props.settings.email || '',
    address: props.settings.address || '',
    instagram: props.settings.instagram || '',
    home_heading: props.settings.home_heading || '',
    home_description: props.settings.home_description || '',
    offer_title: props.settings.offer_title || '',
    offer_description: props.settings.offer_description || '',
});
const appointmentEdits = reactive(
    Object.fromEntries(
        props.appointments.map((appointment) => [appointment.id, { status: appointment.status, appointment_date: '', start_time: '' }]),
    ),
);
const customerStatuses = reactive(Object.fromEntries(props.customers.map((customer) => [customer.id, customer.status])));

const tabs = [
    { key: 'overview', label: 'Overview & visits' },
    { key: 'services', label: 'Services & categories' },
    { key: 'team', label: 'Stylists' },
    { key: 'content', label: 'Gallery & reviews' },
    { key: 'customers', label: 'Customers' },
    { key: 'settings', label: 'Studio details' },
];

function editService(service: Service) {
    editingService.value = service.id;
    serviceForm.defaults({
        service_category_id: service.service_category_id || '',
        name: service.name,
        description: service.description,
        duration_minutes: service.duration_minutes,
        price: service.price,
        image_url: service.image_url || '',
        image: null,
        featured: service.featured,
        active: service.active,
    });
    serviceForm.reset();
}

function saveService() {
    if (editingService.value) {
        serviceForm
            .transform((data) => ({ ...data, _method: 'put' }))
            .post(route('admin.services.update', editingService.value), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    editingService.value = null;
                    serviceForm.reset();
                },
                onFinish: () => serviceForm.transform((data) => data),
            });
    } else {
        serviceForm.post(route('admin.services.store'), { forceFormData: true, preserveScroll: true, onSuccess: () => serviceForm.reset() });
    }
}

function editStylist(stylist: Stylist) {
    editingStylist.value = stylist.id;
    stylistForm.defaults({
        name: stylist.name,
        title: stylist.title,
        bio: stylist.bio || '',
        image_url: stylist.image_url || '',
        image: null,
        active: stylist.active,
    });
    stylistForm.reset();
}

function saveStylist() {
    if (editingStylist.value) {
        stylistForm
            .transform((data) => ({ ...data, _method: 'put' }))
            .post(route('admin.stylists.update', editingStylist.value), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    editingStylist.value = null;
                    stylistForm.reset();
                },
                onFinish: () => stylistForm.transform((data) => data),
            });
    } else {
        stylistForm.post(route('admin.stylists.store'), { forceFormData: true, preserveScroll: true, onSuccess: () => stylistForm.reset() });
    }
}

function selectedImage(event: Event): File | null {
    const input = event.target as HTMLInputElement;
    const image = input.files?.[0] ?? null;
    input.value = '';

    return image;
}

function editGalleryItem(item: GalleryItem) {
    editingGalleryItem.value = item.id;
    galleryForm.defaults({
        title: item.title,
        category: item.category || '',
        image_url: item.image_url || '',
        image: null,
        before_image_url: item.before_image_url || '',
        before_image: null,
        after_image_url: item.after_image_url || '',
        after_image: null,
        alt_text: item.alt_text || '',
        sort_order: item.sort_order,
        active: item.active,
    });
    galleryForm.reset();
}

function saveGalleryItem() {
    if (editingGalleryItem.value) {
        galleryForm
            .transform((data) => ({ ...data, _method: 'put' }))
            .post(route('admin.gallery.update', editingGalleryItem.value), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    editingGalleryItem.value = null;
                    galleryForm.reset();
                },
                onFinish: () => galleryForm.transform((data) => data),
            });
    } else {
        galleryForm.post(route('admin.gallery.store'), { forceFormData: true, preserveScroll: true, onSuccess: () => galleryForm.reset() });
    }
}

function editTestimonial(testimonial: Testimonial) {
    editingTestimonial.value = testimonial.id;
    testimonialForm.defaults({
        customer_name: testimonial.customer_name,
        customer_title: testimonial.customer_title || '',
        quote: testimonial.quote,
        rating: testimonial.rating,
        image_url: testimonial.image_url || '',
        image: null,
        active: testimonial.active,
    });
    testimonialForm.reset();
}

function saveTestimonial() {
    if (editingTestimonial.value) {
        testimonialForm
            .transform((data) => ({ ...data, _method: 'put' }))
            .post(route('admin.testimonials.update', editingTestimonial.value), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    editingTestimonial.value = null;
                    testimonialForm.reset();
                },
                onFinish: () => testimonialForm.transform((data) => data),
            });
    } else {
        testimonialForm.post(route('admin.testimonials.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => testimonialForm.reset(),
        });
    }
}

function saveCategory() {
    if (editingCategory.value) {
        categoryForm.put(route('admin.categories.update', editingCategory.value), {
            preserveScroll: true,
            onSuccess: () => {
                editingCategory.value = null;
                categoryForm.reset();
            },
        });
    } else {
        categoryForm.post(route('admin.categories.store'), { preserveScroll: true, onSuccess: () => categoryForm.reset() });
    }
}

function updateAppointment(appointment: Appointment) {
    const edit = appointmentEdits[appointment.id];
    if (!edit) return;
    const payload: Record<string, string> = { status: edit.status };
    if (edit.appointment_date) payload.appointment_date = edit.appointment_date;
    if (edit.start_time) payload.start_time = edit.start_time;
    router.patch(route('admin.appointments.update', appointment.id), payload, { preserveScroll: true });
}

function updateCustomer(customer: Customer) {
    router.patch(route('admin.customers.update', customer.id), { status: customerStatuses[customer.id] }, { preserveScroll: true });
}

function archive(url: string) {
    router.delete(url, { preserveScroll: true });
}
</script>

<template>
    <Head title="Studio dashboard | Signature by Farah" />
    <section class="bg-[#eee6dc] px-6 py-12 md:py-16">
        <div class="mx-auto max-w-7xl">
            <p class="text-[10px] tracking-[0.25em] text-[#a98150]">SIGNATURE BY FARAH</p>
            <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="font-serif text-5xl font-normal">Good morning.</h1>
                    <p class="mt-2 text-sm text-[#72695f]">Here’s a little look at how the studio is doing.</p>
                </div>
                <Link :href="route('home')" class="text-[10px] tracking-[0.15em] text-[#8b6842] underline underline-offset-4">VIEW WEBSITE ↗</Link>
            </div>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-8 lg:px-10">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-5 md:gap-4">
            <article
                v-for="stat in [
                    { label: 'ALL VISITS', value: stats.appointments },
                    { label: 'NEED A LOOK', value: stats.pending },
                    { label: 'COMPLETED', value: stats.completed },
                    { label: 'CUSTOMERS', value: stats.customers },
                    { label: 'REVENUE', value: formatPkr(stats.revenue) },
                ]"
                :key="stat.label"
                class="bg-white p-5"
            >
                <p class="text-[9px] tracking-[0.15em] text-[#9b9186]">{{ stat.label }}</p>
                <p class="mt-3 font-serif text-3xl">{{ stat.value }}</p>
            </article>
        </div>
        <nav class="mt-8 flex gap-2 overflow-x-auto border-b border-[#ded5c9] pb-3">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                class="shrink-0 px-4 py-3 text-[10px] tracking-[0.12em] transition"
                :class="activeTab === tab.key ? 'bg-[#29251f] text-white' : 'bg-white text-[#655c52] hover:bg-[#f1ece4]'"
                @click="activeTab = tab.key"
            >
                {{ tab.label.toUpperCase() }}
            </button>
        </nav>

        <section v-if="activeTab === 'overview'" class="mt-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-[9px] tracking-[0.2em] text-[#a98150]">YOUR NEXT HELLOS</p>
                    <h2 class="mt-2 font-serif text-3xl">Appointments</h2>
                </div>
                <p class="text-xs text-[#82796f]">Showing the latest 30 visits · Revenue counts completed services</p>
            </div>
            <div class="mt-6 space-y-3">
                <article
                    v-for="appointment in appointments"
                    :key="appointment.id"
                    class="grid gap-5 border border-[#e4dbcf] bg-white p-5 lg:grid-cols-[1fr_auto] lg:items-center"
                >
                    <div>
                        <p class="text-[9px] tracking-[0.13em] text-[#a98150]">
                            {{
                                new Date(`${appointment.appointment_date}T00:00:00`).toLocaleDateString(undefined, {
                                    weekday: 'short',
                                    month: 'short',
                                    day: 'numeric',
                                })
                            }}
                            · {{ appointment.start_time.slice(0, 5) }}
                        </p>
                        <h3 class="mt-2 font-serif text-xl">{{ appointment.customer_name }} · {{ appointment.service.name }}</h3>
                        <p class="mt-1 text-xs text-[#82796f]">
                            {{ appointment.customer_email }} · {{ appointment.customer_phone }} ·
                            {{ appointment.stylist?.name || 'Stylist unassigned' }}
                        </p>
                        <p class="mt-2 text-[9px] tracking-[0.12em] text-[#8b6842] capitalize">{{ appointment.status }}</p>
                    </div>
                    <div class="flex flex-wrap items-end gap-2">
                        <label class="grid gap-1 text-[9px] text-[#82796f]"
                            >STATUS<select
                                v-model="appointmentEdits[appointment.id]!.status"
                                class="border border-[#ded5c9] bg-[#fbf8f3] px-2 py-2 text-xs"
                            >
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="completed">Completed</option>
                                <option value="rejected">Rejected</option>
                                <option value="cancelled">Cancelled</option>
                            </select></label
                        >
                        <label class="grid gap-1 text-[9px] text-[#82796f]"
                            >NEW DATE<input
                                v-model="appointmentEdits[appointment.id]!.appointment_date"
                                type="date"
                                class="border border-[#ded5c9] bg-[#fbf8f3] px-2 py-2 text-xs"
                        /></label>
                        <label class="grid gap-1 text-[9px] text-[#82796f]"
                            >NEW TIME<input
                                v-model="appointmentEdits[appointment.id]!.start_time"
                                type="time"
                                step="1800"
                                class="border border-[#ded5c9] bg-[#fbf8f3] px-2 py-2 text-xs"
                        /></label>
                        <button class="bg-[#29251f] px-4 py-2.5 text-[9px] tracking-[0.12em] text-white" @click="updateAppointment(appointment)">
                            SAVE
                        </button>
                    </div>
                </article>
                <p v-if="appointments.length === 0" class="border border-dashed border-[#d8cfc3] p-10 text-center text-sm text-[#82796f]">
                    No appointments yet. New bookings will appear here.
                </p>
            </div>
        </section>

        <section v-else-if="activeTab === 'services'" class="mt-8 grid gap-10 lg:grid-cols-[340px_1fr]">
            <div class="space-y-8">
                <form class="space-y-3 bg-white p-5" @submit.prevent="saveCategory">
                    <h2 class="font-serif text-2xl">{{ editingCategory ? 'Edit category' : 'New category' }}</h2>
                    <input
                        v-model="categoryForm.name"
                        required
                        placeholder="Category name"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    />
                    <input
                        v-model="categoryForm.description"
                        placeholder="Short description"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    />
                    <button class="bg-[#29251f] px-4 py-3 text-[9px] tracking-[0.14em] text-white">
                        {{ editingCategory ? 'UPDATE CATEGORY' : 'ADD CATEGORY' }}
                    </button>
                    <p v-if="categoryForm.errors.name" class="text-xs text-red-700">{{ categoryForm.errors.name }}</p>
                </form>
                <form class="space-y-3 bg-white p-5" @submit.prevent="saveService">
                    <h2 class="font-serif text-2xl">{{ editingService ? 'Edit service' : 'New service' }}</h2>
                    <select v-model="serviceForm.service_category_id" class="w-full border border-[#ded5c9] bg-white px-3 py-3 text-sm">
                        <option value="">No category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <input v-model="serviceForm.name" required placeholder="Service name" class="w-full border border-[#ded5c9] px-3 py-3 text-sm" />
                    <textarea
                        v-model="serviceForm.description"
                        required
                        rows="3"
                        placeholder="Description"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    ></textarea>
                    <div class="grid grid-cols-2 gap-3">
                        <input
                            v-model.number="serviceForm.duration_minutes"
                            type="number"
                            min="30"
                            max="240"
                            step="30"
                            required
                            placeholder="Minutes"
                            class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                        /><input
                            v-model="serviceForm.price"
                            type="number"
                            min="0"
                            step="1"
                            required
                            placeholder="Price (PKR)"
                            class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                        />
                    </div>
                    <input
                        v-model="serviceForm.image_url"
                        type="url"
                        placeholder="Image URL (optional)"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    />
                    <label class="grid gap-2 text-xs text-[#655c52]"
                        >Or choose a service image from your device<input
                            type="file"
                            accept="image/*"
                            class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                            @change="serviceForm.image = selectedImage($event)"
                        /><span v-if="serviceForm.image" class="text-[#82796f]">{{ serviceForm.image.name }}</span></label
                    >
                    <ImagePreview :src="serviceForm.image_url" :file="serviceForm.image" label="Service image preview" />
                    <div class="flex gap-5 text-xs">
                        <label class="flex gap-2"><input v-model="serviceForm.featured" type="checkbox" /> Featured</label
                        ><label class="flex gap-2"><input v-model="serviceForm.active" type="checkbox" /> Available</label>
                    </div>
                    <p v-if="serviceForm.progress" role="status" class="text-xs text-[#82796f]">
                        Uploading image: {{ serviceForm.progress.percentage }}%
                    </p>
                    <button :disabled="serviceForm.processing" class="bg-[#a98150] px-4 py-3 text-[9px] tracking-[0.14em] text-white disabled:opacity-60">
                        {{ editingService ? 'UPDATE SERVICE' : 'ADD SERVICE' }}</button
                    ><button
                        v-if="editingService"
                        type="button"
                        class="ml-3 text-xs underline"
                        @click="
                            editingService = null;
                            serviceForm.reset();
                        "
                    >
                        Cancel
                    </button>
                    <p v-for="error in Object.values(serviceForm.errors)" :key="error" class="text-xs text-red-700">{{ error }}</p>
                </form>
            </div>
            <div class="space-y-8">
                <div>
                    <h2 class="font-serif text-2xl">Service categories</h2>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <article v-for="category in categories" :key="category.id" class="flex items-center justify-between bg-white p-4">
                            <div>
                                <p class="font-medium">{{ category.name }}</p>
                                <p class="text-[10px] text-[#82796f]">{{ category.active ? 'Visible' : 'Hidden' }}</p>
                            </div>
                            <div class="flex gap-3">
                                <button
                                    class="text-[9px] text-[#8b6842] underline"
                                    @click="
                                        editingCategory = category.id;
                                        categoryForm.defaults({
                                            name: category.name,
                                            description: category.description || '',
                                            sort_order: category.sort_order,
                                            active: category.active,
                                        });
                                        categoryForm.reset();
                                    "
                                >
                                    EDIT</button
                                ><button class="text-[9px] text-red-700 underline" @click="archive(route('admin.categories.destroy', category.id))">
                                    REMOVE
                                </button>
                            </div>
                        </article>
                    </div>
                </div>
                <div>
                    <h2 class="font-serif text-2xl">
                        Services <span class="text-sm text-[#82796f]">({{ services.length }})</span>
                    </h2>
                    <div class="mt-3 space-y-2">
                        <article v-for="service in services" :key="service.id" class="flex flex-wrap items-center justify-between gap-4 bg-white p-4">
                            <div>
                                <p class="font-serif text-xl">{{ service.name }}</p>
                                <p class="mt-1 text-xs text-[#82796f]">
                                    {{ service.category?.name || 'Uncategorized' }} · {{ service.duration_minutes }} min ·
                                    {{ formatPkr(service.price) }} · {{ service.active ? 'Available' : 'Archived' }}
                                </p>
                            </div>
                            <div class="flex gap-4">
                                <button class="text-[9px] text-[#8b6842] underline" @click="editService(service)">EDIT</button
                                ><button class="text-[9px] text-red-700 underline" @click="archive(route('admin.services.destroy', service.id))">
                                    ARCHIVE
                                </button>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section v-else-if="activeTab === 'team'" class="mt-8 grid gap-10 lg:grid-cols-[340px_1fr]">
            <form class="h-fit space-y-3 bg-white p-5" @submit.prevent="saveStylist">
                <h2 class="font-serif text-2xl">{{ editingStylist ? 'Edit stylist' : 'Add a stylist' }}</h2>
                <input v-model="stylistForm.name" required placeholder="Name" class="w-full border border-[#ded5c9] px-3 py-3 text-sm" /><input
                    v-model="stylistForm.title"
                    required
                    placeholder="Title"
                    class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                /><textarea
                    v-model="stylistForm.bio"
                    rows="3"
                    placeholder="Short bio"
                    class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                ></textarea
                ><input
                    v-model="stylistForm.image_url"
                    type="url"
                    placeholder="Portrait image URL"
                    class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                /><label class="grid gap-2 text-xs text-[#655c52]"
                    >Or choose a portrait from your device<input
                        type="file"
                        accept="image/*"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                        @change="stylistForm.image = selectedImage($event)"
                    /><span v-if="stylistForm.image" class="text-[#82796f]">{{ stylistForm.image.name }}</span></label
                >
                <p v-if="stylistForm.progress" role="status" class="text-xs text-[#82796f]">
                    Uploading image: {{ stylistForm.progress.percentage }}%
                </p>
                <ImagePreview :src="stylistForm.image_url" :file="stylistForm.image" label="Stylist portrait preview" />
                <label class="flex gap-2 text-xs"
                    ><input v-model="stylistForm.active" type="checkbox" /> Available for bookings</label
                ><button :disabled="stylistForm.processing" class="bg-[#a98150] px-4 py-3 text-[9px] tracking-[0.14em] text-white disabled:opacity-60">
                    {{ editingStylist ? 'UPDATE STYLIST' : 'ADD STYLIST' }}</button
                ><button
                    v-if="editingStylist"
                    type="button"
                    class="ml-3 text-xs underline"
                    @click="
                        editingStylist = null;
                        stylistForm.reset();
                    "
                >
                    Cancel
                </button>
                <p v-for="error in Object.values(stylistForm.errors)" :key="error" class="text-xs text-red-700">{{ error }}</p>
            </form>
            <div>
                <h2 class="font-serif text-2xl">Your people</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <article v-for="stylist in stylists" :key="stylist.id" class="flex gap-4 bg-white p-4">
                        <img
                            :src="stylist.image_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=160&q=80'"
                            :alt="stylist.name"
                            class="h-20 w-20 object-cover"
                        />
                        <div class="min-w-0">
                            <h3 class="font-serif text-xl">{{ stylist.name }}</h3>
                            <p class="text-xs text-[#8b6842]">{{ stylist.title }}</p>
                            <p class="mt-1 text-[10px] text-[#82796f]">{{ stylist.active ? 'Available' : 'Hidden' }}</p>
                            <div class="mt-2 flex gap-3">
                                <button class="text-[9px] underline" @click="editStylist(stylist)">EDIT</button
                                ><button class="text-[9px] text-red-700 underline" @click="archive(route('admin.stylists.destroy', stylist.id))">
                                    ARCHIVE
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section v-else-if="activeTab === 'content'" class="mt-8 grid gap-10 lg:grid-cols-2">
            <div>
                <form
                    class="space-y-3 bg-white p-5"
                    @submit.prevent="saveGalleryItem"
                >
                    <h2 class="font-serif text-2xl">{{ editingGalleryItem ? 'Edit gallery image' : 'Add a gallery image' }}</h2>
                    <input
                        v-model="galleryForm.title"
                        required
                        placeholder="Image title"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    /><input v-model="galleryForm.category" placeholder="Category" class="w-full border border-[#ded5c9] px-3 py-3 text-sm" /><input
                        v-model="galleryForm.image_url"
                        type="url"
                        placeholder="Single image URL (optional for a before-and-after pair)"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    /><label class="grid gap-2 text-xs text-[#655c52]"
                        >Or upload a gallery image<input
                            type="file"
                            accept="image/*"
                            class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                            @change="galleryForm.image = selectedImage($event)"
                        /><span v-if="galleryForm.image" class="text-[#82796f]">{{ galleryForm.image.name }}</span></label
                    >
                    <ImagePreview
                        v-if="galleryForm.image || (galleryForm.image_url && galleryForm.image_url !== galleryForm.before_image_url)"
                        :src="galleryForm.image_url"
                        :file="galleryForm.image"
                        label="Gallery image preview"
                    />
                    <div class="grid grid-cols-2 gap-3">
                        <div class="grid gap-2">
                            <input
                                v-model="galleryForm.before_image_url"
                                type="url"
                                placeholder="Before image URL"
                                class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                            /><label class="grid gap-2 text-xs text-[#655c52]"
                                >Or upload before image<input
                                    type="file"
                                    accept="image/*"
                                    class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                                    @change="galleryForm.before_image = selectedImage($event)"
                                /><span v-if="galleryForm.before_image" class="text-[#82796f]">{{ galleryForm.before_image.name }}</span></label
                            >
                            <ImagePreview :src="galleryForm.before_image_url" :file="galleryForm.before_image" label="Before image preview" />
                        </div>
                        <div class="grid gap-2">
                            <input
                                v-model="galleryForm.after_image_url"
                                type="url"
                                placeholder="After image URL"
                                class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                            /><label class="grid gap-2 text-xs text-[#655c52]"
                                >Or upload after image<input
                                    type="file"
                                    accept="image/*"
                                    class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                                    @change="galleryForm.after_image = selectedImage($event)"
                                /><span v-if="galleryForm.after_image" class="text-[#82796f]">{{ galleryForm.after_image.name }}</span></label
                            >
                            <ImagePreview :src="galleryForm.after_image_url" :file="galleryForm.after_image" label="After image preview" />
                        </div>
                    </div>
                    <input
                        v-model="galleryForm.alt_text"
                        placeholder="Accessibility description"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    />
                    <p v-if="galleryForm.progress" role="status" class="text-xs text-[#82796f]">
                        Uploading images: {{ galleryForm.progress.percentage }}%
                    </p>
                    <button :disabled="galleryForm.processing" class="bg-[#a98150] px-4 py-3 text-[9px] tracking-[0.14em] text-white disabled:opacity-60">
                        {{ editingGalleryItem ? 'UPDATE GALLERY IMAGE' : 'ADD TO GALLERY' }}
                    </button>
                    <button
                        v-if="editingGalleryItem"
                        type="button"
                        class="ml-3 text-xs underline"
                        @click="
                            editingGalleryItem = null;
                            galleryForm.reset();
                        "
                    >
                        Cancel
                    </button>
                    <p v-for="error in Object.values(galleryForm.errors)" :key="error" class="text-xs text-red-700">{{ error }}</p>
                </form>
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <article v-for="image in gallery" :key="image.id" class="bg-white">
                        <div v-if="image.before_image_url && image.after_image_url" class="grid grid-cols-2">
                            <img :src="image.before_image_url" :alt="`${image.title} before`" class="aspect-[4/3] w-full object-cover" /><img
                                :src="image.after_image_url"
                                :alt="`${image.title} after`"
                                class="aspect-[4/3] w-full object-cover"
                            />
                        </div>
                        <img v-else :src="image.image_url" :alt="image.title" class="aspect-[4/3] w-full object-cover" />
                        <div class="flex justify-between p-3">
                            <span class="text-xs">{{ image.title }}</span
                            ><div class="flex gap-3">
                                <button class="text-[9px] underline" @click="editGalleryItem(image)">EDIT</button>
                                <button class="text-[9px] text-red-700 underline" @click="archive(route('admin.gallery.destroy', image.id))">REMOVE</button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
            <div>
                <form
                    class="space-y-3 bg-white p-5"
                    @submit.prevent="saveTestimonial"
                >
                    <h2 class="font-serif text-2xl">{{ editingTestimonial ? 'Edit testimonial' : 'Add a kind word' }}</h2>
                    <input
                        v-model="testimonialForm.customer_name"
                        required
                        placeholder="Customer name"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    /><input
                        v-model="testimonialForm.customer_title"
                        placeholder="Customer title"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    /><textarea
                        v-model="testimonialForm.quote"
                        required
                        rows="4"
                        placeholder="Their review"
                        class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                    ></textarea>
                    <div class="grid grid-cols-2 gap-3">
                        <select v-model.number="testimonialForm.rating" class="border border-[#ded5c9] bg-white px-3 py-3 text-sm">
                            <option v-for="rating in [5, 4, 3, 2, 1]" :key="rating" :value="rating">{{ rating }} stars</option></select
                        ><input
                            v-model="testimonialForm.image_url"
                            type="url"
                            placeholder="Portrait URL (optional)"
                            class="border border-[#ded5c9] px-3 py-3 text-sm"
                        />
                    </div>
                    <label class="grid gap-2 text-xs text-[#655c52]"
                        >Or choose a customer photo from your device<input
                            type="file"
                            accept="image/*"
                            class="w-full border border-[#ded5c9] px-3 py-3 text-sm"
                            @change="testimonialForm.image = selectedImage($event)"
                        /><span v-if="testimonialForm.image" class="text-[#82796f]">{{ testimonialForm.image.name }}</span></label>
                        <ImagePreview :src="testimonialForm.image_url" :file="testimonialForm.image" label="Customer photo preview" />
                        <p v-if="testimonialForm.progress" role="status" class="text-xs text-[#82796f]">
                            Uploading image: {{ testimonialForm.progress.percentage }}%
                        </p>
                        <button
                            :disabled="testimonialForm.processing"
                            class="bg-[#a98150] px-4 py-3 text-[9px] tracking-[0.14em] text-white disabled:opacity-60"
                        >
                            {{ editingTestimonial ? 'UPDATE TESTIMONIAL' : 'ADD TESTIMONIAL' }}
                        </button>
                        <button
                            v-if="editingTestimonial"
                            type="button"
                            class="ml-3 text-xs underline"
                            @click="
                                editingTestimonial = null;
                                testimonialForm.reset();
                            "
                        >
                            Cancel
                        </button>
                    <p v-for="error in Object.values(testimonialForm.errors)" :key="error" class="text-xs text-red-700">{{ error }}</p>
                </form>
                <div class="mt-5 space-y-3">
                    <article v-for="review in testimonials" :key="review.id" class="flex items-start justify-between gap-4 bg-white p-4">
                        <img v-if="review.image_url" :src="review.image_url" :alt="review.customer_name" class="h-12 w-12 object-cover" />
                        <div>
                            <p class="font-serif">{{ review.customer_name }}</p>
                            <p class="mt-1 line-clamp-2 text-xs text-[#82796f]">“{{ review.quote }}”</p>
                        </div>
                        <div class="flex shrink-0 gap-3">
                            <button class="text-[9px] underline" @click="editTestimonial(review)">EDIT</button>
                            <button class="text-[9px] text-red-700 underline" @click="archive(route('admin.testimonials.destroy', review.id))">REMOVE</button>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section v-else-if="activeTab === 'settings'" class="mt-8 max-w-2xl">
            <form class="space-y-4 bg-white p-6" @submit.prevent="settingsForm.put(route('admin.settings.update'), { preserveScroll: true })">
                <p class="text-[9px] tracking-[0.18em] text-[#a98150]">COME SAY HELLO</p>
                <h2 class="font-serif text-3xl">Studio details & homepage</h2>
                <label class="grid gap-2 text-xs text-[#655c52]"
                    >Phone<input v-model="settingsForm.phone" class="border border-[#ded5c9] px-3 py-3 text-sm" /></label
                ><label class="grid gap-2 text-xs text-[#655c52]"
                    >Email<input v-model="settingsForm.email" type="email" class="border border-[#ded5c9] px-3 py-3 text-sm" /></label
                ><label class="grid gap-2 text-xs text-[#655c52]"
                    >Address<input v-model="settingsForm.address" class="border border-[#ded5c9] px-3 py-3 text-sm" /></label
                ><label class="grid gap-2 text-xs text-[#655c52]"
                    >Instagram URL<input v-model="settingsForm.instagram" type="url" class="border border-[#ded5c9] px-3 py-3 text-sm"
                /></label>
                <hr class="border-[#e8dfd3]" />
                <label class="grid gap-2 text-xs text-[#655c52]"
                    >Homepage headline<input v-model="settingsForm.home_heading" class="border border-[#ded5c9] px-3 py-3 text-sm" /></label
                ><label class="grid gap-2 text-xs text-[#655c52]"
                    >Homepage introduction<textarea
                        v-model="settingsForm.home_description"
                        rows="3"
                        class="border border-[#ded5c9] px-3 py-3 text-sm"
                    ></textarea></label
                ><label class="grid gap-2 text-xs text-[#655c52]"
                    >Offer heading<input v-model="settingsForm.offer_title" class="border border-[#ded5c9] px-3 py-3 text-sm" /></label
                ><label class="grid gap-2 text-xs text-[#655c52]"
                    >Offer details<textarea
                        v-model="settingsForm.offer_description"
                        rows="3"
                        class="border border-[#ded5c9] px-3 py-3 text-sm"
                    ></textarea></label
                ><button class="bg-[#29251f] px-5 py-4 text-[9px] tracking-[0.15em] text-white">SAVE SITE CONTENT</button>
                <p v-for="error in Object.values(settingsForm.errors)" :key="error" class="text-xs text-red-700">{{ error }}</p>
            </form>
        </section>

        <section v-else-if="activeTab === 'customers'" class="mt-8">
            <div>
                <p class="text-[9px] tracking-[0.2em] text-[#a98150]">THE PEOPLE WHO VISIT</p>
                <h2 class="mt-2 font-serif text-3xl">Customers</h2>
                <p class="mt-2 text-xs text-[#82796f]">Showing up to 100 most recently registered accounts.</p>
            </div>
            <div class="mt-6 overflow-x-auto bg-white">
                <table class="w-full min-w-[820px] text-left">
                    <thead class="border-b border-[#e4dbcf] text-[9px] tracking-[0.14em] text-[#9b9186]">
                        <tr>
                            <th class="p-4 font-normal">NAME</th>
                            <th class="p-4 font-normal">EMAIL</th>
                            <th class="p-4 font-normal">PHONE</th>
                            <th class="p-4 font-normal">JOINED</th>
                            <th class="p-4 font-normal">ACCOUNT</th>
                            <th class="p-4 font-normal"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="customer in customers" :key="customer.id" class="border-b border-[#f0e9df] text-sm">
                            <td class="p-4">{{ customer.name }}</td>
                            <td class="p-4">{{ customer.email }}</td>
                            <td class="p-4">{{ customer.phone || '—' }}</td>
                            <td class="p-4">{{ new Date(customer.created_at).toLocaleDateString() }}</td>
                            <td class="p-4">
                                <select v-model="customerStatuses[customer.id]" class="border border-[#ded5c9] bg-[#fbf8f3] px-2 py-2 text-xs">
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspend 30 days</option>
                                    <option value="banned">Banned</option>
                                </select>
                            </td>
                            <td class="p-4">
                                <button class="text-[9px] tracking-[0.12em] text-[#8b6842] underline" @click="updateCustomer(customer)">SAVE</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-if="customers.length === 0" class="p-8 text-center text-sm text-[#82796f]">No customer accounts yet.</p>
            </div>
        </section>
    </section>
</template>
