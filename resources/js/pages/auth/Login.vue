<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />
    <AuthLayout title="Log in" description="Enter your account details to continue.">
        <form class="space-y-5" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium">Email</label>
                <input id="email" v-model="form.email" type="email" autocomplete="username" required
                    class="w-full rounded-md border border-[#ded5c9] bg-white px-3 py-3 text-sm focus:border-[#a98150] focus:outline-none">
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium">Password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="current-password" required
                    class="w-full rounded-md border border-[#ded5c9] bg-white px-3 py-3 text-sm focus:border-[#a98150] focus:outline-none">
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.remember" type="checkbox" class="rounded border-input">
                Remember me
            </label>
            <button type="submit" :disabled="form.processing"
                class="w-full bg-[#7651a5] px-4 py-3 text-[10px] tracking-[0.16em] text-white transition hover:bg-[#62418e] disabled:opacity-50">
                Log in
            </button>
            <div class="flex justify-between text-sm">
                <Link :href="route('password.request')" class="underline">Forgot password?</Link>
                <Link :href="route('register')" class="underline">Create an account</Link>
            </div>
        </form>
    </AuthLayout>
</template>
