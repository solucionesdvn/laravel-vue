<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { can } from '@/lib/can'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  registers: Array<{
    id: number
    opened_at: string
    closed_at: string | null
    initial_amount: number
    final_amount: number | null
    user: { name: string }
  }>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Caja', href: '/cash-registers' },
]
</script>

<template>
  <Head title="Cajas" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Historial de Cajas</h1>
        <Link
          v-if="can('cash-registers.create')"
          :href="route('cash-registers.create')"
          class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg shadow transition-colors"
        >
          + Abrir Caja
        </Link>
      </div>

      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Apertura</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Cierre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Inicial</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Final</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="register in props.registers" :key="register.id">
              <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">{{ register.opened_at }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                <span v-if="register.closed_at">{{ register.closed_at }}</span>
                <span v-else class="text-yellow-500 font-semibold">Abierta</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ register.user.name }}</td>
              <td class="px-6 py-4 text-sm text-right text-gray-800 dark:text-white">
                $ {{ register.initial_amount.toFixed(2) }}
              </td>
              <td class="px-6 py-4 text-sm text-right text-gray-800 dark:text-white">
                <span v-if="register.final_amount !== null">$ {{ register.final_amount.toFixed(2) }}</span>
                <span v-else class="text-yellow-500 font-semibold">Pendiente</span>
              </td>
            </tr>
            <tr v-if="props.registers.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Sin registros.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>