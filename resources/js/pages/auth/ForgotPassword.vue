<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => form.post(route('password.email'));
</script>

<template>
    <Head title="Forgot password" />
    <AuthLayout title="Forgot password" description="We will email you a password reset link.">
        <form class="space-y-5" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium">Email</label>
                <input id="email" v-model="form.email" type="email" autocomplete="email" required
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>
            <p v-if="status" class="text-sm text-green-600">{{ status }}</p>
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground disabled:opacity-50">
                Email reset link
            </button>
            <p class="text-center text-sm"><Link :href="route('login')" class="underline">Back to login</Link></p>
        </form>
    </AuthLayout>
</template>
