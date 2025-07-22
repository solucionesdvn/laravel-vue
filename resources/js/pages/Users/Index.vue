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
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">User Management</h1>
        <Link
          v-if="can('users.create')"
          :href="route('users.create')"
          class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow transition-colors"
        >
          + Create
        </Link>
      </div>

      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ user.id }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ user.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ user.email }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                <span
                  v-for="role in user.roles"
                  :key="role.id"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1"
                >
                  {{ role.name }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                {{ user.company?.name ?? '—' }}
              </td>
              <td class="px-6 py-4 text-sm text-center space-x-2">
                <Link
                  :href="route('users.show', user.id)"
                  class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-white bg-gray-600 hover:bg-gray-700 rounded shadow"
                >
                  Show
                </Link>
                <Link
                  v-if="can('users.edit')"
                  :href="route('users.edit', user.id)"
                  class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded shadow"
                >
                  Edit
                </Link>
                <button
                  v-if="can('users.delete')"
                  @click="deleteUser(user.id)"
                  class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded shadow"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
