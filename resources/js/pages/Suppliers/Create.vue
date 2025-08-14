<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const form = useForm({
  name: '',
  contact_name: '',
  email: '',
  phone: '',
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
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
      <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <CardHeader>
          <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Crear Nuevo Proveedor</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Nombre -->
            <div>
              <Label for="name">Nombre</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                autofocus
                placeholder="Nombre de la empresa o proveedor"
                :class="{ 'border-red-500': form.errors.name }"
              />
              <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
            </div>

            <!-- Nombre de contacto -->
            <div>
              <Label for="contact_name">Nombre de Contacto</Label>
              <Input
                id="contact_name"
                v-model="form.contact_name"
                type="text"
                placeholder="Ej. Juan Pérez"
                :class="{ 'border-red-500': form.errors.contact_name }"
              />
              <p v-if="form.errors.contact_name" class="text-sm text-red-600 mt-1">{{ form.errors.contact_name }}</p>
            </div>

            <!-- Email -->
            <div>
              <Label for="email">Correo Electrónico</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="proveedor@correo.com"
                :class="{ 'border-red-500': form.errors.email }"
              />
              <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</p>
            </div>

            <!-- Teléfono -->
            <div>
              <Label for="phone">Teléfono</Label>
              <Input
                id="phone"
                v-model="form.phone"
                type="text"
                placeholder="Ej. +57 300 123 4567"
                :class="{ 'border-red-500': form.errors.phone }"
              />
              <p v-if="form.errors.phone" class="text-sm text-red-600 mt-1">{{ form.errors.phone }}</p>
            </div>

            <!-- Dirección -->
            <div>
              <Label for="address">Dirección</Label>
              <Input
                id="address"
                v-model="form.address"
                type="text"
                placeholder="Calle 123 #45-67"
                :class="{ 'border-red-500': form.errors.address }"
              />
              <p v-if="form.errors.address" class="text-sm text-red-600 mt-1">{{ form.errors.address }}</p>
            </div>

            <!-- NIT -->
            <div>
              <Label for="nit">NIT</Label>
              <Input
                id="nit"
                v-model="form.nit"
                type="text"
                placeholder="900123456-7"
                :class="{ 'border-red-500': form.errors.nit }"
              />
              <p v-if="form.errors.nit" class="text-sm text-red-600 mt-1">{{ form.errors.nit }}</p>
            </div>

            <!-- Notas -->
            <div>
              <Label for="notes">Notas</Label>
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
            <div class="flex justify-end gap-4 pt-4">
              <Button variant="ghost" as-child>
                <Link :href="route('suppliers.index')">Cancelar</Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <span v-if="form.processing">Guardando...</span>
                <span v-else>Guardar</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>