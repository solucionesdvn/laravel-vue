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
import { Pencil, Trash, CirclePlus, Search } from 'lucide-vue-next';
import { debounce } from 'lodash';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categorías', href: route('categories.index') }
];

const props = defineProps<{
  categories: {
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
    router.get(route('categories.index'), { search: newValue }, {
      preserveState: true,
      replace: true,
    });
  }, 300)
);

function deleteCategory(id: number) {
  router.delete(route('categories.destroy', id));
}
</script>

<template>
  <Head title="Categorías" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 space-y-6">
      <!-- Título y botón -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Lista de Categorías</h1>
        <div>
          <Button
            as-child
            size="sm"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('categories.create')"
          >
            <Link :href="route('categories.create')">
              <CirclePlus class="mr-1 h-4 w-4" /> Nueva Categoría
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
            placeholder="Buscar por nombre o descripción..."
            class="w-full pl-8"
          />
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption v-if="categories.data.length === 0">No hay categorías registradas.</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">Descripción</TableHead>
              <TableHead class="px-6 py-3">Color</TableHead>
              <TableHead class="px-6 py-3 text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="category in categories.data" :key="category.id">
              <TableCell class="px-6 py-4 font-medium">{{ category.name }}</TableCell>
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
              <TableCell class="px-6 py-4">
                <div class="flex justify-end gap-2">
                  <Button
                    as-child
                    size="icon"
                    variant="outline"
                    class="bg-blue-500 text-white hover:bg-blue-700"
                    v-if="can('categories.edit')"
                  >
                    <Link :href="route('categories.edit', category.id)">
                      <Pencil class="h-4 w-4" />
                    </Link>
                  </Button>
                  <AlertDialog v-if="can('categories.delete')">
                    <AlertDialogTrigger as-child>
                      <Button
                        size="icon"
                        variant="outline"
                        class="bg-rose-500 text-white hover:bg-rose-700"
                      >
                        <Trash class="h-4 w-4" />
                      </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                      <AlertDialogHeader>
                        <AlertDialogTitle>¿Está seguro de eliminar esta categoría?</AlertDialogTitle>
                        <AlertDialogDescription>
                          Esta acción no se puede deshacer. Esto eliminará permanentemente la categoría.
                        </AlertDialogDescription>
                      </AlertDialogHeader>
                      <AlertDialogFooter>
                        <AlertDialogAction @click="deleteCategory(category.id)">
                          Continuar
                        </AlertDialogAction>
                        <AlertDialogCancel>Cancelar</AlertDialogCancel>
                      </AlertDialogFooter>
                    </AlertDialogContent>
                  </AlertDialog>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Paginación -->
      <div v-if="categories.data.length > 0" class="mt-4 flex justify-center space-x-2">
        <template v-for="(link, index) in categories.links" :key="index">
          <button
            v-if="link.url"
            :class="[ 'px-3 py-1 rounded text-sm',
              link.active
                ? 'bg-indigo-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'
            ]"
            @click="router.visit(link.url, { preserveScroll: true })"
            v-html="link.label"
          ></button>
          <span
            v-else
            class="px-3 py-1 rounded text-sm text-gray-400 cursor-not-allowed"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </AppLayout>
</template>