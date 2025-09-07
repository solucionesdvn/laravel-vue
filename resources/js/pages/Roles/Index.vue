<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { can } from '@/lib/can';
import { computed } from 'vue';

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
import { Pencil, Trash, CirclePlus } from 'lucide-vue-next';

const props = defineProps<{
  roles: Array<{
    id: number;
    name: string;
    permissions: Array<{ name: string }>;
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Roles', href: route('roles.index') },
];

function deleteRole(id: number) {
    router.delete(route('roles.destroy', id));
}

const groupPermissions = (permissions: Array<{ name: string }>) => {
  const grouped: Record<string, string[]> = {};
  permissions.forEach(p => {
    const [group, ...actionParts] = p.name.split('.');
    const action = actionParts.join('.');
    if (!grouped[group]) {
      grouped[group] = [];
    }
    grouped[group].push(action);
  });
  return grouped;
};

const getBadgeClass = (action: string) => {
  const colors: Record<string, string> = {
    view: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    create: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    edit: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    delete: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
  };
  return colors[action] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 space-y-6">
            <!-- Título y botón -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Gestión de Roles</h1>
                <div>
                    <Button
                        as-child
                        size="sm"
                        class="bg-indigo-500 text-white hover:bg-indigo-700"
                        v-if="can('roles.create')"
                    >
                        <Link :href="route('roles.create')">
                            <CirclePlus class="mr-1 h-4 w-4" /> Nuevo Rol
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                <Table>
                    <TableCaption v-if="roles.length === 0">No hay roles definidos.</TableCaption>
                    <TableHeader class="bg-gray-50 dark:bg-gray-800">
                        <TableRow>
                            <TableHead class="w-[150px]">Rol</TableHead>
                            <TableHead>Permisos</TableHead>
                            <TableHead class="w-[150px] text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody class="bg-white dark:bg-gray-900">
                        <TableRow v-for="role in roles" :key="role.id" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <TableCell class="font-medium align-top pt-4">{{ role.name }}</TableCell>
                            <TableCell>
                                <div class="flex flex-col gap-2">
                                    <div v-for="(actions, group) in groupPermissions(role.permissions)" :key="group" class="flex items-baseline flex-wrap">
                                        <strong class="capitalize mr-2 min-w-[120px]">{{ group.replace('-', ' ') }}:</strong>
                                        <div class="flex flex-wrap gap-1">
                                            <span v-for="action in actions" :key="action"
                                                class="text-xs font-medium px-2.5 py-0.5 rounded-full"
                                                :class="getBadgeClass(action)"
                                            >
                                                {{ action }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell class="text-right align-top pt-4">
                                <div class="flex justify-end gap-2">
                                    <Button as-child size="icon" variant="outline" class="bg-blue-500 text-white hover:bg-blue-700" v-if="can('roles.edit')">
                                        <Link :href="route('roles.edit', role.id)">
                                            <Pencil class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <AlertDialog v-if="can('roles.delete')">
                                        <AlertDialogTrigger as-child>
                                            <Button size="icon" variant="outline" class="bg-rose-500 text-white hover:bg-rose-700">
                                                <Trash class="h-4 w-4" />
                                            </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>¿Está seguro de que desea eliminar este rol?</AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    Esta acción no se puede deshacer. Esto eliminará permanentemente el rol y sus permisos asociados.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                                <AlertDialogAction @click="deleteRole(role.id)">
                                                    Continuar
                                                </AlertDialogAction>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>