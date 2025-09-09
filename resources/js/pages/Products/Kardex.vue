<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type Product, type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { format } from 'date-fns';

const props = defineProps<{
  product: Product;
  kardex: Array<{
    date: string;
    type: string;
    details: string;
    quantity_in: number;
    quantity_out: number;
    balance: number;
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Productos', href: route('products.index') },
    { title: props.product.name, href: null },
    { title: 'Kardex', href: route('products.kardex', { product: props.product.id }) },
];

const formatDate = (dateString: string) => {
    return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};
</script>

<template>
    <Head :title="`Kardex - ${product.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Kardex de Inventario: {{ product.name }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Fecha</TableHead>
                                <TableHead>Tipo</TableHead>
                                <TableHead>Detalles</TableHead>
                                <TableHead class="text-center">Entrada</TableHead>
                                <TableHead class="text-center">Salida</TableHead>
                                <TableHead class="text-center">Saldo</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="kardex.length === 0">
                                <TableCell :colspan="6" class="text-center">
                                    No hay movimientos para este producto.
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="(item, index) in kardex" :key="index">
                                <TableCell>{{ formatDate(item.date) }}</TableCell>
                                <TableCell>
                                    <Link
                                        :href="item.url"
                                        class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center justify-center w-28"
                                        :class="{
                                            'bg-green-100 text-green-800 hover:bg-green-200': item.type === 'Entrada',
                                            'bg-red-100 text-red-800 hover:bg-red-200': item.type === 'Venta',
                                            'bg-yellow-100 text-yellow-800 hover:bg-yellow-200': item.type === 'Ajuste Salida',
                                        }"
                                    >
                                        {{ item.type }}
                                    </Link>
                                </TableCell>
                                <TableCell>{{ item.details }}</TableCell>
                                <TableCell class="text-center font-mono text-green-600">
                                    {{ item.quantity_in > 0 ? `+${item.quantity_in}` : '' }}
                                </TableCell>
                                <TableCell class="text-center font-mono text-red-600">
                                    {{ item.quantity_out > 0 ? `-${item.quantity_out}` : '' }}
                                </TableCell>
                                <TableCell class="text-center font-bold font-mono">
                                    {{ item.balance }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>