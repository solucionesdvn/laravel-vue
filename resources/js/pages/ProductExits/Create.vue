<script setup lang="ts">
import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  products: Array<{
    id: number
    name: string
    stock: number
    price: number
  }>
}>()

const form = reactive({
  date: new Date().toISOString().slice(0, 10),
  reason: '',
  notes: '',
  items: [] as Array<{ product_id: number; quantity: number }>
})

const errors = ref<Record<string, string[]>>({})

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Salidas', href: '/product-exits' },
  { title: 'Crear', href: null },
]

function addItem() {
  form.items.push({
    product_id: props.products[0]?.id ?? 0,
    quantity: 1,
  })
}

function removeItem(index: number) {
  form.items.splice(index, 1)
}

function submit() {
  router.post(route('product-exits.store'), form, {
    onError: (e) => {
      errors.value = e
    },
  })
}
</script>

<template>
  <Head title="Nueva Salida de Productos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Registrar Salida</h1>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
          <input
            type="date"
            v-model="form.date"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-white"
          />
          <div v-if="errors.date" class="text-red-500 text-sm">{{ errors.date[0] }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo</label>
          <input
            type="text"
            v-model="form.reason"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-white"
          />
          <div v-if="errors.reason" class="text-red-500 text-sm">{{ errors.reason[0] }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas</label>
          <textarea
            v-model="form.notes"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-white"
            rows="3"
          ></textarea>
        </div>

        <div>
          <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">Productos</h2>
          <div v-if="errors['items']" class="text-red-500 text-sm mb-2">{{ errors['items'][0] }}</div>

          <div v-for="(item, index) in form.items" :key="index" class="flex items-center gap-4 mb-2">
            <select
              v-model="item.product_id"
              class="block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-white"
            >
              <option v-for="product in props.products" :key="product.id" :value="product.id">
                {{ product.name }} (Stock: {{ product.stock }})
              </option>
            </select>

            <input
              type="number"
              v-model.number="item.quantity"
              min="1"
              class="w-24 border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-white"
              placeholder="Cantidad"
            />

            <Button @click="removeItem(index)" class="bg-red-600 hover:bg-red-700 text-white">Quitar</Button>
          </div>

          <Button @click="addItem" class="bg-gray-600 hover:bg-gray-700 text-white mt-2">+ Agregar producto</Button>
        </div>
      </div>

      <div class="pt-4">
        <Button @click="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white">Registrar salida</Button>
      </div>
    </div>
  </AppLayout>
</template>