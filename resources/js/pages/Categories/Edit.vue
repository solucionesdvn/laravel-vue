<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Categorías',
    href: '/categories',
  },
  {
    title: 'Editar',
    href: '#',
  },
];

defineProps<{
  category: {
    id: number;
    name: string;
    description: string | null;
  };
}>();

const form = useForm({
  name: $props.category.name,
  description: $props.category.description ?? '',
});
</script>

<template>
  <Head title="Editar categoría" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="overflow-x-auto p-3">
      <Link
        :href="route('categories.index')"
        class="cursor-pointer px-3 py-2 text-xs font-medium text-white bg-blue-700 rounded-lg"
      >
        Volver
      </Link>

      <form
        @submit.prevent="form.put(route('categories.update', category.id))"
        class="space-y-6 mt-4 max-w-md mx-auto"
      >
        <!-- Nombre -->
        <div class="grid gap-2">
          <label for="name" class="text-sm font-medium text-gray-700">Nombre:</label>
          <input
            v-model="form.name"
            type="text"
            id="name"
            name="name"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500"
          />
          <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Descripción -->
        <div class="grid gap-2">
          <label for="description" class="text-sm font-medium text-gray-700">Descripción:</label>
          <textarea
            v-model="form.description"
            id="description"
            name="description"
            rows="4"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500"
          ></textarea>
          <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
        </div>

        <!-- Botón -->
        <div class="text-right">
          <button
            type="submit"
            :disabled="form.processing"
            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition"
          >
            Actualizar
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
