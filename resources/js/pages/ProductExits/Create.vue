<script setup lang="ts">
import { computed } from 'vue'
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
  products: Array<{
    id: number
    name: string
    stock: number
  }>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Salidas', href: route('product-exits.index') },
  { title: 'Crear Salida', href: null },
]

const form = useForm({
  date: new Date().toISOString().slice(0, 10),
  reason: '',
  notes: '',
  items: [] as Array<{ product_id: number | null; quantity: number }>,
})

// Computed property to get IDs of products already in the form
const selectedProductIds = computed(() => {
  return new Set(form.items.map(item => item.product_id));
});

// Function to get the stock for a given product ID
function getStockForProduct(productId: number | null): number | undefined {
    if (!productId) return undefined;
    const product = props.products.find(p => p.id === productId);
    return product ? product.stock : undefined;
}

function addItem() {
  form.items.push({
    product_id: null,
    quantity: 1,
  })
}

function removeItem(index: number) {
  form.items.splice(index, 1)
}

function submit() {
  form.post(route('product-exits.store'))
}

const inputClass = "mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50";

</script>

<template>
  <Head title="Nueva Salida de Productos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
        <form @submit.prevent="submit">
            <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
                <CardHeader>
                    <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Registrar Nueva Salida</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <Label for="date">Fecha</Label>
                            <Input id="date" type="date" v-model="form.date" />
                            <InputError :message="form.errors.date" />
                        </div>
                        <div>
                            <Label for="reason">Motivo de la Salida</Label>
                            <Input id="reason" type="text" v-model="form.reason" placeholder="Ej. Merma, Uso interno" />
                            <InputError :message="form.errors.reason" />
                        </div>
                    </div>
                    <div>
                        <Label for="notes">Notas Adicionales</Label>
                        <textarea id="notes" v-model="form.notes" rows="3" :class="inputClass"></textarea>
                        <InputError :message="form.errors.notes" />
                    </div>

                    <!-- Items -->
                    <div class="space-y-4 pt-4 border-t dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Productos</h3>
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end border dark:border-gray-700 rounded-lg p-3"
                        >
                            <div class="col-span-12 sm:col-span-7">
                                <Label class="text-xs">Producto</Label>
                                <select v-model="item.product_id" :class="inputClass">
                                    <option :value="null" disabled>-- Seleccione un producto --</option>
                                    <option 
                                        v-for="product in props.products" 
                                        :key="product.id" 
                                        :value="product.id"
                                        :disabled="selectedProductIds.has(product.id) && item.product_id !== product.id"
                                    >
                                        {{ product.name }} (Stock: {{ product.stock }})
                                    </option>
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <Label class="text-xs">Cantidad</Label>
                                <Input 
                                    type="number" 
                                    min="1" 
                                    :max="getStockForProduct(item.product_id)"
                                    v-model.number="item.quantity" 
                                    placeholder="Cantidad" 
                                />
                            </div>

                            <div class="col-span-6 sm:col-span-1 flex justify-end">
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
                <CardFooter class="flex justify-end bg-gray-50 dark:bg-gray-800/50 py-4 px-6">
                    <div class="flex gap-4">
                        <Button variant="ghost" as-child>
                            <Link :href="route('product-exits.index')">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Guardando...</span>
                            <span v-else>Registrar Salida</span>
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </form>
    </div>
  </AppLayout>
</template>