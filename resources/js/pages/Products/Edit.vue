<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  product: {
    id: number;
    name: string;
    sku: string;
    stock: number;
    price: number;
    category_id: number;
    supplier_id: number | null;
    image: string | null;
  };
  categories: { id: number; name: string }[];
  suppliers: { id: number; name: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Productos', href: route('products.index') },
  { title: 'Editar Producto', href: route('products.edit', props.product.id) },
];

const form = useForm({
  _method: 'PUT', // Importante para la subida de archivos
  name: props.product.name,
  sku: props.product.sku,
  category_id: props.product.category_id,
  supplier_id: props.product.supplier_id,
  stock: props.product.stock,
  price: props.product.price,
  image: null as File | null,
});

function submit() {
  form.post(route('products.update', props.product.id), {
    forceFormData: true,
  });
}
</script>

<template>
  <Head title="Editar Producto" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto bg-white dark:bg-gray-900 rounded-lg shadow-md">
      <h1 class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 dark:text-white">Editar Producto</h1>

      <form @submit.prevent="submit" class="space-y-6">
        
        <div>
          <Label for="name">Nombre del Producto</Label>
          <Input id="name" v-model="form.name" type="text" autofocus />
          <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <div>
          <Label for="sku">SKU (Código de producto)</Label>
          <Input id="sku" v-model="form.sku" type="text" />
          <p v-if="form.errors.sku" class="text-sm text-red-600 mt-1">{{ form.errors.sku }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <Label for="category_id">Categoría</Label>
                <select id="category_id" v-model="form.category_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option :value="null" disabled>-- Seleccione --</option>
                    <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <p v-if="form.errors.category_id" class="text-sm text-red-600 mt-1">{{ form.errors.category_id }}</p>
            </div>
            <div>
                <Label for="supplier_id">Proveedor</Label>
                <select id="supplier_id" v-model="form.supplier_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option :value="null">-- Opcional --</option>
                    <option v-for="sup in props.suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
                </select>
                <p v-if="form.errors.supplier_id" class="text-sm text-red-600 mt-1">{{ form.errors.supplier_id }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <Label for="price">Precio de Venta</Label>
                <Input id="price" v-model="form.price" type="number" min="0" step="0.01" />
                <p v-if="form.errors.price" class="text-sm text-red-600 mt-1">{{ form.errors.price }}</p>
            </div>
            <div>
                <Label for="stock">Stock</Label>
                <Input id="stock" v-model="form.stock" type="number" min="0" />
                <p v-if="form.errors.stock" class="text-sm text-red-600 mt-1">{{ form.errors.stock }}</p>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4 pt-4">
          <Button variant="ghost" as-child>
            <Link :href="route('products.index')">Cancelar</Link>
          </Button>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Actualizando...</span>
            <span v-else>Actualizar Producto</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>