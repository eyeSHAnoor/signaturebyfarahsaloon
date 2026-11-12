<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<SharedData>();

const navItems = computed(() => [
    {
        label: 'Main',
        items: [
            {
                visible: true,
                title: 'Dashboard',
                href: '/dashboard',
                icon: 'material-symbols:dashboard-outline',
                isActive: page.url === '/dashboard' || route().current('home') || route().current('dashboard'),
            },
            {
                visible: true,
                title: 'Account settings',
                href: route('account.edit'),
                icon: 'material-symbols:settings-outline',
                isActive: route().current('account.*') || route().current('profile.*'),
            },
        ],
    },
]);
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <template v-for="(group, gIndex) in navItems" :key="gIndex">
            <SidebarGroupLabel>{{ group.label }}</SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem v-for="(item, index) in group.items" :key="index">
                    <SidebarMenuButton v-if="item.visible" :is-active="item.isActive" :tooltip="item.title"
                        :class="[item.isActive && '!bg-brand-blue font-semibold !text-white']" as-child>
                        <Link :href="item.href" class="flex items-center h-10 leading-none gap-2">
                            <Icon :icon="item.icon" class="!size-6.5 text-brand-blue"
                                :class="[item.isActive && 'text-white  font-bold']" />
                            <span class="text-sm leading-none text-gray-600" :class="[item.isActive && 'text-white']">{{
                                item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </template>
    </SidebarGroup>
</template>
