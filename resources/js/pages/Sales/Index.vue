<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { can } from '@/lib/can';

import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { CirclePlus, Eye, Trash2 } from 'lucide-vue-next';
import type { Sale } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Ventas', href: '/sales' }
];

defineProps<{
    sales: {
        data: Sale[]
        links: {
            url: string | null
            label: string
            active: boolean
        }[]
    }
}>()

const annulSale = (saleId: number) => {
    if (confirm('¿Estás seguro de que quieres anular esta venta? Esta acción no se puede deshacer.')) {
        router.delete(route('sales.destroy', saleId), {
            preserveScroll: true,
        });
    }
}

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
  <Head title="Ventas" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Título y botón -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Lista de Ventas</h1>
        <div>
          <Button
            as-child
            size="sm"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('sales.create')"
          >
            <Link :href="route('sales.create')">
              <CirclePlus class="mr-1" /> Nueva Venta
            </Link>
          </Button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption>Tabla de ventas</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">ID</TableHead>
              <TableHead class="px-6 py-3">Cliente</TableHead>
              <TableHead class="px-6 py-3">Vendedor</TableHead>
              <TableHead class="px-6 py-3 text-right">Total</TableHead>
              <TableHead class="px-6 py-3">Fecha</TableHead>
              <TableHead class="px-6 py-3">Estado</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-if="sales.data.length === 0">
                <TableCell colspan="7" class="text-center py-6 text-gray-500">
                    No hay ventas registradas.
                </TableCell>
            </TableRow>
            <TableRow
                v-for="sale in sales.data"
                :key="sale.id"
                :class="{ 'bg-red-50 dark:bg-red-900/20 opacity-60': sale.deleted_at }"
            >
              <TableCell class="px-6 py-4">{{ sale.id }}</TableCell>
              <TableCell class="px-6 py-4">{{ sale.client?.name || 'N/A' }}</TableCell>
              <TableCell class="px-6 py-4">{{ sale.user?.name || 'N/A' }}</TableCell>
              <TableCell class="px-6 py-4 text-right">{{ formatCurrency(sale.total) }}</TableCell>
              <TableCell class="px-6 py-4">{{ formatDate(sale.created_at) }}</TableCell>
              <TableCell class="px-6 py-4">
                <span
                    v-if="sale.deleted_at"
                    class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-200"
                >
                    Anulada
                </span>
                <span
                    v-else
                    class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200"
                >
                    Completada
                </span>
              </TableCell>
              <TableCell class="px-6 py-4 flex gap-2">
                <Button
                  as-child
                  size="sm"
                  class="bg-yellow-500 text-white hover:bg-yellow-700"
                  v-if="can('sales.view')"
                >
                    <Link :href="route('sales.show', sale.id)">
                        <Eye />
                    </Link>
                </Button>
                <Button
                  v-if="!sale.deleted_at && can('sales.delete')"
                  @click="annulSale(sale.id)"
                  size="sm"
                  variant="destructive"
                  class="bg-red-600 text-white hover:bg-red-800"
                >
                    <Trash2 />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2 p-4">
          <template v-for="(link, index) in sales.links" :key="index">
            <button
              v-if="link.url"
              :class="[ 'px-3 py-1 rounded text-sm',
                link.active
                  ? 'bg-indigo-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              ]"
              @click="router.visit(link.url, { preserveScroll: true })"
              v-html="link.label"
            ></button>
             <span
                v-else
                class="px-3 py-1 rounded text-sm text-gray-400 cursor-not-allowed"
                v-html="link.label"
            />
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
