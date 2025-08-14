<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
  client: {
    id: number;
    name: string;
    email: string;
    phone: string;
    identification: string | null;
    address: string;
  };
}>();

const form = useForm({
  name: props.client.name,
  email: props.client.email,
  phone: props.client.phone,
  identification: props.client.identification ?? '',
  address: props.client.address,
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Clientes', href: route('clients.index') },
  { title: 'Editar Cliente', href: route('clients.edit', props.client.id) },
];

function submit() {
  form.put(route('clients.update', props.client.id));
}
</script>

<template>
  <Head title="Editar Cliente" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
      <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <CardHeader>
          <CardTitle class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 dark:text-white">Editar Cliente</CardTitle>
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
                placeholder="Nombre completo del cliente"
                :class="{ 'border-red-500': form.errors.name }"
              />
              <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
            </div>

            <!-- Email -->
            <div>
              <Label for="email">Correo Electrónico</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="cliente@correo.com"
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
                placeholder="300 123 4567"
                :class="{ 'border-red-500': form.errors.phone }"
              />
              <p v-if="form.errors.phone" class="text-sm text-red-600 mt-1">{{ form.errors.phone }}</p>
            </div>

            <!-- Identificación -->
            <div>
              <Label for="identification">Identificación</Label>
              <Input
                id="identification"
                v-model="form.identification"
                type="text"
                placeholder="C.C. 123456789 / NIT 900123456-7"
                :class="{ 'border-red-500': form.errors.identification }"
              />
              <p v-if="form.errors.identification" class="text-sm text-red-600 mt-1">{{ form.errors.identification }}</p>
            </div>

            <!-- Dirección -->
            <div>
              <Label for="address">Dirección</Label>
              <Input
                id="address"
                v-model="form.address"
                type="text"
                placeholder="Calle 123 #45-67, Ciudad"
                :class="{ 'border-red-500': form.errors.address }"
              />
              <p v-if="form.errors.address" class="text-sm text-red-600 mt-1">{{ form.errors.address }}</p>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4 pt-4">
              <Button variant="ghost" as-child>
                <Link :href="route('clients.index')">Cancelar</Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                <span v-if="form.processing">Actualizando...</span>
                <span v-else>Actualizar Cliente</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>