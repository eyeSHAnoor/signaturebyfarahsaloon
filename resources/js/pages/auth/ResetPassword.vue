<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email?: string | null;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email ?? '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset password" />
    <AuthLayout title="Reset password" description="Choose a new password for your account.">
        <form class="space-y-5" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium">Email</label>
                <input id="email" v-model="form.email" type="email" autocomplete="email" required
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium">New password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="new-password" required
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-medium">Confirm password</label>
                <input id="password_confirmation" v-model="form.password_confirmation" type="password"
                    autocomplete="new-password" required
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            </div>
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground disabled:opacity-50">
                Reset password
            </button>
        </form>
    </AuthLayout>
</template>
