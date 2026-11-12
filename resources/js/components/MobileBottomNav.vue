<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SharedData } from '@/types';

const page = usePage<SharedData>();
const user = computed(() => page.props.auth?.user);
const accountHref = computed(() => user.value ? route('account.edit') : route('login'));
</script>

<template>
    <nav class="fixed bottom-0 left-0 right-0 z-50 border-t bg-background px-4 py-3 md:hidden">
        <div class="mx-auto flex max-w-md items-center justify-around">
            <Link :href="route('dashboard')" class="text-sm font-medium">Dashboard</Link>
            <Link :href="accountHref" class="text-sm font-medium">
                {{ user ? 'Account settings' : 'Log in' }}
            </Link>
        </div>
    </nav>
</template>
