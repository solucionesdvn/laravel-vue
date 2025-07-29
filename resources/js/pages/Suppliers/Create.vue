<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

const form = useForm({
  name: '',
  contact_name: '',
  email: '',
  address: '',
  nit: '',
  notes: '',
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Proveedores', href: route('suppliers.index') },
  { title: 'Crear Proveedor', href: null },
];

function submit() {
  form.post(route('suppliers.store'));
}
</script>

<template>
  <Head title="Crear Proveedor" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Crear Proveedor</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Nombre -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            autofocus
            placeholder="Nombre de la empresa o proveedor"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Nombre de contacto -->
        <div>
          <label for="contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre de Contacto</label>
          <input
            id="contact_name"
            v-model="form.contact_name"
            type="text"
            placeholder="Ej. Juan Pérez"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.contact_name" class="text-sm text-red-600 mt-1">{{ form.errors.contact_name }}</p>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Correo Electrónico</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="proveedor@correo.com"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</p>
        </div>

        <!-- Dirección -->
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Dirección</label>
          <input
            id="address"
            v-model="form.address"
            type="text"
            placeholder="Calle 123 #45-67"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.address" class="text-sm text-red-600 mt-1">{{ form.errors.address }}</p>
        </div>

        <!-- NIT -->
        <div>
          <label for="nit" class="block text-sm font-medium text-gray-700 dark:text-gray-200">NIT</label>
          <input
            id="nit"
            v-model="form.nit"
            type="text"
            placeholder="900123456-7"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
          />
          <p v-if="form.errors.nit" class="text-sm text-red-600 mt-1">{{ form.errors.nit }}</p>
        </div>

        <!-- Notas -->
        <div>
          <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notas</label>
          <textarea
            id="notes"
            v-model="form.notes"
            rows="4"
            placeholder="Observaciones o detalles adicionales del proveedor"
            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 resize-none"
          ></textarea>
          <p v-if="form.errors.notes" class="text-sm text-red-600 mt-1">{{ form.errors.notes }}</p>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4">
          <Link :href="route('suppliers.index')" class="text-gray-600 hover:underline">Cancelar</Link>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Guardando...</span>
            <span v-else>Guardar</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>