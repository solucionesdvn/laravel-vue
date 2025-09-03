<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import type { DocumentTemplate, BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    template: Object as () => DocumentTemplate
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Plantillas', href: route('document-templates.index') },
  { title: 'Ver Plantilla', href: null },
];

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-start items-center">
                    <Button as-child variant="outline">
                        <Link :href="route('document-templates.index')">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver al Listado
                        </Link>
                    </Button>
                </div>

                <div class="bg-gray-200 dark:bg-gray-900 p-8 flex justify-center">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-md w-[210mm] min-h-[297mm] p-12">
                        <div class="prose dark:prose-invert max-w-none" v-html="template.content"></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
