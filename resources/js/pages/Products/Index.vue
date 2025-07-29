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
import { CirclePlus, Pencil, Trash, Camera, Search } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Productos', href: '/products' }
]

const props = defineProps<{
  products: {
    data: Array<any>
    links: Array<any>
    meta: Record<string, any>
  }
  filters: {
    search: string
  }
}>()

const selectedProduct = ref(null)
const showModal = ref(false)
const imageForm = ref({ image: null })
const previewUrl = ref<string | null>(null)
const errors = ref<{ image?: string }>({})

const searchForm = useForm({
  search: props.filters.search || ''
})

function submitSearch() {
  searchForm.get(route('products.index'), {
    preserveScroll: true,
    preserveState: true
  })
}

function openImageModal(product: any) {
  selectedProduct.value = product
  showModal.value = true
  imageForm.value.image = null
  previewUrl.value = product.image || null
  errors.value.image = undefined
}

function handleImageChange(event: Event) {
  const file = (event.target as HTMLInputElement)?.files?.[0]
  if (file) {
    imageForm.value.image = file
    previewUrl.value = URL.createObjectURL(file)
    errors.value.image = undefined
  }
}

function updateImage() {
  if (!selectedProduct.value) return

  const formData = new FormData()
  formData.append('_method', 'PUT')
  if (imageForm.value.image) {
    formData.append('image', imageForm.value.image)
  }

  router.post(route('products.updateImage', selectedProduct.value.id), formData, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showModal.value = false
      imageForm.value.image = null
      previewUrl.value = null
      errors.value.image = undefined
    },
    onError: (err) => {
      errors.value.image = err.image
    },
  })
}

function deleteProduct(id: number) {
  if (confirm('¿Estás seguro de eliminar este producto?')) {
    router.delete(route('products.destroy', id), {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <Head title="Productos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Lista de Productos</h1>
        <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
          <Link :href="route('products.create')">
            <CirclePlus class="mr-1" /> Nuevo producto
          </Link>
        </Button>
      </div>

      <!-- Buscador -->
      <div class="w-full md:w-1/3">
        <form @submit.prevent="submitSearch" class="flex items-center gap-2">
          <input
            type="text"
            v-model="searchForm.search"
            placeholder="Buscar producto..."
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
          <TableCaption>Tabla de productos</TableCaption>

          <TableHeader class="bg-gray-50 dark:bg-gray-800">
            <TableRow>
              <TableHead class="px-6 py-3">Imagen</TableHead>
              <TableHead class="px-6 py-3">Nombre</TableHead>
              <TableHead class="px-6 py-3">SKU</TableHead>
              <TableHead class="px-6 py-3">Precio</TableHead>
              <TableHead class="px-6 py-3">Stock</TableHead>
              <TableHead class="px-6 py-3">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <TableRow v-for="product in products.data" :key="product.id">
              <TableCell class="px-6 py-4">
                <img
                  :src="product.image || 'https://via.placeholder.com/50'"
                  alt="Product Image"
                  class="w-12 h-12 rounded object-cover border shadow"
                />
              </TableCell>
              <TableCell class="px-6 py-4">{{ product.name }}</TableCell>
              <TableCell class="px-6 py-4">{{ product.sku }}</TableCell>
              <TableCell class="px-6 py-4">$ {{ product.price }}</TableCell>
              <TableCell class="px-6 py-4">{{ product.stock }}</TableCell>
              <TableCell class="px-6 py-4 flex gap-2">
                <Button as-child size="sm" class="bg-blue-500 text-white hover:bg-blue-700">
                  <Link :href="route('products.edit', product.id)">
                    <Pencil />
                  </Link>
                </Button>
                <Button size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700" @click="openImageModal(product)">
                  <Camera />
                </Button>
                <Button size="sm" class="bg-rose-500 text-white hover:bg-rose-700" @click="deleteProduct(product.id)">
                  <Trash />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Paginación -->
        <div class="mt-4 flex justify-center space-x-2">
          <template v-for="(link, index) in products.links" :key="index">
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

        <div v-if="products.data.length === 0" class="text-center text-gray-500 py-6">
          No hay productos registrados.
        </div>
      </div>

      <!-- Modal imagen -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
      >
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg w-full max-w-md space-y-4">
          <h2 class="text-lg font-semibold text-center text-gray-800 dark:text-white">Actualizar imagen</h2>
          <div class="flex justify-center">
            <img
              v-if="previewUrl"
              :src="previewUrl"
              alt="Vista previa"
              class="w-40 h-40 object-cover rounded border shadow"
            />
            <div
              v-else
              class="w-40 h-40 flex items-center justify-center text-gray-400 bg-gray-100 dark:bg-gray-800 rounded border"
            >
              Sin imagen
            </div>
          </div>
          <input
            type="file"
            accept="image/*"
            @change="handleImageChange"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
              file:rounded-lg file:border-0 file:text-sm file:font-semibold
              file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
          />
          <p v-if="errors.image" class="text-sm text-red-600 mt-1">{{ errors.image }}</p>
          <div class="flex justify-end gap-2">
            <Button variant="outline" @click="showModal = false">Cancelar</Button>
            <Button @click="updateImage" class="bg-indigo-600 text-white hover:bg-indigo-700">
              Guardar
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>