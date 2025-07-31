<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { can } from '@/lib/can'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  productExits: {
    data: Array<{
      id: number
      date: string
      reason: string
      notes: string
      total: number | string | null
      user: { name: string }
    }>
  }
  filters: {
    search: string
  }
}>()

const search = ref(props.filters.search || '')

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Salidas', href: '/product-exits' },
]

function performSearch() {
  router.get(route('product-exits.index'), { search: search.value }, {
    preserveScroll: true,
    preserveState: true
  })
}
</script>

<template>
  <Head title="Salidas de Productos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Salidas de Productos</h1>
        <Link
          v-if="can('product-exits.create')"
          :href="route('product-exits.create')"
          class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition-colors"
        >
          + Nueva salida
        </Link>
      </div>

      <div class="flex items-center gap-4 flex-wrap">
        <!-- Si aún no implementas SearchInput, comenta esta línea -->
        <!-- <SearchInput v-model="search" @search="performSearch" placeholder="Buscar por motivo o nota..." /> -->
      </div>

      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado por</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="exit in props.productExits.data" :key="exit.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">{{ exit.date }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ exit.reason }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 truncate max-w-[200px]">{{ exit.notes }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ exit.user.name }}</td>
              <td class="px-6 py-4 text-sm text-right text-gray-800 dark:text-white">
                $ {{ Number(exit.total || 0).toFixed(2) }}
              </td>
            </tr>
            <tr v-if="props.productExits.data.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Sin resultados.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>