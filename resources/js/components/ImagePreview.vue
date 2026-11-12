<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        label: string;
        src?: string | null;
        file?: File | null;
    }>(),
    {
        src: null,
        file: null,
    },
);

const localUrl = ref<string | null>(null);
const dimensions = ref<{ width: number; height: number } | null>(null);
const previewUrl = computed(() => localUrl.value || props.src || '');

watch(
    () => props.file,
    (file) => {
        if (localUrl.value) URL.revokeObjectURL(localUrl.value);
        localUrl.value = file ? URL.createObjectURL(file) : null;
        dimensions.value = null;
    },
    { immediate: true },
);

watch(
    () => props.src,
    () => {
        dimensions.value = null;
    },
);

onBeforeUnmount(() => {
    if (localUrl.value) URL.revokeObjectURL(localUrl.value);
});

function formatFileSize(size: number): string {
    return size < 1024 * 1024 ? `${(size / 1024).toFixed(1)} KB` : `${(size / (1024 * 1024)).toFixed(1)} MB`;
}

function imageLoaded(event: Event) {
    const image = event.target as HTMLImageElement;
    dimensions.value = { width: image.naturalWidth, height: image.naturalHeight };
}
</script>

<template>
    <figure v-if="previewUrl" class="grid grid-cols-[5rem_minmax(0,1fr)] items-start gap-3 border border-[#e4dbcf] bg-[#fbf8f3] p-3">
        <img :src="previewUrl" :alt="label" class="h-20 w-20 object-cover" @load="imageLoaded" />
        <figcaption class="min-w-0 text-xs text-[#655c52]">
            <p class="font-medium">{{ label }}</p>
            <p v-if="file" class="mt-1 break-all">{{ file.name }} · {{ formatFileSize(file.size) }}</p>
            <a v-else :href="src || undefined" target="_blank" rel="noreferrer" class="mt-1 block break-all underline"> View current image </a>
            <p v-if="dimensions" class="mt-1 text-[#82796f]">{{ dimensions.width }} × {{ dimensions.height }} px</p>
        </figcaption>
    </figure>
</template>
