<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post('/confirm-password', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Confirm password" />
    <AuthLayout title="Confirm your password" description="Please confirm your password to continue.">
        <form class="space-y-5" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium">Password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="current-password" required
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground disabled:opacity-50">
                Confirm password
            </button>
        </form>
    </AuthLayout>
</template>
