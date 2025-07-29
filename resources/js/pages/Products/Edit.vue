<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  product: {
    id: number;
    sku: string;
    name: string;
    category_id: number;
    supplier_id: number;
    price: number;
    image: string | null;
  };
  categories: { id: number; name: string }[];
  suppliers: { id: number; name: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Productos', href: route('products.index') },
  { title: 'Editar Producto', href: null },
];

const form = useForm({
  sku: props.product.sku ?? '',
  name: props.product.name ?? '',
  category_id: props.product.category_id ?? '',
  supplier_id: props.product.supplier_id ?? '',
  price: props.product.price ?? '',
});

function submit() {
  form.put(route('products.update', props.product.id));
}
</script>

<template>
  <Head title="Editar Producto" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Editar Producto</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- SKU -->
        <div>
          <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-200">SKU</label>
          <input
            id="sku"
            v-model="form.sku"
            type="text"
            class="mt-1 block w-full rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.sku" class="text-sm text-red-600 mt-1">{{ form.errors.sku }}</p>
        </div>

        <!-- Nombre -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="mt-1 block w-full rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Categoría -->
        <div>
          <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Categoría</label>
          <select
            id="category_id"
            v-model="form.category_id"
            class="mt-1 block w-full rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          >
            <option disabled value="">Selecciona una categoría</option>
            <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <p v-if="form.errors.category_id" class="text-sm text-red-600 mt-1">{{ form.errors.category_id }}</p>
        </div>

        <!-- Proveedor -->
        <div>
          <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Proveedor</label>
          <select
            id="supplier_id"
            v-model="form.supplier_id"
            class="mt-1 block w-full rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          >
            <option disabled value="">Selecciona un proveedor</option>
            <option v-for="prov in props.suppliers" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
          </select>
          <p v-if="form.errors.supplier_id" class="text-sm text-red-600 mt-1">{{ form.errors.supplier_id }}</p>
        </div>

        <!-- Precio -->
        <div>
          <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Precio</label>
          <input
            id="price"
            v-model="form.price"
            type="number"
            min="0"
            step="0.01"
            class="mt-1 block w-full rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.price" class="text-sm text-red-600 mt-1">{{ form.errors.price }}</p>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4">
          <Link :href="route('products.index')" class="text-gray-600 hover:underline">Cancelar</Link>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Actualizando...</span>
            <span v-else>Actualizar</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
