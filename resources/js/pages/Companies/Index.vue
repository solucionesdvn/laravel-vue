<script setup lang="ts">
import { watch } from 'vue';
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
  { title: 'Empresas', href: route('companies.index') }
];

const props = defineProps<{
  companies: {
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
    router.get(route('companies.index'), { search: newValue }, {
      preserveState: true,
      replace: true,
    });
  }, 300)
);

function deleteCompany(id: number) {
  if (confirm("¿Está seguro de eliminar esta empresa?")) {
    router.delete(route('companies.destroy', id));
  }
}
</script>

<template>
  <Head title="Empresas" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 space-y-6">
      <!-- Título y botón -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Lista de Empresas</h1>
        <div>
          <Button
            as-child
            size="sm"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('companies.create')"
          >
            <Link :href="route('companies.create')">
              <CirclePlus class="mr-1 h-4 w-4" /> Nueva Empresa
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
            placeholder="Buscar por nombre..."
            class="w-full pl-8"
          />
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption v-if="companies.data.length === 0">No hay empresas registradas.</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">NIT</TableHead>
              <TableHead class="px-6 py-3">Dirección</TableHead>
              <TableHead class="px-6 py-3 text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="company in companies.data" :key="company.id">
              <TableCell class="px-6 py-4 font-medium">{{ company.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ company.nit }}</TableCell>
              <TableCell class="px-6 py-4">{{ company.address }}</TableCell>
              <TableCell class="px-6 py-4">
                <div class="flex justify-end gap-2">
                  <Button
                    as-child
                    size="icon"
                    variant="outline"
                    class="bg-blue-500 text-white hover:bg-blue-700"
                    v-if="can('companies.edit')"
                  >
                    <Link :href="route('companies.edit', company.id)">
                      <Pencil class="h-4 w-4" />
                    </Link>
                  </Button>
                  <Button
                    size="icon"
                    variant="outline"
                    class="bg-rose-500 text-white hover:bg-rose-700"
                    v-if="can('companies.delete')"
                    @click="deleteCompany(company.id)"
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
      <div v-if="companies.data.length > 0" class="mt-4 flex justify-center space-x-2">
        <Link
          v-for="(link, index) in companies.links"
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