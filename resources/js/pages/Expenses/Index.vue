<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
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
import { CirclePlus, Search, Eye, Trash2, Pencil } from 'lucide-vue-next';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gastos', href: route('expenses.index') }
];

const props = defineProps<{
  expenses: {
    data: Array<any>;
    links: Array<any>;
    meta: Record<string, any>;
  };
  filters: {
    search: string;
  };
}>();

const searchForm = useForm({
  search: props.filters.search || ''
});

function submitSearch() {
  searchForm.get(route('expenses.index'), {
    preserveScroll: true,
    preserveState: true
  });
}

const formatMoney = (amount: number) =>
  new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(amount);

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

function deleteExpense(id: number) {
  router.delete(route('expenses.destroy', id), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Gastos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Listado de Gastos</h1>
        <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700" v-if="can('expenses.create')">
          <Link :href="route('expenses.create')">
            <CirclePlus class="mr-1" /> Nuevo Gasto
          </Link>
        </Button>
      </div>

      <!-- Search -->
      <div class="w-full md:w-1/3">
        <form @submit.prevent="submitSearch" class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchForm.search"
            placeholder="Buscar por descripción..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
          />
          <Button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700">
            <Search class="w-4 h-4" />
          </Button>
        </form>
      </div>

      <!-- Table -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption>Historial de gastos</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Fecha</TableHead>
              <TableHead class="px-6 py-3">Descripción</TableHead>
              <TableHead class="px-6 py-3">Monto</TableHead>
              <TableHead class="px-6 py-3">Registrado por</TableHead>
              <TableHead class="px-6 py-3">Caja</TableHead>
              <TableHead class="px-6 py-3 text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-if="expenses.data.length === 0">
                <TableCell colspan="6" class="text-center py-6 text-gray-500">
                    No hay gastos registrados.
                </TableCell>
            </TableRow>
            <TableRow v-for="expense in expenses.data" :key="expense.id">
              <TableCell class="px-6 py-4">{{ formatDate(expense.date) }}</TableCell>
              <TableCell class="px-6 py-4">{{ expense.description }}</TableCell>
              <TableCell class="px-6 py-4">{{ formatMoney(expense.amount) }}</TableCell>
              <TableCell class="px-6 py-4">{{ expense.user?.name || 'N/A' }}</TableCell>
              <TableCell class="px-6 py-4">{{ expense.cash_register?.id || 'N/A' }}</TableCell>
              <TableCell class="px-6 py-4 flex gap-2 justify-end">
                <Button as-child size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700" v-if="can('expenses.view')">
                  <Link :href="route('expenses.show', expense.id)">
                    <Eye class="h-4 w-4" />
                  </Link>
                </Button>
                <Button as-child size="sm" class="bg-blue-500 text-white hover:bg-blue-700" v-if="can('expenses.edit')">
                  <Link :href="route('expenses.edit', expense.id)">
                    <Pencil class="h-4 w-4" />
                  </Link>
                </Button>
                <AlertDialog v-if="can('expenses.delete')">
                  <AlertDialogTrigger as-child>
                    <Button size="sm" class="bg-rose-500 text-white hover:bg-rose-700">
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle>¿Está seguro de eliminar este gasto?</AlertDialogTitle>
                      <AlertDialogDescription>
                        Esta acción no se puede deshacer. Esto eliminará permanentemente el gasto.
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                      <AlertDialogAction @click="deleteExpense(expense.id)">
                        Confirmar
                      </AlertDialogAction>
                      <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    </AlertDialogFooter>
                  </AlertDialogContent>
                </AlertDialog>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center space-x-2 p-4">
          <template v-for="(link, index) in expenses.links" :key="index">
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
             <span
                v-else
                class="px-3 py-1 rounded text-sm text-gray-400 cursor-not-allowed"
                v-html="link.label"
            />
          </template>
        </div>

        <div v-if="expenses.data.length === 0" class="text-center text-gray-500 py-6">
          No hay gastos registrados.
        </div>
      </div>
    </div>
  </AppLayout>
</template>