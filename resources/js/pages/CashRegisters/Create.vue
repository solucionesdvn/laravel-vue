<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cajas', href: '/cash-registers' },
  { title: 'Abrir Caja', href: '/cash-registers/create' },
];

// Formulario
const form = useForm({
  opening_amount: '',
  notes: '',
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Abrir Caja" />

    <div class="max-w-2xl mx-auto p-6 bg-white shadow rounded-xl space-y-6">
      <h1 class="text-2xl font-bold text-gray-800">Abrir Nueva Caja</h1>

      <form @submit.prevent="form.post(route('cash-registers.store'))" class="space-y-5">
        <!-- Monto Inicial -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Monto Inicial</label>
          <input
            type="number"
            step="0.01"
            v-model="form.opening_amount"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-400"
            :class="{ 'border-red-500': form.errors.opening_amount }"
          />
          <p v-if="form.errors.opening_amount" class="text-sm text-red-600 mt-1">
            {{ form.errors.opening_amount }}
          </p>
        </div>

        <!-- Notas -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
          <textarea
            v-model="form.notes"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-400"
            :class="{ 'border-red-500': form.errors.notes }"
          ></textarea>
          <p v-if="form.errors.notes" class="text-sm text-red-600 mt-1">
            {{ form.errors.notes }}
          </p>
        </div>

        <!-- Botón -->
        <div class="flex justify-end">
          <Button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">
            Abrir Caja
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>