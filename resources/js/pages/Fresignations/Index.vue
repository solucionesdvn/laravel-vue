<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { can } from '@/lib/can';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Fresignations', href: '/fresignations' },
];

defineProps<{
  fresignations: Array<any>
}>();

function deleteFresignation(id: number) {
  if (confirm('Are you sure you want to delete this record?')) {
    router.delete(route('fresignations.destroy', id));
  }
}
</script>

<template>
  <Head title="Fresignations" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-4">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Fresignations</h1>
        <Link
          v-if="can('fresignations.create')"
          :href="route('fresignations.create')"
          class="btn-primary"
        >
          Create
        </Link>
      </div>

      <div class="overflow-x-auto rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-400">
            <tr>
              <th class="px-4 py-3">Date</th>
              <th class="px-4 py-3">City</th>
              <th class="px-4 py-3">Empresa</th>
              <th class="px-4 py-3">Position</th>
              <th class="px-4 py-3">Start Date</th>
              <th class="px-4 py-3">End Date</th>
              <th class="px-4 py-3">Reason</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in fresignations"
              :key="item.id"
              class="border-b odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800"
            >
              <td class="px-4 py-2">{{ item.resignation_date }}</td>
              <td class="px-4 py-2">{{ item.city }}</td>
              <td class="px-4 py-2">{{ item.company_name }}</td>
              <td class="px-4 py-2">{{ item.position }}</td>
              <td class="px-4 py-2">{{ item.start_date }}</td>
              <td class="px-4 py-2">{{ item.end_date }}</td>
              <td class="px-4 py-2">{{ item.reason }}</td>
              <td class="px-4 py-2 text-center space-x-2">
                <Link
                  :href="route('fresignations.edit', item.id)"
                  v-if="can('fresignations.edit')"
                  class="btn-primary"
                >
                  Edit
                </Link>
                <button
                  v-if="can('fresignations.delete')"
                  @click="deleteFresignation(item.id)"
                  class="btn-danger"
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