<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  expense: {
    id: number;
    date: string;
    amount: number;
    description: string;
    notes: string | null;
    user: { id: number; name: string };
    cash_register: { id: number; opened_at: string };
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gastos', href: route('expenses.index') },
  { title: `Gasto #${props.expense.id}`, href: null },
];

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const formatMoney = (value: number) => {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
  <Head :title="`Gasto #${expense.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 w-full">
      <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <CardHeader>
          <div class="flex justify-between items-center">
            <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
              Detalles del Gasto #{{ expense.id }}
            </CardTitle>
            <Button as-child variant="outline">
                <Link :href="route('expenses.index')">Volver al Listado</Link>
            </Button>
          </div>
        </CardHeader>
        <CardContent class="space-y-6 pt-6">
          <!-- Info General -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Fecha:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ formatDate(expense.date) }}</p>
            </div>
            <div>
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Monto:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ formatMoney(expense.amount) }}</p>
            </div>
            <div>
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Registrado por:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ expense.user.name }}</p>
            </div>
            <div>
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Caja Registradora:</p>
              <p class="text-gray-800 dark:text-gray-200">#{{ expense.cash_register.id }} (Abierta desde: {{ formatCashRegisterDate(expense.cash_register.opened_at) }})</p>
            </div>
            <div class="md:col-span-3">
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Descripción:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ expense.description }}</p>
            </div>
            <div class="md:col-span-3">
              <p class="text-gray-500 dark:text-gray-400 font-semibold">Notas:</p>
              <p class="text-gray-800 dark:text-gray-200">{{ expense.notes || 'Sin notas.' }}</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>