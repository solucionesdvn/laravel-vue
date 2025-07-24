<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { can } from '@/lib/can';
import { Button } from '@/components/ui/button';
import { Pencil, Trash, CirclePlus } from 'lucide-vue-next';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';



const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Proveedores', href: '/suppliers' }
];

defineProps({
  suppliers: Array
});

function deleteSupplier(id) {
  if (confirm("Esta seguro de eliminar este proveedor?")) {
    router.delete(route('suppliers.destroy', id));
  }
}
</script>

<template>
  <Head title="Proveedores" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Lista de Proveedores</h1>
        <div class="flex">
            <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                <Link 
                  v-if="can('suppliers.create')"
                    :href="route('suppliers.create')"
                    href="/suppliers/create"> 
                    <CirclePlus/>Nuevo proveedor
              </Link>
            </Button>
        </div>


      </div>

      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableHead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de contacto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo Electronico</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIT</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
          </TableHead>
          <tbody 
          v-if="suppliers.length"
          class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700" >
            <tr v-for="supplier in suppliers" :key="supplier.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ supplier.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ supplier.contact_name }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ supplier.email }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ supplier.address }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ supplier.nit }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                <Button as-child size="sm" class="bg-blue-500 text-white hover:bg-blue-700">
                  <Link 
                  v-if="can('suppliers.edit')"
                  :href="`/suppliers/${supplier.id}/edit`">
                      <Pencil />
                  </Link>
                </Button>
                <Button size="sm" class="bg-rose-500 text-white hover:bg-rose-700" 
                  v-if="can('suppliers.delete')"                
                  @click="deleteSupplier(supplier.id)">
                  <Trash />
              </Button>
              </td>
            </tr>
          </tbody>
        </Table>
        <div v-if="suppliers.length === 0" class="text-center text-gray-500 py-8">
          No hay Proveedores registrados.
        </div>
      </div>
    </div>
  </AppLayout>
</template>
