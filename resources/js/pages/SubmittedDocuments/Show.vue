<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import type { SubmittedDocument } from '@/types';
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Printer, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    submittedDocument: Object as () => SubmittedDocument
});

// Genera el contenido final del documento reemplazando las variables
const renderedContent = computed(() => {
    let content = props.submittedDocument.document_template?.content || '';
    const data = props.submittedDocument.data || {};

    for (const key in data) {
        const placeholder = `{{${key}}}`;
        const value = data[key];
        
        if (typeof value === 'object' && value !== null) {
            for (const subKey in value) {
                const subPlaceholder = `{{${key}.${subKey}}}`;
                content = content.replace(new RegExp(subPlaceholder.replace(/([{}])/g, '\\$1'), 'g'), value[subKey] || subPlaceholder);
            }
        } else {
            content = content.replace(new RegExp(placeholder.replace(/([{}])/g, '\\$1'), 'g'), value || placeholder);
        }
    }
    return content;
});

const printDocument = () => {
    window.print();
};

</script>

<template>
    <AppSidebarLayout>
        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
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

                <Card class="shadow-lg">
                    <CardHeader>
                        <CardTitle>Vista del Documento</CardTitle>
                        <CardDescription>
                            Plantilla usada: <strong>{{ submittedDocument.document_template?.name }}</strong>
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="prose dark:prose-invert max-w-none p-4 border rounded-md bg-white dark:bg-gray-100 text-black" v-html="renderedContent"></div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppSidebarLayout>

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
            }
        }
    </style>
</template>
