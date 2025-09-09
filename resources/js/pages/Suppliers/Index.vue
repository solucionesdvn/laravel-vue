<script setup lang="ts">
import { ref, watch } from 'vue';
import ShowSupplier from './Show.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { can } from '@/lib/can';
import { debounce } from 'lodash';

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
const form = useForm({
  search: props.filters.search || ''
});

// Búsqueda con debounce al escribir
watch(
  () => form.search,
  debounce((newValue) => {
    router.get(route('suppliers.index'), { search: newValue }, {
      preserveState: true,
      replace: true,
    });
  }, 300)
);

function deleteSupplier(id: number) {
  router.delete(route('suppliers.destroy', id));
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
        <div class="relative">
          <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            type="search"
            v-model="form.search"
            placeholder="Buscar proveedor..."
            class="w-full pl-8"
          />
        </div>
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
              <TableHead class="px-6 py-3">Teléfono</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="supplier in suppliers.data" :key="supplier.id">
              <TableCell class="px-6 py-4">{{ supplier.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ supplier.contact_name }}</TableCell>
              <TableCell class="px-6 py-4">{{ supplier.email }}</TableCell>
              <TableCell class="px-6 py-4">{{ supplier.phone }}</TableCell>
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
                <AlertDialog v-if="can('suppliers.delete')">
                  <AlertDialogTrigger as-child>
                    <Button
                      size="sm"
                      class="bg-rose-500 text-white hover:bg-rose-700"
                    >
                      <Trash />
                    </Button>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle>¿Está seguro de eliminar este proveedor?</AlertDialogTitle>
                      <AlertDialogDescription>
                        Esta acción no se puede deshacer. Esto eliminará permanentemente el proveedor.
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                      <AlertDialogAction @click="deleteSupplier(supplier.id)">
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

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2">
          <template v-for="(link, index) in suppliers.links" :key="index">
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