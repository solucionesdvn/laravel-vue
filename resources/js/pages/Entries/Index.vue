<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { CirclePlus, Search } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Entradas', href: '/entries' }
]

const props = defineProps<{
  entries: {
    data: Array<any>
    links: Array<any>
    meta: Record<string, any>
  }
  filters: {
    search: string
  }
}>()

const searchForm = useForm({
  search: props.filters.search || ''
})

function submitSearch() {
  searchForm.get(route('entries.index'), {
    preserveScroll: true,
    preserveState: true
  })
}
</script>

<template>
  <Head title="Entradas" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Entradas de Productos</h1>
        <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
          <Link :href="route('entries.create')">
            <CirclePlus class="mr-1" /> Nueva entrada
          </Link>
        </Button>
      </div>

      <!-- Buscador -->
      <div class="w-full md:w-1/3">
        <form @submit.prevent="submitSearch" class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchForm.search"
            placeholder="Buscar proveedor o nota..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
          />
          <Button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700">
            <Search class="w-4 h-4" />
          </Button>
        </form>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <TableCaption>Historial de entradas</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Fecha</TableHead>
              <TableHead class="px-6 py-3">Proveedor</TableHead>
              <TableHead class="px-6 py-3">Notas</TableHead>
              <TableHead class="px-6 py-3">Total</TableHead>
              <TableHead class="px-6 py-3">Registrado por</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="entry in entries.data" :key="entry.id">
              <TableCell class="px-6 py-4">{{ entry.date }}</TableCell>
              <TableCell class="px-6 py-4">{{ entry.supplier?.name || 'N/A' }}</TableCell>
              <TableCell class="px-6 py-4">{{ entry.notes || '-' }}</TableCell>
              <TableCell class="px-6 py-4">$ {{ entry.total_cost }}</TableCell>
              <TableCell class="px-6 py-4">{{ entry.user?.name || 'Sistema' }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2">
          <template v-for="(link, index) in entries.links" :key="index">
            <button
              v-if="link.url"
              v-html="link.label"
              :class="[ 
                'px-3 py-1 rounded text-sm',
                link.active 
                  ? 'bg-indigo-600 text-white' 
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              ]"
              @click="router.visit(link.url, { preserveScroll: true })"
            ></button>
          </template>
        </div>

        <div v-if="entries.data.length === 0" class="text-center text-gray-500 py-6">
          No hay entradas registradas.
        </div>
      </div>
    </div>
  </AppLayout>
</template>