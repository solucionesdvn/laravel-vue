<script setup lang="ts">
import { ref } from 'vue';
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

import { Pencil, Trash, CirclePlus, Search } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categorias', href: '/categories' },
];

const props = defineProps<{
  categories: {
    data: Array<any>;
    links: Array<any>;
    meta: Record<string, any>;
  };
  filters: {
    search: string;
  };
}>();

// Formulario de búsqueda
const searchForm = useForm({
  search: props.filters.search || '',
});

function submitSearch() {
  searchForm.get(route('categories.index'), {
    preserveScroll: true,
    preserveState: true,
  });
}

function deleteCategory(id: number) {
  if (confirm('¿Está seguro de eliminar esta categoria?')) {
    router.delete(route('categories.destroy', id));
  }
}
</script>

<template>
  <Head title="Categorias" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Título y botón -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Lista de Categorias</h1>
        <div>
          <Button
            as-child
            size="default"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('categories.create')"
          >
            <Link :href="route('categories.create')">
              <CirclePlus class="mr-1 w-4 h-4" /> Nueva categoria
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
            placeholder="Buscar categoria..."
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
          <TableCaption>Tabla de Categorias</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">Descripción</TableHead>
              <TableHead class="px-6 py-3">Color</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="category in categories.data" :key="category.id">
              <TableCell class="px-6 py-4">{{ category.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ category.description }}</TableCell>
              <TableCell class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <span
                    class="inline-block w-5 h-5 rounded-full border border-gray-300"
                    :style="{ backgroundColor: category.color }"
                    :title="category.color"
                  ></span>
                  <span class="text-sm text-gray-600 dark:text-gray-300">{{ category.color }}</span>
                </div>
              </TableCell>
              <TableCell class="px-6 py-4 flex gap-2">
                <Button
                  as-child
                  size="sm"
                  class="bg-blue-500 text-white hover:bg-blue-700"
                  v-if="can('categories.edit')"
                  aria-label="Editar categoría"
                >
                  <Link :href="`/categories/${category.id}/edit`">
                    <Pencil class="w-4 h-4" />
                  </Link>
                </Button>
                <Button
                  size="sm"
                  class="bg-rose-500 text-white hover:bg-rose-700"
                  v-if="can('categories.delete')"
                  @click="deleteCategory(category.id)"
                  aria-label="Eliminar categoría"
                >
                  <Trash class="w-4 h-4" />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2">
          <template v-for="(link, index) in categories.links" :key="index">
            <button
              v-if="link.url"
              :class="[
                'px-3 py-1 rounded text-sm',
                link.active
                  ? 'bg-indigo-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              ]"
              @click="router.visit(link.url, { preserveScroll: true })"
              v-html="link.label"
            ></button>
          </template>
        </div>

        <div v-if="categories.data.length === 0" class="text-center text-gray-500 py-6">
          No hay categorías registradas.
        </div>
      </div>
    </div>
  </AppLayout>
</template>
