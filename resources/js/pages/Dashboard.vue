<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { DollarSign, Users, Package, ShoppingCart, Wallet } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
];

const props = defineProps<{
  stats: {
    totalClients: number;
    totalProducts: number;
    totalEmployees: number;
    totalSalesAmount: number;
  };
  recentSales: Array<{
    id: number;
    total: number;
    date: string;
    client?: { name: string };
  }>;
  openCashRegisters: Array<{
    id: number;
    opened_at: string;
    opening_amount: number;
    user?: { name: string };
  }>;
}>();

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
};

const formatDate = (dateString: string) => {
  return format(new Date(dateString), 'dd/MM/yyyy HH:mm', { locale: es });
};
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 space-y-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-6">Resumen del Negocio</h1>

      <!-- Tarjetas de Estadísticas -->
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Ventas Totales</CardTitle>
            <DollarSign class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ formatCurrency(props.stats.totalSalesAmount) }}</div>
            <p class="text-xs text-muted-foreground">Monto total de ventas registradas</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Total Clientes</CardTitle>
            <Users class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ props.stats.totalClients }}</div>
            <p class="text-xs text-muted-foreground">Clientes registrados en el sistema</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Total Productos</CardTitle>
            <Package class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ props.stats.totalProducts }}</div>
            <p class="text-xs text-muted-foreground">Productos disponibles en inventario</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Total Empleados</CardTitle>
            <ShoppingCart class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ props.stats.totalEmployees }}</div>
            <p class="text-xs text-muted-foreground">Empleados registrados en el sistema</p>
          </CardContent>
        </Card>
      </div>

      <!-- Ventas Recientes y Cajas Abiertas -->
      <div class="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Ventas Recientes</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID Venta</TableHead>
                  <TableHead>Cliente</TableHead>
                  <TableHead>Total</TableHead>
                  <TableHead>Fecha</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="props.recentSales.length === 0">
                  <TableCell colspan="4" class="text-center text-muted-foreground">No hay ventas recientes.</TableCell>
                </TableRow>
                <TableRow v-for="sale in props.recentSales" :key="sale.id">
                  <TableCell>{{ sale.id }}</TableCell>
                  <TableCell>{{ sale.client?.name || 'N/A' }}</TableCell>
                  <TableCell>{{ formatCurrency(sale.total) }}</TableCell>
                  <TableCell>{{ formatDate(sale.date) }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Cajas Abiertas</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID Caja</TableHead>
                  <TableHead>Abierta por</TableHead>
                  <TableHead>Monto Inicial</TableHead>
                  <TableHead>Hora Apertura</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="props.openCashRegisters.length === 0">
                  <TableCell colspan="4" class="text-center text-muted-foreground">No hay cajas abiertas.</TableCell>
                </TableRow>
                <TableRow v-for="register in props.openCashRegisters" :key="register.id">
                  <TableCell>{{ register.id }}</TableCell>
                  <TableCell>{{ register.user?.name || 'N/A' }}</TableCell>
                  <TableCell>{{ formatCurrency(register.opening_amount) }}</TableCell>
                  <TableCell>{{ formatDate(register.opened_at) }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>