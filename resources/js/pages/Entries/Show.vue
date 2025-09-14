<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  entry: {
    id: number;
    date: string;
    total_cost: number;
    notes: string | null;
    supplier: { id: number; name: string } | null;
    user: { id: number; name: string };
    items: Array<{
      id: number;
      quantity: number;
      unit_price: number;
      subtotal: number;
      product: { id: number; name: string; sku: string };
    }>;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Entradas', href: route('entries.index') },
  { title: 'Detalle de Entrada', href: null },
];

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
};

</script>

<template>
  <Head title="Detalle de Entrada" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 w-full">
      <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <CardHeader>
          <div class="flex justify-between items-center">
            <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
              Detalles de la Entrada
            </CardTitle>
            <Button as-child variant="outline">
                <Link :href="route('entries.index')">Volver al Listado</Link>
            </Button>
          </div>
        </CardHeader>
        <CardContent class="space-y-6 pt-6">
          <!-- Info General -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Fecha:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ formatDate(entry.date) }}</p>
            </div>
            <div>
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Proveedor:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ entry.supplier?.name || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Registrado por:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ entry.user.name }}</p>
            </div>
            <div class="md:col-span-3">
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Notas:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ entry.notes || 'Sin notas.' }}</p>
            </div>
          </div>

          <!-- Tabla de Items -->
          <div class="border-t dark:border-gray-700 pt-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Productos Incluidos</h3>
            <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                <Table>
                    <TableHeader class="bg-gray-50 dark:bg-gray-800">
                        <TableRow>
                            <TableHead>Producto</TableHead>
                            <TableHead>SKU</TableHead>
                            <TableHead class="text-right">Cantidad</TableHead>
                            <TableHead class="text-right">Precio de Compra</TableHead>
                            <TableHead class="text-right">Subtotal</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <TableRow v-for="item in entry.items" :key="item.id">
                            <TableCell>{{ item.product.name }}</TableCell>
                            <TableCell>{{ item.product.sku }}</TableCell>
                            <TableCell class="text-right">{{ item.quantity }}</TableCell>
                            <TableCell class="text-right">{{ formatCurrency(item.unit_price) }}</TableCell>
                            <TableCell class="text-right">{{ formatCurrency(item.subtotal) }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
          </div>

          <!-- Total -->
          <div class="flex justify-end pt-4 border-t dark:border-gray-700">
            <div class="text-xl font-bold text-gray-800 dark:text-white">
              Costo Total: {{ formatCurrency(entry.total_cost) }}
            </div>
          </div>

        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
