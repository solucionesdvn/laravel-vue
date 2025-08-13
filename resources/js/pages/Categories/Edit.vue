<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'; // Importar componentes Card

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
  { title: 'Editar Categoría', href: route('categories.edit', props.category.id) },
];

function submit() {
  form.put(route('categories.update', props.category.id));
}
</script>

<template>
  <Head title="Editar Categoría" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
      <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <CardHeader>
          <CardTitle class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 dark:text-white">Editar Categoría</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Nombre -->
            <div>
              <Label for="name">Nombre de la Categoría</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                autofocus
                placeholder="Ej. Electrónica"
                :class="{ 'border-red-500': form.errors.name }"
              />
              <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
            </div>

            <!-- Descripción -->
            <div>
              <Label for="description">Descripción</Label>
              <Input
                id="description"
                v-model="form.description"
                type="text"
                placeholder="Breve descripción de la categoría"
                :class="{ 'border-red-500': form.errors.description }"
              />
              <p v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</p>
            </div>

            <!-- Color -->
            <div>
              <Label for="color">Color</Label>
              <div class="flex items-center gap-2">
                <Input
                  id="color"
                  v-model="form.color"
                  type="color"
                  class="w-12 h-12 p-0 border-none cursor-pointer"
                  :class="{ 'border-red-500': form.errors.color }"
                />
                <Input
                  v-model="form.color"
                  type="text"
                  placeholder="#RRGGBB"
                  class="flex-1"
                  :class="{ 'border-red-500': form.errors.color }"
                />
              </div>
              <p v-if="form.errors.color" class="text-sm text-red-600 mt-1">{{ form.errors.color }}</p>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4 pt-4">
              <Button variant="ghost" as-child>
                <Link :href="route('categories.index')">Cancelar</Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <span v-if="form.processing">Actualizando...</span>
                <span v-else>Actualizar Categoría</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>