<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { ArrowLeft } from 'lucide-vue-next'
import { format } from 'date-fns'

const props = defineProps<{
  product: {
    id: number
    name: string
    stock: number
    sku?: string
  }
  transactions: {
    data: any[]
    links: any[]
    total: number
    from: number | null
    to: number | null
  }
  initialStock: number
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Productos', href: route('products.index') },
  { title: 'Kardex' },
]

const formatDate = (dateString: string) => {
  if (!dateString) return ''
  return format(new Date(dateString), 'dd/MM/yyyy HH:mm')
}

const getTransactionUrl = (tx: any): string => {
  switch (tx.type) {
    case 'Entrada':
      return `/entries/${tx.reference_id}`
    case 'Venta':
      return `/sales/${tx.reference_id}`
    case 'Salida':
      return `/product-exits/${tx.reference_id}`
    default:
      return '#'
  }
}
</script>

<template>
  <Head :title="`Kardex de ${product.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Kardex: {{ product.name }}</h1>
          <p class="text-sm text-gray-500 mt-1">SKU: {{ product.sku || 'N/A' }}</p>
        </div>
        <Link
          :href="route('products.index')"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700"
        >
          <ArrowLeft class="w-4 h-4 mr-2" /> Volver a Productos
        </Link>
      </div>

      <!-- Resumen del producto -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Stock Inicial</h2>
          <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-white">{{ initialStock }}</p>
        </div>
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Stock Actual</h2>
          <p class="mt-1 text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ product.stock }}</p>
        </div>
      </div>

      <!-- Tabla de movimientos -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table>
          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Fecha</TableHead>
              <TableHead class="px-6 py-3">Tipo</TableHead>
              <TableHead class="px-6 py-3 text-right">Entrada</TableHead>
              <TableHead class="px-6 py-3 text-right">Salida</TableHead>
              <TableHead class="px-6 py-3 text-right">Saldo</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="tx in transactions.data" :key="`${tx.type}-${tx.reference_id}-${tx.date}`">
              <TableCell class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ formatDate(tx.date) }}</TableCell>
              <TableCell class="px-6 py-4">
                <Link :href="getTransactionUrl(tx)" class="transition-opacity hover:opacity-75">
                  <span
                    class="px-3 py-1 text-xs font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': tx.type === 'Entrada',
                      'bg-yellow-100 text-yellow-800': tx.type === 'Venta',
                      'bg-red-100 text-red-800': tx.type === 'Salida',
                      'bg-gray-100 text-gray-800': !['Entrada','Venta','Salida'].includes(tx.type)
                    }"
                  >
                    {{ tx.type }}
                  </span>
                </Link>
              </TableCell>
              <TableCell class="px-6 py-4 font-mono text-right text-green-600">{{ tx.quantity_in > 0 ? tx.quantity_in : '' }}</TableCell>
              <TableCell class="px-6 py-4 font-mono text-right text-red-600">{{ tx.quantity_out > 0 ? tx.quantity_out : '' }}</TableCell>
              <TableCell class="px-6 py-4 font-mono text-right font-bold text-gray-800 dark:text-white">{{ tx.balance }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <div v-if="transactions.data.length === 0" class="text-center text-gray-500 py-10">
          No hay movimientos registrados para este producto.
        </div>
      </div>

      <!-- Paginación -->
      <div v-if="transactions.links.length > 3" class="mt-4 flex justify-center space-x-2">
        <button
          v-for="(link, index) in transactions.links"
          :key="index"
          v-html="link.label"
          :class="[
            'px-3 py-1 rounded text-sm',
            link.active
              ? 'bg-indigo-600 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600',
            { 'cursor-not-allowed opacity-50': !link.url }
          ]"
          :disabled="!link.url"
          @click="link.url && router.visit(link.url, { preserveScroll: true })"
        />
      </div>
    </div>
  </AppLayout>
</template>