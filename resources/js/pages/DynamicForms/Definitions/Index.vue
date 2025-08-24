<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { CirclePlus, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { can } from '@/lib/can'; // Assuming this helper exists

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Definiciones de Formularios', href: '/form-definitions' }
];

defineProps<{
    definitions: {
        data: Array<{
            id: number;
            name: string;
            slug: string;
            description: string | null;
            created_at: string;
        }>;
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
    };
    filters: {
        search: string;
    };
}>();

const deleteDefinition = (id: number) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta definición de formulario? Esto también eliminará todos los datos asociados.')) {
        router.delete(route('form-definitions.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (value: string) => {
    return new Date(value).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};
</script>

<template>
  <Head title="Definiciones de Formularios" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Título y botón -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Definiciones de Formularios</h1>
        <div>
          <Button
            as-child
            size="sm"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('form-definitions.create')"
          >
            <Link :href="route('form-definitions.create')">
              <CirclePlus class="mr-1" /> Nueva Definición
            </Link>
          </Button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption>Lista de definiciones de formularios</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">Slug</TableHead>
              <TableHead class="px-6 py-3">Descripción</TableHead>
              <TableHead class="px-6 py-3">Creado</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-if="definitions.data.length === 0">
                <TableCell colspan="5" class="text-center py-6 text-gray-500">
                    No hay definiciones de formularios registradas.
                </TableCell>
            </TableRow>
            <TableRow
                v-for="definition in definitions.data"
                :key="definition.id"
            >
              <TableCell class="px-6 py-4">{{ definition.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ definition.slug }}</TableCell>
              <TableCell class="px-6 py-4">{{ definition.description || 'N/A' }}</TableCell>
              <TableCell class="px-6 py-4">{{ formatDate(definition.created_at) }}</TableCell>
              <TableCell class="px-6 py-4 flex gap-2">
                <Button
                  as-child
                  size="sm"
                  class="bg-yellow-500 text-white hover:bg-yellow-700"
                  v-if="can('form-definitions.view')"
                >
                    <Link :href="route('form-definitions.show', definition.id)">
                        <Eye />
                    </Link>
                </Button>
                <Button
                  as-child
                  size="sm"
                  class="bg-blue-500 text-white hover:bg-blue-700"
                  v-if="can('form-definitions.edit')"
                >
                    <Link :href="route('form-definitions.edit', definition.id)">
                        <Pencil />
                    </Link>
                </Button>
                <Button
                  @click="deleteDefinition(definition.id)"
                  size="sm"
                  variant="destructive"
                  class="bg-red-600 text-white hover:bg-red-800"
                  v-if="can('form-definitions.delete')"
                >
                    <Trash2 />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2 p-4">
          <template v-for="(link, index) in definitions.links" :key="index">
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
             <span
                v-else
                class="px-3 py-1 rounded text-sm text-gray-400 cursor-not-allowed"
                v-html="link.label"
            />
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
