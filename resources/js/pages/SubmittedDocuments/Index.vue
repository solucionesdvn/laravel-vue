'''<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Eye, Search, Pencil, FileText } from 'lucide-vue-next';
import { Link, Head } from '@inertiajs/vue3';
import type { SubmittedDocument, Paginated, DocumentTemplate } from '@/types';
import { format } from 'date-fns';
import type { BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';
import { Input } from '@/components/ui/input';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Documentos Enviados', href: route('submitted-documents.index') }
];

const props = defineProps<{
    submittedDocuments: Paginated<SubmittedDocument>;
    documentTemplates: DocumentTemplate[];
}>();

const formatDate = (dateString: string) => {
    return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};

// Lógica para el buscador de plantillas
const templateSearch = ref('');
const filteredTemplates = computed(() => {
    if (!templateSearch.value) {
        return props.documentTemplates;
    }
    return props.documentTemplates.filter(template =>
        template.name.toLowerCase().includes(templateSearch.value.toLowerCase())
    );
});
</script>

'''<template>
    <Head title="Documentos Enviados" />

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Documentos Cliente</h1>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Existing Card for Submitted Documents -->
                <Card>
                    <CardHeader>
                        <CardTitle>Listado de Documentos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Plantilla</TableHead>
                                    <TableHead>Enviado por</TableHead>
                                    <TableHead>Fecha de Envío</TableHead>
                                    <TableHead>Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="submittedDocuments.data.length === 0">
                                    <TableCell colspan="4" class="text-center">
                                        No hay documentos enviados todavía.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="doc in submittedDocuments.data" :key="doc.id">
                                    <TableCell>
                                        <span class="font-medium">{{ doc.document_template?.name || 'Plantilla eliminada' }}</span>
                                    </TableCell>
                                    <TableCell>
                                        <span class="text-muted-foreground">{{ doc.submitted_by_user?.name || 'Usuario no disponible' }}</span>
                                    </TableCell>
                                    <TableCell>{{ formatDate(doc.created_at) }}</TableCell>
                                    <TableCell>
                                        <Button as-child size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700">
                                            <Link :href="route('submitted-documents.show', doc.id)">
                                                <Eye class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button as-child size="sm" class="bg-blue-500 text-white hover:bg-blue-700">
                                            <Link :href="route('submitted-documents.edit', doc.id)">
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button as-child size="sm" class="bg-red-600 text-white hover:bg-red-700">
                                            <a :href="route('submitted-documents.export.pdf', doc.id)" target="_blank">
                                                <FileText class="h-4 w-4" />
                                            </a>
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- New Card for Document Templates -->
                <Card>
                    <CardHeader>
                        <CardTitle>Plantillas de Documentos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <!-- Buscador de plantillas -->
                        <div class="relative mb-4">
                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                type="search"
                                v-model="templateSearch"
                                placeholder="Buscar plantilla..."
                                class="w-full pl-8"
                            />
                        </div>

                        <div v-if="filteredTemplates.length === 0" class="text-center text-gray-500 py-6">
                            No se encontraron plantillas.
                        </div>
                        
                        <div v-else class="grid grid-cols-1 gap-4 max-h-96 overflow-y-auto pr-2">
                            <Card v-for="template in filteredTemplates" :key="template.id" class="bg-green-50 dark:bg-green-900/50 border-green-200 dark:border-green-800 hover:border-green-400 dark:hover:border-green-600 transition-colors">
                                <CardHeader class="pb-2">
                                    <CardTitle class="text-green-800 dark:text-green-200 text-base">{{ template.name }}</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div class="flex justify-end">
                                        <Button as-child variant="outline" size="sm" class="border-green-300 dark:border-green-700 hover:bg-green-100 dark:hover:bg-green-800/60">
                                            <Link :href="route('submitted-documents.create', template.id)">
                                                Crear Documento
                                            </Link>
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppSidebarLayout>
</template>
'''
''
