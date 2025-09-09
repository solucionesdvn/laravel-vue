<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { can } from '@/lib/can'
import { type BreadcrumbItem } from '@/types'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { CirclePlus, Search, Eye } from 'lucide-vue-next'
import { format } from 'date-fns';

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

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Salidas', href: route('product-exits.index') },
]

function submitSearch() {
  searchForm.get(route('product-exits.index'), {
    preserveScroll: true,
    preserveState: true
  })
}

const formatDate = (dateString: string) => {
    return format(new Date(dateString), 'dd/MM/yyyy HH:mm');
};

const formatCurrency = (value: number | string | null) => {
    const numberValue = Number(value || 0);
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(numberValue);
};

</script>

<template>
  <Head title="Salidas de Productos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Salidas de Productos</h1>
        <Button as-child size="sm" v-if="can('product-exits.create')" class="bg-indigo-500 text-white hover:bg-indigo-700">
          <Link :href="route('product-exits.create')">
            <CirclePlus class="mr-1" /> Nueva Salida
          </Link>
        </Button>
      </div>

      <div class="w-full md:w-1/3">
        <form @submit.prevent="submitSearch" class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchForm.search"
            placeholder="Buscar por motivo o nota..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
          />
          <Button type="submit" variant="outline">
            <Search class="w-4 h-4" />
          </Button>
        </form>
      </div>

      <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <Table>
          <TableCaption>Historial de salidas de productos</TableCaption>
          <TableHeader>
            <TableRow>
              <TableHead>Fecha</TableHead>
              <TableHead>Motivo</TableHead>
              <TableHead>Registrado por</TableHead>
              <TableHead class="text-right">Valor Total</TableHead>
              <TableHead class="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="productExits.data.length === 0">
                <TableCell :colspan="5" class="text-center py-6 text-gray-500">
                    No hay salidas registradas.
                </TableCell>
            </TableRow>
            <TableRow v-for="exit in productExits.data" :key="exit.id">
              <TableCell>{{ formatDate(exit.date) }}</TableCell>
              <TableCell>{{ exit.reason }}</TableCell>
              <TableCell>{{ exit.user.name }}</TableCell>
              <TableCell class="text-right">{{ formatCurrency(exit.total) }}</TableCell>
              <TableCell class="text-right">
                <Button as-child size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700" v-if="can('product-exits.view')">
                  <Link :href="route('product-exits.show', exit.id)">
                    <Eye class="h-4 w-4" />
                  </Link>
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <div class="mt-4 flex justify-center space-x-2 p-4">
          <template v-for="(link, index) in productExits.links" :key="index">
            <button
              v-if="link.url"
              :class="[ 'px-3 py-1 rounded text-sm',
                link.active
                  ? 'bg-indigo-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              ]"
              @click="router.visit(link.url, { preserveScroll: true })"
              v-html="link.label"
            ></button>
             <span
                v-else
                class="px-3 py-1 rounded text-sm text-gray-400 cursor-not-allowed"
                v-html="link.label"
            />
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>