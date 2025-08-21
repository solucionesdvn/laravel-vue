<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { can } from '@/lib/can';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Pencil, Trash, CirclePlus, Search, CheckCircle, XCircle } from 'lucide-vue-next';
import { watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Métodos de Pago', href: route('payment-methods.index') }
];

const props = defineProps<{
  paymentMethods: {
    data: Array<{ id: number; name: string; }>;
    links: Array<{ url: string | null; label: string; active: boolean }>;
  };
  filters: {
    search: string;
  };
}>();

const form = useForm({
  search: props.filters.search,
});

const deleteItem = (id: number) => {
  if (confirm('¿Está seguro de eliminar este método de pago?')) {
    router.delete(route('payment-methods.destroy', id), {
      preserveScroll: true,
    });
  }
};

const debounce = (fn: Function, delay: number) => {
  let timeoutId: ReturnType<typeof setTimeout>;
  return function (this: any, ...args: any[]) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
};

watch(
  () => form.search,
  debounce((value: string) => {
    router.get(route('payment-methods.index'), { search: value }, { preserveState: true, replace: true });
  }, 300)
);

</script>

<template>
  <Head title="Métodos de Pago" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 space-y-6">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Métodos de Pago</h1>
        <Button as-child size="sm" v-if="can('payment-methods.create')">
          <Link :href="route('payment-methods.create')">
            <CirclePlus class="mr-1 h-4 w-4" /> Nuevo Método
          </Link>
        </Button>
      </div>

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

      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nombre</TableHead>
              <TableHead class="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="paymentMethods.data.length === 0">
                <TableCell :colspan="2" class="text-center py-6 text-gray-500">
                    No hay métodos de pago registrados.
                </TableCell>
            </TableRow>
            <TableRow v-for="item in paymentMethods.data" :key="item.id">
              <TableCell class="font-medium">{{ item.name }}</TableCell>
              
              <TableCell>
                <div class="flex justify-end gap-2">
                  <Button as-child size="icon" variant="outline" v-if="can('payment-methods.edit')">
                    <Link :href="route('payment-methods.edit', item.id)">
                      <Pencil class="h-4 w-4" />
                    </Link>
                  </Button>
                  <Button size="icon" variant="destructive" v-if="can('payment-methods.delete')" @click="deleteItem(item.id)">
                    <Trash class="h-4 w-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      
      <div v-if="paymentMethods.links.length > 3" class="flex justify-center mt-4">
        <div class="flex flex-wrap -mb-1">
          <template v-for="(link, key) in paymentMethods.links" :key="key">
            <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
            <Link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                  :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url" v-html="link.label" />
          </template>
        </div>
      </div>

    </div>
  </AppLayout>
</template>