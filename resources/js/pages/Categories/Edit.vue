<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  category: {
    id: number;
    name: string;
    description: string | null;
    color: string;
  };
}>();

const form = useForm({
  name: props.category.name,
  description: props.category.description ?? '',
  color: props.category.color ?? '#000000',
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categorías', href: route('categories.index') },
  { title: 'Editar Categoría', href: null },
];

function submit() {
  form.put(route('categories.update', props.category.id));
}
</script>

<template>
  <Head title="Editar Categoría" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Editar Categoría</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Nombre -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            autofocus
            placeholder="Ej. Papelería"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Descripción -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción</label>
          <input
            id="description"
            v-model="form.description"
            type="text"
            placeholder="Breve descripción de la categoría"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</p>
        </div>

        <!-- Color -->
        <div>
          <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Color</label>
          <input
            id="color"
            v-model="form.color"
            type="color"
            class="mt-1 block w-20 h-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800"
          />
          <p v-if="form.errors.color" class="text-sm text-red-600 mt-1">{{ form.errors.color }}</p>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4">
          <Link :href="route('categories.index')" class="text-gray-600 hover:underline">Cancelar</Link>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Actualizando...</span>
            <span v-else>Actualizar</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
