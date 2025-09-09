<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { can } from '@/lib/can'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { CirclePlus, Lock, Unlock } from 'lucide-vue-next'
import { format } from 'date-fns';

const props = defineProps<{ 
  openRegister: {
    id: number
    user?: { name: string }
    opened_at: string
    opening_amount: number
    notes?: string
    sales?: Array<{ cash_register_id: number; payment_method_id: number; total_sales_by_method: number }>
    sales_sum_total?: number
    sales_count?: number
    total_expenses?: number // Add this line
  } | null
  closedRegisters: {
    data: Array<any>
    links: Array<any>
  }
  paymentMethods: Array<{ id: number; name: string }>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gestión de Caja', href: route('cash-registers.index') },
]

const formatDate = (dateString: string) => {
    return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};

const formatMoney = (amount: number) =>
  new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(amount)

const formattedSalesByMethod = (salesData: any[] | undefined) => {
  if (!salesData || salesData.length === 0) {
    return 'N/A';
  }
  const salesMap = new Map();
  salesData.forEach(sale => {
    const methodName = props.paymentMethods.find(pm => pm.id === sale.payment_method_id)?.name || `Método ${sale.payment_method_id}`;
    salesMap.set(methodName, (salesMap.get(methodName) || 0) + sale.total_sales_by_method);
  });

  let result = '';
  salesMap.forEach((total, name) => {
    result += `${name}: ${formatMoney(total)}
`;
  });
  return result.trim();
};

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Gestión de Caja" />

    <div class="p-4 sm:p-6 space-y-6">
      <!-- Estado Actual -->
      <Card class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border-2">
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-2xl font-bold text-gray-800 dark:text-white">
                <template v-if="openRegister">
                    <Unlock class="h-7 w-7 text-green-500" />
                    <span>Caja Abierta</span>
                </template>
                <template v-else>
                    <Lock class="h-7 w-7 text-red-500" />
                    <span>Caja Cerrada</span>
                </template>
            </CardTitle>
            <CardDescription>
                {{ openRegister ? 'Actualmente hay una caja abierta. Todas las ventas se registrarán aquí.' : 'No hay ninguna caja abierta. Debe abrir una para empezar a vender.' }}
            </CardDescription>
        </CardHeader>
        <CardContent>
            <div v-if="openRegister" class="space-y-2 text-sm">
                <p><span class="font-semibold">Usuario:</span> {{ openRegister.user?.name }}</p>
                <p><span class="font-semibold">Fecha de Apertura:</span> {{ formatDate(openRegister.opened_at) }}</p>
                <p><span class="font-semibold">Monto Inicial:</span> {{ formatMoney(openRegister.opening_amount) }}</p>
                <p v-if="openRegister.sales_sum_total !== null"><span class="font-semibold">Consolidado Recaudado:</span> {{ formatMoney(openRegister.sales_sum_total) }}</p>
                <p v-if="openRegister.sales_count !== undefined"><span class="font-semibold">Cantidad de Ventas:</span> {{ openRegister.sales_count }}</p>
                <p v-if="openRegister.total_expenses !== undefined"><span class="font-semibold">Total Gastos:</span> {{ formatMoney(openRegister.total_expenses) }}</p>
                <p v-if="openRegister.sales_sum_total !== undefined && openRegister.total_expenses !== undefined"><span class="font-semibold">Balance Total:</span> {{ formatMoney(Number(openRegister.opening_amount) + Number(openRegister.sales_sum_total || 0) - Number(openRegister.total_expenses || 0)) }}</p>
                <p v-if="openRegister.sales && openRegister.sales.length > 0"><span class="font-semibold">Ventas por Método:</span><br><pre>{{ formattedSalesByMethod(openRegister.sales) }}</pre></p>
                <div class="pt-4">
                    <Button as-child size="lg" v-if="can('cash-registers.close')">
                        <Link :href="route('cash-registers.close', openRegister.id)">Cerrar Caja</Link>
                    </Button>
                </div>
            </div>
            <div v-else class="pt-4">
                <Button as-child size="lg" v-if="can('cash-registers.create')">
                    <Link :href="route('cash-registers.create')">
                        <CirclePlus class="mr-2" /> Abrir Nueva Caja
                    </Link>
                </Button>
            </div>
        </CardContent>
      </Card>

      <!-- Historial -->
      <div class="space-y-4 pt-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Historial de Cajas Cerradas</h2>
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <Table>
                <TableCaption>Historial de cajas finalizadas</TableCaption>
                <TableHeader>
                    <TableRow>
                        
                        <TableHead>Usuario</TableHead>
                        <TableHead>Apertura</TableHead>
                        <TableHead>Cierre</TableHead>
                        <TableHead>Monto Inicial</TableHead>
                        <TableHead>Monto Final</TableHead>
                        <TableHead>Ventas</TableHead>
                        <TableHead>Ventas por Método</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="closedRegisters.data.length === 0">
                        <TableCell :colspan="7" class="text-center py-6 text-gray-500">
                            No hay registros de cajas cerradas.
                        </TableCell>
                    </TableRow>
                    <TableRow v-for="register in closedRegisters.data" :key="register.id">
                        
                        <TableCell>{{ register.user?.name || 'N/A' }}</TableCell>
                        <TableCell>{{ formatDate(register.opened_at) }}</TableCell>
                        <TableCell>{{ register.closed_at ? formatDate(register.closed_at) : '' }}</TableCell>
                        <TableCell>{{ formatMoney(register.opening_amount) }}</TableCell>
                        <TableCell>{{ formatMoney(register.closing_amount) }}</TableCell>
                        <TableCell>{{ formatMoney(register.total_sales) }}</TableCell>
                        <TableCell><pre>{{ formattedSalesByMethod(register.sales) }}</pre></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <!-- Paginación aquí si es necesario -->
        </div>
      </div>
    </div>
  </AppLayout>
</template>