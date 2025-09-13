<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  categories: { id: number; name: string }[];
  suppliers: { id: number; name: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Productos', href: route('products.index') },
  { title: 'Crear Producto', href: route('products.create') },
];

const form = useForm({
  name: '',
  sku: '',
  category_id: null,
  supplier_id: null,
  stock: 0,
  price: '',
  cost_price: '',
  // image: null,
});

// const previewUrl = ref<string | null>(null);

// function handleImageChange(event: Event) {
//   const target = event.target as HTMLInputElement;
//   const file = target.files?.[0];

//   if (file) {
//     form.image = file;
//     previewUrl.value = URL.createObjectURL(file);
//   } else {
//     form.image = null;
//     previewUrl.value = null;
//   }
// }

function submit() {
  form.post(route('products.store'), {
    // forceFormData: true,
  });
}
</script>

<template>
  <Head title="Crear Producto" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
      <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <CardHeader>
          <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Crear Nuevo Producto</CardTitle>
        </CardHeader>
        <CardContent>
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

            <div>
                <Label for="category_id">Categoría</Label>
                <select id="category_id" v-model="form.category_id" class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                    <option :value="null" disabled>-- Seleccione --</option>
                    <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <p v-if="form.errors.category_id" class="text-sm text-red-600 mt-1">{{ form.errors.category_id }}</p>
            </div>

            <div>
                <Label for="supplier_id">Proveedor</Label>
                <select id="supplier_id" v-model="form.supplier_id" class="mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                    <option :value="null">-- Opcional --</option>
                    <option v-for="sup in props.suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
                </select>
                <p v-if="form.errors.supplier_id" class="text-sm text-red-600 mt-1">{{ form.errors.supplier_id }}</p>
            </div>

            <div>
                <Label for="price">Precio de Venta</Label>
                <Input id="price" v-model="form.price" type="number" min="0" step="0.01" />
                <p v-if="form.errors.price" class="text-sm text-red-600 mt-1">{{ form.errors.price }}</p>
            </div>

            <div>
                <Label for="cost_price">Precio de Costo</Label>
                <Input id="cost_price" v-model="form.cost_price" type="number" min="0" step="0.01" />
                <p v-if="form.errors.cost_price" class="text-sm text-red-600 mt-1">{{ form.errors.cost_price }}</p>
            </div>

            <div>
                <Label for="stock">Stock Inicial</Label>
                <Input id="stock" v-model="form.stock" type="number" min="0" />
                <p v-if="form.errors.stock" class="text-sm text-red-600 mt-1">{{ form.errors.stock }}</p>
            </div>

            <!-- <div>
              <Label for="image">Imagen del Producto</Label>
              <Input id="image" type="file" @change="handleImageChange" class="mt-1 block w-full" />
              <p v-if="form.errors.image" class="text-sm text-red-600 mt-1">{{ form.errors.image }}</p>
              <img v-if="previewUrl" :src="previewUrl" class="mt-2 h-24 w-24 object-cover rounded-md" />
            </div> -->

            <div class="flex justify-end gap-4 pt-4">
              <Button variant="ghost" as-child>
                <Link :href="route('products.index')">Cancelar</Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <span v-if="form.processing">Guardando...</span>
                <span v-else>Guardar Producto</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>