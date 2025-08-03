<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  register: {
    id: number;
    opening_amount: number;
    total_sales: number;
    total_expenses: number;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cajas', href: '/cash-registers' },
  { title: 'Cerrar Caja', href: `/cash-registers/${props.register.id}/close` },
];

// Formulario
const form = useForm({
  closing_amount: '',
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Cerrar Caja" />

    <div class="max-w-md mx-auto p-6 bg-white shadow rounded-xl space-y-6">
      <h1 class="text-2xl font-bold text-gray-800">Cerrar Caja</h1>

      <form
        @submit.prevent="form.put(`/cash-registers/${props.register.id}`)"
        class="space-y-4"
      >
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Monto Final (opcional)
          </label>
          <input
            type="number"
            step="0.01"
            v-model="form.closing_amount"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-400"
            :class="{ 'border-red-500': form.errors.closing_amount }"
          />
          <p v-if="form.errors.closing_amount" class="text-sm text-red-600 mt-1">
            {{ form.errors.closing_amount }}
          </p>
        </div>

        <div class="flex justify-end">
          <Button type="submit" class="bg-emerald-600 text-white hover:bg-emerald-700">
            Confirmar Cierre
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>