<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  suppliers: Array<{ id: number; name: string }>
  products: Array<{ id: number; name: string; supplier_id: number | null }>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Entradas', href: '/entries' },
  { title: 'Crear', href: null }
]

const marginOptions = [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80]
const selectedMargin = ref(30)

const form = useForm({
  supplier_id: null,
  date: new Date().toISOString().substring(0, 10),
  notes: '',
  items: [] as Array<{
    product_id: number | null
    quantity: number
    purchase_price: number
    update_price: boolean
    margin: number
  }>
})

function addItem() {
  form.items.push({
    product_id: null,
    quantity: 1,
    purchase_price: 0,
    update_price: false,
    margin: selectedMargin.value
  })
}

function removeItem(index: number) {
  form.items.splice(index, 1)
}

const total = computed(() =>
  form.items.reduce((sum, item) => sum + item.quantity * item.purchase_price, 0)
)

const filteredProducts = computed(() => {
  if (!form.supplier_id) return props.products
  return props.products.filter(p => p.supplier_id === form.supplier_id)
})

function calculatedPrice(purchase_price: number, margin: number): number {
  return +(purchase_price * (1 + margin / 100)).toFixed(2)
}

function submit() {
  form.post(route('entries.store'), {
    preserveScroll: true,
  })
}
</script>
<template>
  <Head title="Nueva Entrada" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Registrar nueva entrada</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Proveedor -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Proveedor</label>
          <select v-model="form.supplier_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900">
            <option value="">-- Sin proveedor --</option>
            <option v-for="supplier in props.suppliers" :key="supplier.id" :value="supplier.id">
              {{ supplier.name }}
            </option>
          </select>
          <InputError :message="form.errors.supplier_id" />
        </div>

        <!-- Fecha -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fecha</label>
          <input
            type="date"
            v-model="form.date"
            class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900"
          />
          <InputError :message="form.errors.date" />
        </div>

        <!-- Notas -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notas</label>
          <textarea
            v-model="form.notes"
            rows="3"
            class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900"
          ></textarea>
          <InputError :message="form.errors.notes" />
        </div>

        <!-- Items -->
        <div class="space-y-4">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-white">Productos</h2>

          <div
            v-for="(item, index) in form.items"
            :key="index"
            class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center border border-gray-300 dark:border-gray-700 rounded-md p-3"
          >
            <div class="sm:col-span-3">
              <label class="text-xs text-gray-500">Producto</label>
              <select v-model="item.product_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                <option value="">-- Producto --</option>
                <option v-for="product in filteredProducts" :key="product.id" :value="product.id">
                  {{ product.name }}
                </option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs text-gray-500">Cantidad</label>
              <input
                type="number"
                min="1"
                v-model="item.quantity"
                class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs text-gray-500">Precio compra</label>
              <input
                type="number"
                min="0"
                step="0.01"
                v-model="item.purchase_price"
                class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs text-gray-500">Margen</label>
              <select v-model="item.margin" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                <option v-for="percent in marginOptions" :key="percent" :value="percent">
                  +{{ percent }}%
                </option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs text-gray-500">Precio venta</label>
              <div class="text-sm text-gray-800 dark:text-gray-300">
                ${{ calculatedPrice(item.purchase_price, item.margin) }}
              </div>
              <div class="flex items-center gap-1 mt-1">
                <input type="checkbox" v-model="item.update_price" />
                <span class="text-xs text-gray-600 dark:text-gray-300">Actualizar</span>
              </div>
            </div>

            <div class="sm:col-span-1 flex justify-end">
              <Button type="button" variant="destructive" size="sm" @click="removeItem(index)">X</Button>
            </div>
          </div>

          <div>
            <Button type="button" variant="secondary" @click="addItem">Agregar producto</Button>
          </div>
          <InputError :message="form.errors.items" />
        </div>

        <!-- Total y Enviar -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          <div class="text-lg font-semibold text-gray-700 dark:text-white">Total: $ {{ total.toFixed(2) }}</div>
          <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-700">Registrar entrada</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

