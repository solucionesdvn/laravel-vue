<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { Toaster, toast } from 'vue-sonner';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();

watchEffect(() => {
    const successMessage = (page.props.flash as any)?.success;
    const errorMessage = (page.props.errors as any)?.error || (page.props.flash as any)?.error;

    if (successMessage) {
        toast.success('Éxito', {
            description: successMessage,
        });
    }

    if (errorMessage) {
        toast.error('Error', {
            description: errorMessage,
        });
    }
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
            <Toaster rich-colors />
        </AppContent>
    </AppShell>
</template>
