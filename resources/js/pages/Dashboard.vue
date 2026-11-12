<script setup lang="ts">
import Layout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
// import { useI18n } from 'vue-i18n';
defineOptions({ layout: Layout });


const { resetList } = useBreadcrumb()
onMounted(() => {
    resetList();
});

// const { t } = useI18n();

</script>

<template>

    <Head title="Dashboard" />

    <div>WELCOME</div>
</template>
