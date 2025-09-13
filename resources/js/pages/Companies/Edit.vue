<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  company: {
    id: number;
    name: string;
    nit: string;
    address: string;
    phone: string;
    currency: string;
    locale: string;
  };
}>();

const form = useForm({
  name: props.company.name,
  nit: props.company.nit,
  address: props.company.address,
  phone: props.company.phone,
  currency: props.company.currency,
  locale: props.company.locale,
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Empresas', href: route('companies.index') },
  { title: 'Editar Empresa', href: route('companies.edit', props.company.id) },
];

function submit() {
  form.put(route('companies.update', props.company.id));
}
</script>

<template>
  <Head title="Editar Empresa" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto bg-white dark:bg-gray-900 rounded-lg shadow-md">
      <h1 class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 dark:text-white">Editar Empresa</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Nombre -->
        <div>
          <Label for="name">Nombre de la Empresa</Label>
          <Input
            id="name"
            v-model="form.name"
            type="text"
            autofocus
            placeholder="Mi Empresa S.A.S."
            :class="{ 'border-red-500': form.errors.name }"
          />
          <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- NIT -->
        <div>
          <Label for="nit">NIT</Label>
          <Input
            id="nit"
            v-model="form.nit"
            type="text"
            placeholder="900.123.456-7"
            :class="{ 'border-red-500': form.errors.nit }"
          />
          <p v-if="form.errors.nit" class="text-sm text-red-600 mt-1">{{ form.errors.nit }}</p>
        </div>

        <!-- Dirección -->
        <div>
          <Label for="address">Dirección</Label>
          <Input
            id="address"
            v-model="form.address"
            type="text"
            placeholder="Carrera 1 #2-3, Bogotá"
            :class="{ 'border-red-500': form.errors.address }"
          />
          <p v-if="form.errors.address" class="text-sm text-red-600 mt-1">{{ form.errors.address }}</p>
        </div>

        <!-- Teléfono -->
        <div>
          <Label for="phone">Teléfono</Label>
          <Input
            id="phone"
            v-model="form.phone"
            type="text"
            placeholder="300 123 4567"
            :class="{ 'border-red-500': form.errors.phone }"
          />
          <p v-if="form.errors.phone" class="text-sm text-red-600 mt-1">{{ form.errors.phone }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Moneda -->
          <div>
            <Label for="currency">Moneda</Label>
            <Input
              id="currency"
              v-model="form.currency"
              type="text"
              placeholder="COP"
              :class="{ 'border-red-500': form.errors.currency }"
            />
            <p v-if="form.errors.currency" class="text-sm text-red-600 mt-1">{{ form.errors.currency }}</p>
          </div>

          <!-- Locale -->
          <div>
            <Label for="locale">Configuración Regional (Locale)</Label>
            <Input
              id="locale"
              v-model="form.locale"
              type="text"
              placeholder="es-CO"
              :class="{ 'border-red-500': form.errors.locale }"
            />
            <p v-if="form.errors.locale" class="text-sm text-red-600 mt-1">{{ form.errors.locale }}</p>
          </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4 pt-4">
          <Button variant="ghost" as-child>
            <Link :href="route('companies.index')">Cancelar</Link>
          </Button>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Actualizando...</span>
            <span v-else>Actualizar Empresa</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>