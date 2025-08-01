<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { can } from '@/lib/can'
import { type BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Ventas', href: '/sales' }
]
</script>

<template>
  <Head title="Ventas" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Ventas</h1>
        <Link
          v-if="can('sales.create')"
          :href="route('sales.create')"
          class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition-colors"
        >
          + Nueva Venta
        </Link>
      </div>

      <div class="text-gray-600 dark:text-gray-300">
        Aún no se ha implementado el listado de ventas. Usa el botón para registrar una nueva venta.
      </div>
    </div>
  </AppLayout>
</template>
