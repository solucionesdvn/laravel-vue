<script setup lang="ts">
import { ref, watch } from 'vue';
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
import { Input } from '@/components/ui/input';
import { Pencil, Trash, CirclePlus, Search } from 'lucide-vue-next';
import { debounce } from 'lodash';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Clientes', href: route('clients.index') }
];

const props = defineProps<{
  clients: {
    data: Array<any>;
    links: Array<any>;
  };
  filters: {
    search: string;
  };
}>();

// Formulario de búsqueda
const form = useForm({
  search: props.filters.search,
});

// Búsqueda con debounce al escribir
watch(
  () => form.search,
  debounce((newValue) => {
    router.get(route('clients.index'), { search: newValue }, {
      preserveState: true,
      replace: true,
    });
  }, 300)
);

function deleteClient(id: number) {
  if (confirm("¿Está seguro de eliminar este cliente?")) {
    router.delete(route('clients.destroy', id));
  }
}
</script>

<template>
  <Head title="Clientes" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 space-y-6">
      <!-- Título y botón -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Lista de Clientes</h1>
        <div>
          <Button
            as-child
            size="sm"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('clients.create')"
          >
            <Link :href="route('clients.create')">
              <CirclePlus class="mr-1 h-4 w-4" /> Nuevo Cliente
            </Link>
          </Button>
        </div>
      </div>

      <!-- Buscador -->
      <div class="w-full md:w-1/3">
        <div class="relative">
          <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            type="search"
            v-model="form.search"
            placeholder="Buscar por nombre o email..."
            class="w-full pl-8"
          />
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption v-if="clients.data.length === 0">No hay clientes registrados.</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">Email</TableHead>
              <TableHead class="px-6 py-3">Teléfono</TableHead>
              <TableHead class="px-6 py-3 text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="client in clients.data" :key="client.id">
              <TableCell class="px-6 py-4 font-medium">{{ client.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ client.email }}</TableCell>
              <TableCell class="px-6 py-4">{{ client.phone }}</TableCell>
              <TableCell class="px-6 py-4">
                <div class="flex justify-end gap-2">
                  <Button
                    as-child
                    size="icon"
                    variant="outline"
                    class="bg-blue-500 text-white hover:bg-blue-700"
                    v-if="can('clients.edit')"
                  >
                    <Link :href="route('clients.edit', client.id)">
                      <Pencil class="h-4 w-4" />
                    </Link>
                  </Button>
                  <Button
                    size="icon"
                    variant="outline"
                    class="bg-rose-500 text-white hover:bg-rose-700"
                    v-if="can('clients.delete')"
                    @click="deleteClient(client.id)"
                  >
                    <Trash class="h-4 w-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Paginación -->
      <div v-if="clients.data.length > 0" class="mt-4 flex justify-center space-x-2">
        <Link
          v-for="(link, index) in clients.links"
          :key="index"
          :href="link.url || ''"
          v-html="link.label"
          class="px-3 py-1 rounded text-sm"
          :class="{
            'bg-indigo-600 text-white': link.active,
            'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600': !link.active,
            'cursor-not-allowed text-gray-400': !link.url
          }"
          :disabled="!link.url"
        />
      </div>
    </div>
  </AppLayout>
</template>