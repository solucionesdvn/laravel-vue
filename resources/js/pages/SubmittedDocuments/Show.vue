<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import type { SubmittedDocument, BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import { Printer, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    submittedDocument: Object as () => SubmittedDocument
});

const template = props.submittedDocument.document_template;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Documentos Enviados', href: route('submitted-documents.index') },
    { title: `Ver: ${template?.name || 'Documento'}` }
];

// Helpers para el renderizado seguro
function escapeRegex(str: string) {
  return str.replace(/[.*+?^${}()|[\\]/g, '\\$&');
}
function escapeHtml(str: string) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

// Lógica de renderizado mejorada
const renderedContent = computed(() => {
    let content = template?.content || '';
    const data = props.submittedDocument.data || {};

    // Primero, reemplazamos los placeholders con los datos existentes
    for (const key in data) {
        const re = new RegExp(String.raw`{{\s*${escapeRegex(key)}\s*}}`, 'g');
        const value = data[key];
        content = content.replace(re, escapeHtml(String(value)));
    }

    // Luego, los placeholders que queden se marcan como no llenados
    content = content.replace(/{{(.*?)}}/g, '<span class="text-red-500 italic">[Campo no llenado: $1]</span>');
    
    return content;
});

const printDocument = () => {
    window.print();
};

</script>

<template>
    <Head :title="`Ver: ${template?.name || 'Documento'}`" />

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center print:hidden">
                    <Button as-child variant="outline">
                        <Link :href="route('submitted-documents.index')">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver al Listado
                        </Link>
                    </Button>
                    <Button @click="printDocument">
                        <Printer class="mr-2 h-4 w-4" />
                        Imprimir
                    </Button>
                </div>

                <Card class="shadow-lg printable-content">
                    <CardHeader class="print:hidden">
                        <CardTitle>Vista del Documento</CardTitle>
                        <CardDescription>
                            Plantilla usada: <strong>{{ template?.name }}</strong>
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="prose dark:prose-invert max-w-none p-4 border rounded-md bg-white dark:bg-gray-100 text-black" v-html="renderedContent"></div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppSidebarLayout>
</template>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .printable-content, .printable-content * {
            visibility: visible;
        }
        .printable-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            border: none;
            box-shadow: none;
        }
        .prose {
            background-color: transparent !important;
        }
    }
</style>
