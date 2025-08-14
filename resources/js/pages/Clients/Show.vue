<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Clientes', href: '/clients' },
  { title: 'Detalle', href: '#' }
];

const props = defineProps<{
  client: {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    identification: string | null;
    address: string | null;
    created_at: string;
  };
}>();

const formatDate = (value: string) => {
  return new Date(value).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};
</script>

<template>
  <Head :title="`Cliente #${client.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header y botón de volver -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Detalle de Cliente #{{ client.id }}</h1>
        <div>
          <Button as-child size="sm" variant="outline">
            <Link :href="route('clients.index')">
              <ArrowLeft class="mr-1" /> Volver a Clientes
            </Link>
          </Button>
        </div>
      </div>

      <!-- Detalles del Cliente -->
      <div class="border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-gray-600 dark:text-gray-400">Nombre:</p>
            <p class="font-semibold">{{ client.name }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Email:</p>
            <p class="font-semibold">{{ client.email }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Teléfono:</p>
            <p class="font-semibold">{{ client.phone || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Identificación:</p>
            <p class="font-semibold">{{ client.identification || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Dirección:</p>
            <p class="font-semibold">{{ client.address || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-gray-600 dark:text-gray-400">Fecha de Creación:</p>
            <p class="font-semibold">{{ formatDate(client.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
