'''<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Eye, Search, Pencil, FileText, Link2, Printer, X } from 'lucide-vue-next';
import { Link, Head } from '@inertiajs/vue3';
import type { SubmittedDocument, Paginated, DocumentTemplate } from '@/types';
import { format } from 'date-fns';
import type { BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';
import { Input } from '@/components/ui/input';
import QrcodeVue from 'qrcode.vue';

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

// Lógica para compartir
const activeShareMenu = ref<number | null>(null);
const origin = typeof window !== 'undefined' ? window.location.origin : '';

function copyPublicLink(token: string) {
  const url = route('public.submitted-documents.show', token);
  navigator.clipboard.writeText(url);
  alert('Enlace copiado al portapapeles!');
}

function toggleShareMenu(docId: number) {
    if (activeShareMenu.value === docId) {
        activeShareMenu.value = null;
    } else {
        activeShareMenu.value = docId;
    }
}

// Lógica para el modal de vista previa de PDF
const showPdfModal = ref(false);
const pdfUrl = ref('');

function openPdfPreview(token: string) {
  pdfUrl.value = route('public.submitted-documents.pdf', token);
  showPdfModal.value = true;
}
</script>

'''<template>
    <Head title="Documentos Enviados" />

    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Documentos Cliente</h1>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
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
                                <template v-for="doc in submittedDocuments.data" :key="doc.id">
                                    <TableRow>
                                        <TableCell>
                                            <span class="font-medium">{{ doc.document_template?.name || 'Plantilla eliminada' }}</span>
                                        </TableCell>
                                        <TableCell>
                                            <span class="text-muted-foreground">{{ doc.submitted_by_user?.name || 'Usuario no disponible' }}</span>
                                        </TableCell>
                                        <TableCell>{{ formatDate(doc.created_at) }}</TableCell>
                                        <TableCell class="px-6 py-4 flex flex-col gap-2">
                                            <div class="flex flex-wrap gap-2">
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
                                                <Button size="sm" class="bg-gray-500 text-white hover:bg-gray-700" @click="toggleShareMenu(doc.id)">
                                                    <Link2 class="h-4 w-4" />
                                                </Button>
                                            </div>

                                            <!-- Share Menu -->
                                            <div v-if="activeShareMenu === doc.id" class="mt-2 p-4 border bg-gray-50 dark:bg-gray-800 rounded-md">
                                                <div v-if="doc.token">
                                                    <QrcodeVue
                                                        :value="route('public.submitted-documents.show', doc.token)"
                                                        :size="128"
                                                        level="M"
                                                        render-as="svg"
                                                        class="mx-auto"
                                                    />
                                                    <div class="mt-4 flex gap-2 flex-wrap justify-center">
                                                        <Button size="sm" @click="copyPublicLink(doc.token)">Copiar enlace</Button>
                                                        <Button as="a" size="sm" :href="`https://wa.me/?text=${encodeURIComponent(route('public.submitted-documents.show', doc.token))}`" target="_blank">WhatsApp</Button>
                                                        <Button size="sm" @click="openPdfPreview(doc.token)">Vista previa</Button>
                                                        <Button as="a" size="sm" :href="route('public.submitted-documents.pdf', doc.token)" target="_blank">Descargar PDF</Button>
                                                    </div>
                                                </div>
                                                <div v-else class="text-center">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Este enlace ya fue utilizado. Puedes generar uno nuevo.</p>
                                                    <Button as-child size="sm">
                                                        <Link :href="route('submitted-documents.regenerate-token', doc.id)" method="post" as="button" preserve-scroll>
                                                            Generar nuevo enlace
                                                        </Link>
                                                    </Button>
                                                </div>
                                                <div class="mt-4 flex justify-end">
                                                     <Button size="sm" variant="ghost" @click="activeShareMenu = null"><X class="h-4 w-4 mr-1" /> Cerrar</Button>
                                                </div>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </template>
                                <TableRow v-if="submittedDocuments.data.length === 0">
                                    <TableCell colspan="4" class="text-center">
                                        No hay documentos enviados todavía.
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

    <!-- Modal Vista Previa PDF -->
    <div v-if="showPdfModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl w-full max-w-4xl h-[90vh] flex flex-col">
            <div class="p-4 flex justify-between items-center border-b">
                <h2 class="text-lg font-semibold">Vista previa del Documento</h2>
                <Button size="sm" variant="destructive" @click="showPdfModal = false">Cerrar</Button>
            </div>
            <iframe :src="pdfUrl" class="flex-1 w-full" />
        </div>
    </div>
</template>
'''
''
