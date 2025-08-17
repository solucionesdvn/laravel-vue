<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card'
import InputError from '@/components/InputError.vue'
import { type BreadcrumbItem } from '@/types'
import { Trash2 } from 'lucide-vue-next'

const props = defineProps<{
  suppliers: Array<{ id: number; name: string }>
  products: Array<{ id: number; name: string; supplier_id: number | null }>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Entradas', href: route('entries.index') },
  { title: 'Crear Entrada', href: null }
]

const marginOptions = [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80]
const selectedMargin = ref(30)

const form = useForm({
  supplier_id: null as number | null,
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

const selectedProductIds = computed(() => {
  return new Set(form.items.map(item => item.product_id));
});

function addItem() {
  form.items.push({
    product_id: null,
    quantity: 1,
    purchase_price: 0,
    update_price: true,
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
  if (isNaN(purchase_price) || isNaN(margin)) return 0;
  return +(purchase_price * (1 + margin / 100)).toFixed(2)
}

function submit() {
  form.post(route('entries.store'), {
    preserveScroll: true,
  })
}

const inputClass = "mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50";

</script>
<template>
  <Head title="Nueva Entrada" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-4xl mx-auto">
        <form @submit.prevent="submit">
            <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
                <CardHeader>
                    <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Registrar Nueva Entrada</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <Label for="supplier_id">Proveedor</Label>
                            <select id="supplier_id" v-model="form.supplier_id" :class="inputClass">
                                <option :value="null">-- Sin proveedor --</option>
                                <option v-for="supplier in props.suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.supplier_id" />
                        </div>
                        <div>
                            <Label for="date">Fecha</Label>
                            <Input id="date" type="date" v-model="form.date" />
                            <InputError :message="form.errors.date" />
                        </div>
                    </div>
                    <div>
                        <Label for="notes">Notas</Label>
                        <textarea id="notes" v-model="form.notes" rows="3" :class="inputClass"></textarea>
                        <InputError :message="form.errors.notes" />
                    </div>

                    <!-- Items -->
                    <div class="space-y-4 pt-4 border-t dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Productos</h3>
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="grid grid-cols-12 gap-3 items-end border dark:border-gray-700 rounded-lg p-3"
                        >
                            <div class="col-span-12 sm:col-span-3">
                                <Label class="text-xs">Producto</Label>
                                <select v-model="item.product_id" :class="inputClass">
                                    <option :value="null">-- Producto --</option>
                                    <option 
                                        v-for="product in filteredProducts" 
                                        :key="product.id" 
                                        :value="product.id"
                                        :disabled="selectedProductIds.has(product.id) && item.product_id !== product.id"
                                    >
                                        {{ product.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <Label class="text-xs">Cantidad</Label>
                                <Input type="number" min="1" v-model.number="item.quantity" />
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <Label class="text-xs">Precio compra</Label>
                                <Input type="number" min="0" step="0.01" v-model.number="item.purchase_price" />
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <Label class="text-xs">Margen</Label>
                                <select v-model.number="item.margin" :class="inputClass">
                                    <option v-for="percent in marginOptions" :key="percent" :value="percent">
                                    +{{ percent }}%
                                    </option>
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <Label class="text-xs">Precio venta</Label>
                                <div class="flex items-center h-9">
                                    <span class="text-sm text-gray-800 dark:text-gray-200 font-mono">${{ calculatedPrice(item.purchase_price, item.margin) }}</span>
                                    <input type="checkbox" v-model="item.update_price" class="ml-2 rounded" />
                                    <span class="text-xs text-gray-600 dark:text-gray-300 ml-1">Act.</span>
                                </div>
                            </div>

                            <div class="col-span-12 sm:col-span-1 flex justify-end">
                                <Button type="button" variant="destructive" size="icon" @click="removeItem(index)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>

                        <div>
                            <Button type="button" variant="secondary" @click="addItem">Agregar Producto</Button>
                        </div>
                        <InputError :message="form.errors.items" />
                    </div>
                </CardContent>
                <CardFooter class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 py-4 px-6">
                    <div class="text-xl font-semibold text-gray-800 dark:text-white">
                        Total: <span class="font-mono">$ {{ total.toFixed(2) }}</span>
                    </div>
                    <div class="flex gap-4">
                        <Button variant="ghost" as-child>
                            <Link :href="route('entries.index')">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Guardando...</span>
                            <span v-else>Registrar Entrada</span>
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </form>
    </div>
  </AppLayout>
</template>