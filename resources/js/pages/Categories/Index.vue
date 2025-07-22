<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { can } from '@/lib/can';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categorias', href: '/categories' },
];

defineProps<{
  categories: Array<{
    id: number;
    name: string;
    description?: string;
  }>;
}>();

function deleteCategory(id: number) {
  if (confirm('Are you sure you want to delete this category?')) {
    router.delete(route('categories.destroy', id));
  }
}
</script>

<template>
  <Head title="Categorias" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-4">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Gestión Categorias</h1>
        <Link
          v-if="can('categories.create')"
          :href="route('categories.create')"
          class="btn-primary"
        >
          Create
        </Link>
      </div>

      <div class="overflow-x-auto rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-400">
            <tr>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Description</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>

          <tbody v-if="categories.length">
            <tr
              v-for="category in categories"
              :key="category.id"
              class="border-b odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800"
            >
              <td class="px-4 py-2">{{ category.name }}</td>
              <td class="px-4 py-2">{{ category.description ?? '-' }}</td>
              <td class="px-4 py-2 text-center space-x-2">
                <Link
                  v-if="can('categories.edit')"
                  :href="route('categories.edit', category.id)"
                  class="btn-primary"
                >
                  Edit
                </Link>
                <button
                  v-if="can('categories.delete')"
                  @click="deleteCategory(category.id)"
                  class="btn-danger"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>

          <tbody v-else>
            <tr>
              <td colspan="3" class="text-center text-gray-500 dark:text-gray-400 p-4">
                No categories found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
