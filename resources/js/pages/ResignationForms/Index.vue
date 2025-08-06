<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { can } from '@/lib/can'
import QrcodeVue from 'qrcode.vue'

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

import {
  Pencil,
  Trash,
  CirclePlus,
  Search,
  Link2,
  Printer,
  FileDown,
  X,
  Eye,
} from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cartas de Renuncia', href: '/resignation-forms' },
]

const props = defineProps<{
  forms: {
    data: Array<any>
    links: Array<any>
    meta: Record<string, any>
  }
  filters: {
    search: string
  }
}>()

const searchForm = useForm({
  search: props.filters?.search || '',
})

function submitSearch() {
  searchForm.get(route('resignation-forms.index'), {
    preserveScroll: true,
    preserveState: true,
  })
}

function deleteForm(id: number) {
  if (confirm('¿Deseas eliminar este registro?')) {
    router.delete(route('resignation-forms.destroy', id))
  }
}

const activeQR = ref<number | null>(null)
const origin = typeof window !== 'undefined' ? window.location.origin : ''

function copyPublicLink(token: string) {
  const url = `${origin}/public/resignation/${token}`
  navigator.clipboard.writeText(url)
  alert('Enlace copiado al portapapeles:\n' + url)
}

// 🌟 Modal Vista Previa PDF
const showPdfModal = ref(false)
const pdfUrl = ref('')

function openPdfPreview(token: string) {
  pdfUrl.value = `${origin}/public/resignation/pdf/${token}`
  showPdfModal.value = true
}
</script>

<template>
  <Head title="Cartas de Renuncia" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
          Lista de Cartas de Renuncia
        </h1>
        <div>
          <Button
            as-child
            size="default"
            class="bg-indigo-500 text-white hover:bg-indigo-700"
            v-if="can('resignation-forms.create')"
          >
            <Link :href="route('resignation-forms.create')">
              <CirclePlus class="mr-1 w-4 h-4" /> Nueva carta
            </Link>
          </Button>
        </div>
      </div>

      <!-- Buscador -->
      <div class="w-full md:w-1/3">
        <form @submit.prevent="submitSearch" class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchForm.search"
            placeholder="Buscar por nombre..."
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
          <TableCaption>Cartas de renuncia registradas</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">Fecha</TableHead>
              <TableHead class="px-6 py-3">Motivo</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="form in forms.data" :key="form.id">
              <TableCell class="px-6 py-4">{{ form.full_name }}</TableCell>
              <TableCell class="px-6 py-4">{{ form.resignation_date }}</TableCell>
              <TableCell class="px-6 py-4 truncate max-w-[200px]">{{ form.reason }}</TableCell>
              <TableCell class="px-6 py-4 flex flex-col gap-2">
                <div class="flex flex-wrap gap-2">
                  <Button
                    as-child
                    size="sm"
                    class="bg-blue-500 text-white hover:bg-blue-700"
                    v-if="can('resignation-forms.edit')"
                  >
                    <Link :href="`/resignation-forms/${form.id}/edit`">
                      <Pencil class="w-4 h-4" />
                    </Link>
                  </Button>
                  <Button
                    size="sm"
                    class="bg-rose-500 text-white hover:bg-rose-700"
                    v-if="can('resignation-forms.delete')"
                    @click="deleteForm(form.id)"
                  >
                    <Trash class="w-4 h-4" />
                  </Button>
                  <Button
                    size="sm"
                    class="bg-gray-500 text-white hover:bg-gray-700"
                    @click="activeQR === form.id ? activeQR = null : activeQR = form.id"
                  >
                    <Link2 class="w-4 h-4" />
                  </Button>
                </div>

                <!-- Acciones al compartir -->
                <div v-if="activeQR === form.id" class="mt-2">
                  <QrcodeVue
                    :value="`${origin}/public/resignation/${form.token}`"
                    :size="128"
                    level="M"
                  />
                  <div class="mt-2 flex gap-2 flex-wrap">
                    <Button
                      size="sm"
                      class="bg-gray-300 text-gray-800 hover:bg-gray-400"
                      @click="copyPublicLink(form.token)"
                    >
                      Copiar enlace
                    </Button>
                    <Button
                      as="a"
                      size="sm"
                      class="bg-green-500 text-white hover:bg-green-600"
                      :href="`https://wa.me/?text=${encodeURIComponent(`${origin}/public/resignation/${form.token}`)}`"
                      target="_blank"
                    >
                      WhatsApp
                    </Button>
                    <Button
                      as="a"
                      size="sm"
                      class="bg-cyan-600 text-white hover:bg-cyan-700"
                      :href="`${origin}/public/resignation/${form.token}`"
                      target="_blank"
                    >
                      <Printer class="w-4 h-4" /> Imprimir
                    </Button>
                    <Button
                      size="sm"
                      class="bg-yellow-500 text-white hover:bg-yellow-600"
                      @click="openPdfPreview(form.token)"
                    >
                      <Eye class="w-4 h-4" /> Vista previa
                    </Button>
                    <Button
                      as="a"
                      size="sm"
                      class="bg-purple-600 text-white hover:bg-purple-700"
                      :href="route('resignation-forms.export.pdf', form.id)"
                      target="_blank"
                    >
                      <FileDown class="w-4 h-4" /> Exportar PDF
                    </Button>
                    <Button
                      size="sm"
                      class="bg-rose-400 text-white hover:bg-rose-600"
                      @click="activeQR = null"
                    >
                      <X class="w-4 h-4" />
                    </Button>
                  </div>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2">
          <template v-for="(link, index) in forms.links" :key="index">
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
          </template>
        </div>

        <div v-if="forms.data.length === 0" class="text-center text-gray-500 py-6">
          No hay cartas de renuncia registradas.
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Modal Vista Previa PDF -->
  <div
    v-if="showPdfModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg overflow-hidden shadow-xl w-full max-w-4xl h-[90vh] flex flex-col">
      <div class="p-4 flex justify-between items-center border-b">
        <h2 class="text-lg font-semibold">Vista previa de carta de renuncia</h2>
        <Button
          size="sm"
          class="bg-red-500 text-white hover:bg-red-700"
          @click="showPdfModal = false"
        >
          Cerrar
        </Button>
      </div>
      <iframe :src="pdfUrl" class="flex-1 w-full" />
    </div>
  </div>
</template>
