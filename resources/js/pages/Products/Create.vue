<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  categories: { id: number; name: string }[];
  suppliers: { id: number; name: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Productos', href: route('products.index') },
  { title: 'Crear Producto', href: null },
];

const form = useForm({
  sku: '',
  name: '',
  category_id: null,
  supplier_id: null,
  stock: 0,
  price: '',
  image: null,
});

const previewUrl = ref<string | null>(null);

function handleImageChange(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (file) {
    form.image = file;
    previewUrl.value = URL.createObjectURL(file);
  } else {
    form.image = null;
    previewUrl.value = null;
  }
}

function submit() {
  form.post(route('products.store'), {
    forceFormData: true,
  });
}
</script>

<template>
  <Head title="Crear Producto" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Crear Producto</h1>

      <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
        <!-- SKU -->
        <div>
          <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-200">SKU</label>
          <input
            id="sku"
            v-model="form.sku"
            type="text"
            placeholder="Ej. PROD-001"
            autofocus
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
            placeholder="Ej. Lápiz HB"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Categoría -->
        <div>
          <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Categoría</label>
          <select
            id="category_id"
            v-model="form.category_id"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          >
            <option disabled value="">Selecciona un proveedor</option>
            <option v-for="prov in props.suppliers" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
          </select>
          <p v-if="form.errors.supplier_id" class="text-sm text-red-600 mt-1">{{ form.errors.supplier_id }}</p>
        </div>

        <!-- Stock -->
        <div>
          <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Stock</label>
          <input
            id="stock"
            v-model="form.stock"
            type="number"
            min="0"
            placeholder="Ej. 50"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.stock" class="text-sm text-red-600 mt-1">{{ form.errors.stock }}</p>
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
            placeholder="Ej. 1500.00"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.price" class="text-sm text-red-600 mt-1">{{ form.errors.price }}</p>
        </div>

        <!-- Imagen -->
        <div>
          <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Imagen</label>
          <input
            id="image"
            type="file"
            accept="image/*"
            @change="handleImageChange"
            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
              file:rounded-lg file:border-0
              file:text-sm file:font-semibold
              file:bg-indigo-50 file:text-indigo-700
              hover:file:bg-indigo-100"
          />
          <p v-if="form.errors.image" class="text-sm text-red-600 mt-1">{{ form.errors.image }}</p>

          <!-- Vista previa -->
          <div v-if="previewUrl" class="mt-2">
            <img :src="previewUrl" alt="Vista previa" class="w-24 h-24 object-cover rounded border" />
          </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4">
          <Link :href="route('products.index')" class="text-gray-600 hover:underline">Cancelar</Link>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Guardando...</span>
            <span v-else>Guardar</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>