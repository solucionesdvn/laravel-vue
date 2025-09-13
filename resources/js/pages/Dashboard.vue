<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed } from 'vue';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { DollarSign, ShoppingCart, Receipt, TrendingDown } from 'lucide-vue-next';

import PieChart from '@/components/charts/PieChart.vue';
import LineChart from '@/components/charts/LineChart.vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
];

const props = defineProps<{
  stats: {
    salesToday: number;
    expensesToday: number;
    transactionsToday: number;
    averageTicketToday: number;
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
  lowStockProducts: Array<{
    id: number;
    name: string;
    sku: string;
    stock: number;
  }>;
  topSellingProducts: Array<{
    product_id: number;
    total_quantity: string; // Comes as string from DB::raw
    product: {
        id: number;
        name: string;
        sku: string;
    };
  }>;
  salesByCategory: Array<{
      category_name: string;
      total_sales: number;
  }>;
  weeklySales: Array<{
      date: string;
      total_sales: number;
      total_transactions: number;
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

const salesByCategoryChartData = computed(() => {
  const labels = props.salesByCategory.map(item => item.category_name);
  const data = props.salesByCategory.map(item => item.total_sales);

  return {
    labels,
    datasets: [
      {
        backgroundColor: [
          '#41B883', '#E46651', '#00D8FF', '#DD1B16', '#F4D03F',
          '#34495E', '#8E44AD', '#2ECC71', '#E67E22', '#BDC3C7',
          '#FF6384', '#36A2EB', '#FFCE56', '#8AC926', '#1982C4',
          '#6A4C93', '#FFCA3A', '#6A994E', '#A7C957', '#F28482'
        ], // Some default colors
        data,
      },
    ],
  }
});

const weeklySalesChartData = computed(() => {
    const labels = props.weeklySales.map(item => format(new Date(item.date + 'T00:00:00'), 'dd/MM', { locale: es }));
    const data = props.weeklySales.map(item => item.total_sales);
    const transactions = props.weeklySales.map(item => item.total_transactions);

    return {
        labels,
        datasets: [
            {
                label: 'Ventas',
                backgroundColor: '#41B883',
                borderColor: '#41B883',
                data,
                tension: 0.1,
                transactions: transactions, // Custom property
            },
        ],
    }
});

const weeklySalesChartOptions = computed(() => ({
    plugins: {
        tooltip: {
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += formatCurrency(context.parsed.y);
                    }
                    
                    const transactions = context.dataset.transactions[context.dataIndex];
                    label += ` (${transactions} transacciones)`;
                    
                    return label;
                }
            }
        }
    }
}));
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 space-y-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-6">Resumen del Día</h1>

      <!-- Tarjetas de Estadísticas -->
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Ventas del Día</CardTitle>
            <DollarSign class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ formatCurrency(props.stats.salesToday) }}</div>
            <p class="text-xs text-muted-foreground">Total vendido en el día actual</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Gastos del Día</CardTitle>
            <TrendingDown class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ formatCurrency(props.stats.expensesToday) }}</div>
            <p class="text-xs text-muted-foreground">Total de gastos registrados hoy</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Transacciones Hoy</CardTitle>
            <ShoppingCart class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ props.stats.transactionsToday }}</div>
            <p class="text-xs text-muted-foreground">Número de ventas del día</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Ticket Promedio</CardTitle>
            <Receipt class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ formatCurrency(props.stats.averageTicketToday) }}</div>
            <p class="text-xs text-muted-foreground">Valor promedio por transacción</p>
          </CardContent>
        </Card>
      </div>

      <!-- Gráficos -->
      <div class="grid gap-4 md:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Ventas de los Últimos 7 Días</CardTitle>
            </CardHeader>
            <CardContent class="h-[300px] relative">
                <LineChart v-if="weeklySalesChartData.labels.length > 0" :chart-data="weeklySalesChartData" :chart-options="weeklySalesChartOptions" />
                <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                    No hay datos de ventas para la última semana.
                </div>
            </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Ventas por Categoría (Últimos 30 días)</CardTitle>
          </CardHeader>
          <CardContent class="h-[300px] relative">
            <PieChart v-if="salesByCategoryChartData.labels.length > 0" :chart-data="salesByCategoryChartData" />
            <div v-else class="flex items-center justify-center h-full text-muted-foreground">
              No hay datos de ventas por categoría.
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Alertas y Rankings -->
      <div class="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle class="text-red-500">Alerta: Productos con Bajo Stock</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Producto</TableHead>
                  <TableHead>SKU</TableHead>
                  <TableHead class="text-right">Stock</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="props.lowStockProducts.length === 0">
                  <TableCell colspan="3" class="text-center text-muted-foreground">No hay productos con bajo stock.</TableCell>
                </TableRow>
                <TableRow v-for="product in props.lowStockProducts" :key="product.id">
                  <TableCell class="font-medium">{{ product.name }}</TableCell>
                  <TableCell>{{ product.sku }}</TableCell>
                  <TableCell class="text-right font-bold text-red-500">{{ product.stock }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Top 5 Productos Vendidos (30 días)</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Producto</TableHead>
                  <TableHead>SKU</TableHead>
                  <TableHead class="text-right">Cantidad Vendida</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="props.topSellingProducts.length === 0">
                  <TableCell colspan="3" class="text-center text-muted-foreground">No hay datos de ventas.</TableCell>
                </TableRow>
                <TableRow v-for="item in props.topSellingProducts" :key="item.product_id">
                  <TableCell class="font-medium">{{ item.product.name }}</TableCell>
                  <TableCell>{{ item.product.sku }}</TableCell>
                  <TableCell class="text-right font-bold">{{ item.total_quantity }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
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