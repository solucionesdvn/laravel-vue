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
import { CirclePlus, Pencil, Trash, Search, Eye } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Plantillas de Documentos', href: '/document-templates' }
]

const props = defineProps<{
  templates: {
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
  searchForm.get(route('document-templates.index'), {
    preserveScroll: true,
    preserveState: true
  })
}

function deleteTemplate(id: number) {
  if (confirm('¿Estás seguro de eliminar esta plantilla?')) {
    router.delete(route('document-templates.destroy', id), {
      preserveScroll: true
    })
  }
}

</script>

<template>
  <Head title="Plantillas de Documentos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Lista de Plantillas</h1>
        <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
          <Link :href="route('document-templates.create')">
            <CirclePlus class="mr-1" /> Nueva Plantilla
          </Link>
        </Button>
      </div>

      <!-- Buscador -->
      <div class="w-full md:w-1/3">
        <form @submit.prevent="submitSearch" class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchForm.search"
            placeholder="Buscar plantilla..."
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
          <TableCaption>Tabla de plantillas de documentos</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">Descripción</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="template in templates.data" :key="template.id">
              <TableCell class="px-6 py-4">{{ template.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ template.description }}</TableCell>
              <TableCell class="px-6 py-4 flex gap-2">
                <Button as-child size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700">
                  <Link :href="route('document-templates.show', template.id)">
                    <Eye />
                  </Link>
                </Button>
                <Button as-child size="sm" class="bg-blue-500 text-white hover:bg-blue-700">
                  <Link :href="route('document-templates.edit', template.id)">
                    <Pencil />
                  </Link>
                </Button>
                <Button size="sm" class="bg-rose-500 text-white hover:bg-rose-700" @click="deleteTemplate(template.id)">
                  <Trash />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2">
          <template v-for="(link, index) in templates.links" :key="index">
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

        <div v-if="templates.data.length === 0" class="text-center text-gray-500 py-6">
          No hay plantillas registradas.
        </div>
      </div>

    </div>
  </AppLayout>
</template>