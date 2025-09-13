
<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
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
const newSaleId = ref<number | null>(null);
const page = usePage();

function closeToast() {
    showToast.value = false;
}

function checkFlash() {
    // Reset before checking
    newSaleId.value = null;

    const flash = page.props.flash as { success?: string; new_sale_id?: number };

    if (flash && flash.success) {
        toastMessage.value = flash.success;

        if (flash.new_sale_id) {
            newSaleId.value = flash.new_sale_id;
        }

        showToast.value = true;

        // Only auto-hide if it's a simple message without a link
        if (!newSaleId.value) {
            setTimeout(() => {
                closeToast();
            }, 4000); // 4 seconds
        }
    }
}

// Watch for changes in the flash prop
watch(() => page.props.flash, (newFlash, oldFlash) => {
    if (newFlash && newFlash !== oldFlash) {
        checkFlash();
    }
}, { deep: true });

// Check on initial load
onMounted(checkFlash);

</script>

<template>
    <!-- Toast global -->
    <div v-if="showToast" class="fixed top-5 right-5 z-50 max-w-sm bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg transition-all animate-fade-in-down">
        <div class="flex items-start justify-between">
            <div class="pr-4">
                <p class="font-bold">¡Éxito!</p>
                <p class="text-sm">{{ toastMessage }}</p>
                <a
                    v-if="newSaleId"
                    :href="route('sales.receipt', newSaleId)"
                    target="_blank"
                    class="inline-block mt-2 underline text-sm font-semibold hover:text-green-200"
                    @click="closeToast"
                >
                    Imprimir Comprobante
                </a>
            </div>
            <button @click="closeToast" class="ml-auto -mt-2 -mr-2 p-1 rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppSidebarLayout>
</template>
