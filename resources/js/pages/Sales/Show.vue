<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { ArrowLeft } from 'lucide-vue-next';
import type { Sale } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Ventas', href: '/sales' },
  { title: 'Detalle', href: '#' }
];

defineProps<{
    sale: Sale
}>()

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value)
}

const formatDate = (value: string) => {
    return new Date(value).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    })
}
</script>

<template>
  <Head :title="`Venta #${sale.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header y botón de volver -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Detalle de Venta #{{ sale.id }}</h1>
        <div>
          <Button as-child size="sm" variant="outline">
            <Link :href="route('sales.index')">
              <ArrowLeft class="mr-1" /> Volver a Ventas
            </Link>
          </Button>
        </div>
      </div>

      <!-- Detalles de la Venta -->
      <div class="border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-gray-600 dark:text-gray-400">Cliente:</p>
            <p class="font-semibold">{{ sale.client?.name || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Vendedor:</p>
            <p class="font-semibold">{{ sale.user?.name || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Fecha:</p>
            <p class="font-semibold">{{ formatDate(sale.created_at) }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Total:</p>
            <p class="font-semibold text-xl">{{ formatCurrency(sale.total) }}</p>
          </div>
        </div>
      </div>

      <!-- Productos de la Venta -->
      <h2 class="text-2xl font-bold text-gray-800 dark:text-white mt-8">Productos de la Venta</h2>
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Producto</TableHead>
              <TableHead class="px-6 py-3 text-right">Cantidad</TableHead>
              <TableHead class="px-6 py-3 text-right">Precio Unitario</TableHead>
              <TableHead class="px-6 py-3 text-right">Subtotal</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-if="sale.items.length === 0">
                <TableCell colspan="4" class="text-center py-6 text-gray-500">
                    No hay productos en esta venta.
                </TableCell>
            </TableRow>
            <TableRow v-for="item in sale.items" :key="item.id">
              <TableCell class="px-6 py-4">{{ item.product?.name || 'Producto Eliminado' }}</TableCell>
              <TableCell class="px-6 py-4 text-right">{{ item.quantity }}</TableCell>
              <TableCell class="px-6 py-4 text-right">{{ formatCurrency(item.unit_price) }}</TableCell>
              <TableCell class="px-6 py-4 text-right">{{ formatCurrency(item.quantity * item.unit_price) }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
