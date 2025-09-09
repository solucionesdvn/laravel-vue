'''<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { can } from '@/lib/can';
import { debounce } from 'lodash';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
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
import { Pencil, Trash, CirclePlus, Search, Undo } from 'lucide-vue-next';

const props = defineProps<{
  users: {
    data: Array<any>;
    links: Array<any>;
  };
  filters: {
    search: string;
    trashed: string;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Usuarios', href: route('users.index') }
];

const form = useForm({
  search: props.filters.search,
  trashed: props.filters.trashed,
});

watch(
  () => form.data(),
  debounce(() => {
    form.get(route('users.index'), {
      preserveState: true,
      replace: true,
    });
  }, 300)
);

function destroy(id: number) {
  router.delete(route('users.destroy', id));
}

function forceDelete(id: number) {
  router.delete(route('users.force-delete', id));
}

function restore(id: number) {
  router.put(route('users.restore', id));
}

</script>

<template>
  <Head title="Usuarios" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 space-y-6">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Gestión de Usuarios</h1>
        <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700" v-if="can('users.create')">
          <Link :href="route('users.create')">
            <CirclePlus class="mr-1 h-4 w-4" /> Nuevo Usuario
          </Link>
        </Button>
      </div>

      <div class="flex items-center justify-between gap-4">
        <div class="relative w-full md:w-1/3">
          <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            type="search"
            v-model="form.search"
            placeholder="Buscar por nombre o email..."
            class="w-full pl-8"
          />
        </div>
        <Select v-model="form.trashed">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Filtrar por estado" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="no">Activos</SelectItem>
            <SelectItem value="only">En Papelera</SelectItem>
            <SelectItem value="with">Todos</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table>
          <TableCaption v-if="users.data.length === 0">No se encontraron usuarios.</TableCaption>
          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead>Nombre</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Empresa</TableHead>
              <TableHead>Roles</TableHead>
              <TableHead class="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody class="bg-white dark:bg-gray-900">
            <TableRow v-for="user in users.data" :key="user.id" :class="{ 'bg-red-100 dark:bg-red-900/30': user.deleted_at }">
              <TableCell class="font-medium">{{ user.name }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>{{ user.company?.name ?? '—' }}</TableCell>
              <TableCell>
                <span
                  v-for="role in user.roles"
                  :key="role.id"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1"
                >
                  {{ role.name }}
                </span>
              </TableCell>
              <TableCell class="text-right">
                <div class="flex justify-end gap-2">
                  <template v-if="user.deleted_at">
                    <AlertDialog>
                      <AlertDialogTrigger as-child>
                        <Button size="icon" variant="outline" class="bg-red-500 text-white hover:bg-red-700">
                          <Trash class="h-4 w-4" />
                        </Button>
                      </AlertDialogTrigger>
                      <AlertDialogContent>
                        <AlertDialogHeader>
                          <AlertDialogTitle>¿Está seguro de eliminar permanentemente?</AlertDialogTitle>
                          <AlertDialogDescription>
                            Esta acción no se puede deshacer. El usuario será eliminado de forma definitiva.
                          </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                          <AlertDialogAction @click="forceDelete(user.id)">Confirmar</AlertDialogAction>
                          <AlertDialogCancel>Cancelar</AlertDialogCancel>
                        </AlertDialogFooter>
                      </AlertDialogContent>
                    </AlertDialog>
                    <Button size="icon" variant="outline" class="bg-green-500 text-white hover:bg-green-700" @click="restore(user.id)">
                      <Undo class="h-4 w-4" />
                    </Button>
                  </template>
                  <template v-else>
                    <Button as-child size="icon" variant="outline" class="bg-blue-500 text-white hover:bg-blue-700" v-if="can('users.edit')">
                      <Link :href="route('users.edit', user.id)">
                        <Pencil class="h-4 w-4" />
                      </Link>
                    </Button>
                    <AlertDialog v-if="can('users.delete')">
                      <AlertDialogTrigger as-child>
                        <Button size="icon" variant="outline" class="bg-rose-500 text-white hover:bg-rose-700">
                          <Trash class="h-4 w-4" />
                        </Button>
                      </AlertDialogTrigger>
                      <AlertDialogContent>
                        <AlertDialogHeader>
                          <AlertDialogTitle>¿Enviar a la papelera?</AlertDialogTitle>
                          <AlertDialogDescription>
                            El usuario será enviado a la papelera y podrá ser restaurado después.
                          </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                          <AlertDialogAction @click="destroy(user.id)">Confirmar</AlertDialogAction>
                          <AlertDialogCancel>Cancelar</AlertDialogCancel>
                        </AlertDialogFooter>
                      </AlertDialogContent>
                    </AlertDialog>
                  </template>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Paginación -->
      <div v-if="users.links.length > 3" class="mt-4 flex justify-center space-x-1">
        <Link
          v-for="(link, index) in users.links"
          :key="index"
          :href="link.url ?? '#'"
          :disabled="!link.url"
          class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200"
          :class="{
            'bg-indigo-600 text-white pointer-events-none': link.active,
            'text-gray-700 bg-gray-200 hover:bg-gray-300 dark:text-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600': !link.active && link.url,
            'text-gray-400 cursor-not-allowed': !link.url
          }"
          v-html="link.label"
        />
      </div>
    </div>
  </AppLayout>
</template>
'''