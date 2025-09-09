<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';
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

import { CirclePlus, Eye, Trash2, BarChart } from 'lucide-vue-next';
import type { Sale } from '@/types'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import axios from 'axios';

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

const isReportModalOpen = ref(false);
const startDate = ref(new Date().toISOString().split('T')[0]); // YYYY-MM-DD
const endDate = ref(new Date().toISOString().split('T')[0]); // YYYY-MM-DD
const productsSoldReport = ref<any[]>([]);
const isLoadingReport = ref(false);

async function generateReport() {
    isLoadingReport.value = true;
    productsSoldReport.value = [];
    try {
        const response = await axios.get(route('sales.report.products-sold'), {
            params: {
                start_date: startDate.value,
                end_date: endDate.value,
            },
        });
        productsSoldReport.value = response.data;
    } catch (error) {
        console.error('Error generating report:', error);
        // Handle error, e.g., show a toast
    } finally {
        isLoadingReport.value = false;
    }
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
          <Button
              size="sm"
              class="bg-green-600 text-white hover:bg-green-700"
              @click="isReportModalOpen = true"
          >
              Reporte de Productos
          </Button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption>Tabla de ventas</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              
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
                <TableCell colspan="6" class="text-center py-6 text-gray-500">
                    No hay ventas registradas.
                </TableCell>
            </TableRow>
            <TableRow
                v-for="sale in sales.data"
                :key="sale.id"
                :class="{ 'bg-red-50 dark:bg-red-900/20 opacity-60': sale.deleted_at }"
            >
              
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

    <!-- Reporte de Productos Modal -->
    <Dialog :open="isReportModalOpen" @update:open="isReportModalOpen = $event">
        <DialogContent class="sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle>Reporte de Productos Vendidos</DialogTitle>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="flex items-center gap-4">
                    <div>
                        <Label for="start_date">Fecha Inicio</Label>
                        <Input id="start_date" type="date" v-model="startDate" />
                    </div>
                    <div>
                        <Label for="end_date">Fecha Fin</Label>
                        <Input id="end_date" type="date" v-model="endDate" />
                    </div>
                    <Button @click="generateReport" :disabled="isLoadingReport">
                        <span v-if="isLoadingReport">Generando...</span>
                        <span v-else>Generar Reporte</span>
                    </Button>
                </div>

                <div v-if="isLoadingReport" class="text-center py-4">
                    Cargando reporte...
                </div>
                <div v-else-if="productsSoldReport.length === 0" class="text-center py-4">
                    No hay productos vendidos en el rango de fechas seleccionado.
                </div>
                <div v-else class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Producto</TableHead>
                                <TableHead>SKU</TableHead>
                                <TableHead class="text-right">Cantidad Total Vendida</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="product in productsSoldReport" :key="product.product_id">
                                <TableCell>{{ product.product.name }}</TableCell>
                                <TableCell>{{ product.product.sku }}</TableCell>
                                <TableCell class="text-right font-bold">{{ product.total_quantity }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
            <DialogFooter>
                <DialogClose as-child>
                    <Button type="button" variant="secondary">Cerrar</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>

  </AppLayout>
</template>
