<script setup lang="ts">
import { ref } from 'vue';
import ShowSupplier from './Show.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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

import { Pencil, Trash, CirclePlus, Eye, Search } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Proveedores', href: '/suppliers' }
];

const props = defineProps<{
  suppliers: {
    data: Array<any>;
    links: Array<any>;
    meta: Record<string, any>;
  };
  filters: {
    search: string;
  };
}>();

const selectedSupplier = ref(null);
const showModal = ref(false);

function viewSupplier(supplier: any) {
  selectedSupplier.value = supplier;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  selectedSupplier.value = null;
}

// Formulario de búsqueda
const searchForm = useForm({
  search: props.filters.search || ''
});

function submitSearch() {
  searchForm.get(route('suppliers.index'), {
    preserveScroll: true,
    preserveState: true
  });
}

function deleteSupplier(id: number) {
  if (confirm("¿Está seguro de eliminar este proveedor?")) {
    router.delete(route('suppliers.destroy', id));
  }
}
</script>

<template>
  <Head title="Proveedores" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Título y botón -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Lista de Proveedores</h1>
        <div>
          <Button
            as-child
            size="sm"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('suppliers.create')"
          >
            <Link :href="route('suppliers.create')">
              <CirclePlus class="mr-1" /> Nuevo proveedor
            </Link>
          </Button>
        </div>
      </div>

      <!-- Buscador -->
      <div class="w-full md:w-1/3">
        <form @submit.prevent="submitSearch" class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchForm.search"
            placeholder="Buscar proveedor..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
          />
          <Button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700">
            <Search class="w-4 h-4" />
          </Button>
        </form>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption>Tabla de proveedores</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">Contacto</TableHead>
              <TableHead class="px-6 py-3">Correo</TableHead>
              <TableHead class="px-6 py-3">Dirección</TableHead>
              <TableHead class="px-6 py-3">NIT</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="supplier in suppliers.data" :key="supplier.id">
              <TableCell class="px-6 py-4">{{ supplier.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ supplier.contact_name }}</TableCell>
              <TableCell class="px-6 py-4">{{ supplier.email }}</TableCell>
              <TableCell class="px-6 py-4">{{ supplier.address }}</TableCell>
              <TableCell class="px-6 py-4">{{ supplier.nit }}</TableCell>
              <TableCell class="px-6 py-4 flex gap-2">
                <Button
                  size="sm"
                  class="bg-yellow-500 text-white hover:bg-yellow-700"
                  v-if="can('suppliers.view')"
                  @click="viewSupplier(supplier)"
                >
                  <Eye />
                </Button>

                <Button
                  as-child
                  size="sm"
                  class="bg-blue-500 text-white hover:bg-blue-700"
                  v-if="can('suppliers.edit')"
                >
                  <Link :href="`/suppliers/${supplier.id}/edit`">
                    <Pencil />
                  </Link>
                </Button>
                <Button
                  size="sm"
                  class="bg-rose-500 text-white hover:bg-rose-700"
                  v-if="can('suppliers.delete')"
                  @click="deleteSupplier(supplier.id)"
                >
                  <Trash />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2">
          <template v-for="(link, index) in suppliers.links" :key="index">
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
          </template>
        </div>

        <div v-if="suppliers.data.length === 0" class="text-center text-gray-500 py-6">
          No hay proveedores registrados.
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Modal fuera del v-for -->
  <ShowSupplier
    v-if="showModal"
    :supplier="selectedSupplier"
    :show="showModal"
    @close="closeModal"
  />
</template>
