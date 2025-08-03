<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { can } from '@/lib/can'
import { Button } from '@/components/ui/button'

const props = defineProps<{
  registers: Array<{
    id: number
    user?: { name: string }
    opened_at: string
    closed_at?: string
    opening_amount: number
    closing_amount?: number
    total_sales: number
    total_expenses: number
    notes?: string
  }>
}>()

const page = usePage()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cajas', href: '/cash-registers' },
]

const formatDate = (date: string) =>
  new Date(date).toLocaleString('es-CO', {
    dateStyle: 'short',
    timeStyle: 'short',
  })

const formatMoney = (amount: number) =>
  new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 2,
  }).format(amount)
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Historial de Cajas" />

    <div class="p-6 space-y-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Historial de Cajas</h1>
        <div>
          <Link
            v-if="can('cash-registers.create')"
            href="/cash-registers/create"
            class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium"
          >
            Abrir Caja
          </Link>
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 uppercase text-xs">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Usuario</th>
              <th class="px-4 py-3 text-left">Abierta</th>
              <th class="px-4 py-3 text-left">Cerrada</th>
              <th class="px-4 py-3 text-left">Monto Inicial</th>
              <th class="px-4 py-3 text-left">Monto Final</th>
              <th class="px-4 py-3 text-left">Ventas</th>
              <th class="px-4 py-3 text-left">Egresos</th>
              <th class="px-4 py-3 text-left">Notas</th>
              <th class="px-4 py-3 text-left">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="register in props.registers"
              :key="register.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-800"
            >
              <td class="px-4 py-2">{{ register.id }}</td>
              <td class="px-4 py-2">{{ register.user?.name ?? '—' }}</td>
              <td class="px-4 py-2">{{ formatDate(register.opened_at) }}</td>
              <td class="px-4 py-2">
                {{ register.closed_at ? formatDate(register.closed_at) : '—' }}
              </td>
              <td class="px-4 py-2">{{ formatMoney(register.opening_amount) }}</td>
              <td class="px-4 py-2">
                {{ register.closing_amount ? formatMoney(register.closing_amount) : '—' }}
              </td>
              <td class="px-4 py-2">{{ formatMoney(register.total_sales) }}</td>
              <td class="px-4 py-2">{{ formatMoney(register.total_expenses) }}</td>
              <td class="px-4 py-2 whitespace-pre-wrap max-w-xs text-gray-600 dark:text-gray-300">
                {{ register.notes ?? '—' }}
              </td>

              <td class="px-4 py-2">
                <Link
                  v-if="!register.closed_at && can('cash-registers.close')"
                  :href="`/cash-registers/${register.id}/close`"
                  class="inline-flex items-center bg-emerald-600 text-white px-3 py-1.5 rounded hover:bg-emerald-700 text-xs font-medium"
                >
                  Cerrar Caja
                </Link>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Sin registros -->
        <div
          v-if="props.registers.length === 0"
          class="text-center text-gray-500 dark:text-gray-400 py-6"
        >
          No hay registros de cajas aún.
        </div>
      </div>
    </div>
  </AppLayout>
</template>