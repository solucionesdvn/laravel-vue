<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Eye } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import type { SubmittedDocument, Paginated, DocumentTemplate } from '@/types';
import { format } from 'date-fns';

defineProps<{
    submittedDocuments: Paginated<SubmittedDocument>;
    documentTemplates: DocumentTemplate[]; // Add documentTemplates prop
}>();

const formatDate = (dateString: string) => {
    return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};
</script>

<template>
    <AppSidebarLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                    <TableCell>{{ doc.document_template?.name }}</TableCell>
                                    <TableCell>{{ doc.submitted_by_user?.name }}</TableCell>
                                    <TableCell>{{ formatDate(doc.created_at) }}</TableCell>
                                    <TableCell>
                                        <Button as-child variant="outline" size="sm">
                                            <Link :href="route('submitted-documents.show', doc.id)">
                                                <Eye class="mr-2 h-4 w-4" />
                                                Ver
                                            </Link>
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
                        <div v-if="documentTemplates.length === 0" class="text-center text-gray-500">
                            No hay plantillas de documentos disponibles.
                        </div>
                        <div v-else class="grid grid-cols-1 gap-4">
                            <Card v-for="template in documentTemplates" :key="template.id" class="bg-blue-100 dark:bg-blue-900 border-blue-300 dark:border-blue-700">
                                <CardHeader>
                                    <CardTitle class="text-blue-800 dark:text-blue-200">{{ template.name }}</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-blue-700 dark:text-blue-300">{{ template.description || 'Sin descripción' }}</p>
                                    <div class="mt-4 flex justify-end">
                                        <Button as-child variant="outline" size="sm">
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
