
<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const showToast = ref(false);
const toastMessage = ref("");
const page = usePage();

function checkFlash() {
    if (page.props.flash && page.props.flash.success) {
        toastMessage.value = page.props.flash.success;
        showToast.value = true;
        setTimeout(() => {
            showToast.value = false;
        }, 3000);
    }
}

onMounted(checkFlash);
watch(() => page.props.flash.success, checkFlash);
</script>

<template>
    <!-- Toast global -->
    <div v-if="showToast" class="fixed top-5 right-5 z-50 bg-green-600 text-white px-4 py-2 rounded shadow-lg transition-all">
        {{ toastMessage }}
    </div>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
