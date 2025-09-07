<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { can } from '@/lib/can';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: '/users' }
];

defineProps({
  users: Array
});

function deleteUser(id) {
  if (confirm("Are you sure you want to delete this user?")) {
    router.delete(route('users.destroy', id));
  }
}
</script>

<template>
  <Head title="Users" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Gestión de Usuarios</h1>
        <Link
          v-if="can('users.create')"
          :href="route('users.create')"
          class="bg-indigo-500 text-white hover:bg-indigo-700 px-4 py-2 rounded-lg text-sm font-medium shadow"
        >
          + Crear
        </Link>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">Nombre</th>
              <th class="px-6 py-3">Email</th>
              <th class="px-6 py-3">Roles</th>
              <th class="px-6 py-3">Empresa</th>
              <th class="px-6 py-3 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ user.id }}</td>
              <td class="px-6 py-4">{{ user.name }}</td>
              <td class="px-6 py-4">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span
                  v-for="role in user.roles"
                  :key="role.id"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1"
                >
                  {{ role.name }}
                </span>
              </td>
              <td class="px-6 py-4">{{ user.company?.name ?? '—' }}</td>
              <td class="px-6 py-4 flex gap-2 justify-center">
                <Link
                  :href="route('users.show', user.id)"
                  class="bg-gray-600 text-white hover:bg-gray-700 px-3 py-1.5 rounded shadow text-xs font-medium"
                >
                  Ver
                </Link>
                <Link
                  v-if="can('users.edit')"
                  :href="route('users.edit', user.id)"
                  class="bg-blue-500 text-white hover:bg-blue-700 px-3 py-1.5 rounded shadow text-xs font-medium"
                >
                  Editar
                </Link>
                <button
                  v-if="can('users.delete')"
                  @click="deleteUser(user.id)"
                  class="bg-rose-500 text-white hover:bg-rose-700 px-3 py-1.5 rounded shadow text-xs font-medium"
                >
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="users.length === 0" class="text-center text-gray-500 py-6">
          No hay usuarios registrados.
        </div>
      </div>
    </div>
  </AppLayout>
</template>
