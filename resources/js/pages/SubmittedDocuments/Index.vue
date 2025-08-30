<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Eye } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import type { SubmittedDocument, Paginated } from '@/types';
import { format } from 'date-fns';

defineProps<{
    submittedDocuments: Paginated<SubmittedDocument>;
}>();

const formatDate = (dateString: string) => {
    return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};
</script>

<template>
    <AppSidebarLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                                        
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppSidebarLayout>
</template>
