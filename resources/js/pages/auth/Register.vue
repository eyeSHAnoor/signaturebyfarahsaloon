<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const fields = [
    { name: 'name', label: 'Name', type: 'text', autocomplete: 'name' },
    { name: 'email', label: 'Email', type: 'email', autocomplete: 'email' },
    { name: 'phone', label: 'Phone', type: 'tel', autocomplete: 'tel' },
    { name: 'password', label: 'Password', type: 'password', autocomplete: 'new-password' },
    { name: 'password_confirmation', label: 'Confirm password', type: 'password', autocomplete: 'new-password' },
] as const;

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create account" />
    <AuthLayout title="Create account" description="Register to get started.">
        <form class="space-y-5" @submit.prevent="submit">
            <div v-for="field in fields" :key="field.name" class="space-y-2">
                <label :for="field.name" class="text-sm font-medium">{{ field.label }}</label>
                <input :id="field.name" v-model="form[field.name]" :type="field.type" :autocomplete="field.autocomplete"
                    :required="field.name !== 'phone'"
                    class="w-full rounded-md border border-[#ded5c9] bg-white px-3 py-3 text-sm focus:border-[#a98150] focus:outline-none">
                <p v-if="form.errors[field.name]" class="text-sm text-destructive">{{ form.errors[field.name] }}</p>
            </div>
            <button type="submit" :disabled="form.processing"
                class="w-full bg-[#7651a5] px-4 py-3 text-[10px] tracking-[0.16em] text-white transition hover:bg-[#62418e] disabled:opacity-50">
                Create account
            </button>
            <p class="text-center text-sm">
                Already registered? <Link :href="route('login')" class="underline">Log in</Link>
            </p>
        </form>
    </AuthLayout>
</template>
