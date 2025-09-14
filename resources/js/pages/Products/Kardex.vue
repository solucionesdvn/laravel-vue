<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type Product, type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { format } from 'date-fns';

import { Link, router } from '@inertiajs/vue3';

const props = defineProps<{
  product: Product;
  entryKardex: {
    data: Array<{
        date: string;
        type: string;
        details: string;
        quantity_in: number;
        quantity_out: number;
        url: string; // Added url
    }>;
    links: Array<any>;
  };
  saleKardex: {
    data: Array<{
        date: string;
        type: string;
        details: string;
        quantity_in: number;
        quantity_out: number;
        url: string; // Added url
    }>;
    links: Array<any>;
  };
  exitKardex: {
    data: Array<{
        date: string;
        type: string;
        details: string;
        quantity_in: number;
        quantity_out: number;
        url: string; // Added url
    }>;
    links: Array<any>;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Productos', href: route('products.index') },
    { title: props.product.name, href: null },
    { title: 'Kardex', href: route('products.kardex', { product: props.product.id }) },
];

const formatDate = (dateString: string) => {
    if (!dateString) {
        return 'N/A'; // Or some other placeholder
    }
    return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};
</script>

<template>
    <Head :title="`Kardex - ${product.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 space-y-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Kardex de Inventario: {{ product.name }}</h2>

            <!-- Entradas -->
            <Card>
                <CardHeader>
                    <CardTitle>Movimientos de Entrada</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Fecha</TableHead>
                                <TableHead>Detalles</TableHead>
                                <TableHead class="text-center">Cantidad</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="entryKardex.data.length === 0">
                                <TableCell :colspan="3" class="text-center">
                                    No hay movimientos de entrada para este producto.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="(item, index) in entryKardex.data" :key="`entry-${index}`">
                                <TableCell>{{ formatDate(item.date) }}</TableCell>
                                <TableCell>
                                    <Link :href="item.url" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                        {{ item.details }}
                                    </Link>
                                </TableCell>
                                <TableCell class="text-center font-mono text-green-600">
                                    +{{ item.quantity_in }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Paginación Entradas -->
                    <div class="mt-4 flex justify-center space-x-2">
                        <template v-for="(link, index) in entryKardex.links" :key="index">
                            <button
                                v-if="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1 rounded text-sm',
                                    link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                                @click="router.visit(link.url, { preserveScroll: true })"
                            ></button>
                        </template>
                    </div>
                </CardContent>
            </Card>

            <!-- Ventas -->
            <Card>
                <CardHeader>
                    <CardTitle>Movimientos de Venta</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Fecha</TableHead>
                                <TableHead>Detalles</TableHead>
                                <TableHead class="text-center">Cantidad</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="saleKardex.data.length === 0">
                                <TableCell :colspan="3" class="text-center">
                                    No hay movimientos de venta para este producto.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="(item, index) in saleKardex.data" :key="`sale-${index}`">
                                <TableCell>{{ formatDate(item.date) }}</TableCell>
                                <TableCell>
                                    <Link :href="item.url" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                        {{ item.details }}
                                    </Link>
                                </TableCell>
                                <TableCell class="text-center font-mono text-red-600">
                                    -{{ item.quantity_out }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Paginación Ventas -->
                    <div class="mt-4 flex justify-center space-x-2">
                        <template v-for="(link, index) in saleKardex.links" :key="index">
                            <button
                                v-if="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1 rounded text-sm',
                                    link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                                @click="router.visit(link.url, { preserveScroll: true })"
                            ></button>
                        </template>
                    </div>
                </CardContent>
            </Card>

            <!-- Salidas (Ajustes) -->
            <Card>
                <CardHeader>
                    <CardTitle>Movimientos de Salida (Ajustes)</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Fecha</TableHead>
                                <TableHead>Detalles</TableHead>
                                <TableHead class="text-center">Cantidad</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="exitKardex.data.length === 0">
                                <TableCell :colspan="3" class="text-center">
                                    No hay movimientos de salida para este producto.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="(item, index) in exitKardex.data" :key="`exit-${index}`">
                                <TableCell>{{ formatDate(item.date) }}</TableCell>
                                <TableCell>
                                    <Link :href="item.url" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                        {{ item.details }}
                                    </Link>
                                </TableCell>
                                <TableCell class="text-center font-mono text-red-600">
                                    -{{ item.quantity_out }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Paginación Salidas -->
                    <div class="mt-4 flex justify-center space-x-2">
                        <template v-for="(link, index) in exitKardex.links" :key="index">
                            <button
                                v-if="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1 rounded text-sm',
                                    link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                                @click="router.visit(link.url, { preserveScroll: true })"
                            ></button>
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
