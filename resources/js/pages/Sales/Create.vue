<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import InputError from '@/components/InputError.vue'
import { Label } from '@/components/ui/label'
import { type BreadcrumbItem } from '@/types'
import { Trash2 } from 'lucide-vue-next'

const props = defineProps<{
  categories: Array<any>
  clients: Array<{ id: number; name: string }>
  paymentMethods: Array<{ id: number; name: string }>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Ventas', href: route('sales.index') },
  { title: 'Registrar Venta', href: null },
]

const selectedCategoryId = ref<number | null>(null)

const form = useForm({
  client_id: null as number | null,
  payment_method_id: props.paymentMethods[0]?.id || null,
  items: [] as Array<{
    product_id: number
    name: string
    quantity: number
    unit_price: number
    stock: number
  }>,
})

const selectedCategory = computed(() => {
  return props.categories.find(cat => cat.id === selectedCategoryId.value)
})

const addToCart = (product: any) => {
  const existing = form.items.find(item => item.product_id === product.id)
  if (existing) {
    if (existing.quantity < product.stock) {
      existing.quantity++
    }
  } else {
    if (product.stock > 0) {
      form.items.push({
        product_id: product.id,
        name: product.name,
        quantity: 1,
        unit_price: Number(product.price || 0),
        stock: product.stock,
      })
    }
  }
}

const removeFromCart = (productId: number) => {
  form.items = form.items.filter(item => item.product_id !== productId)
}

const increaseQuantity = (item: any) => {
  if (item.quantity < item.stock) {
    item.quantity++
  }
}

const decreaseQuantity = (item: any) => {
  if (item.quantity > 1) item.quantity--
}

const total = computed(() =>
  form.items.reduce((sum, item) => sum + item.quantity * Number(item.unit_price || 0), 0)
)

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
};

function submit() {
  if (form.items.length === 0) return
  form.post(route('sales.store'))
}

const inputClass = "mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50";

</script>

<template>
  <Head title="Registrar Venta" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-4 sm:p-6">
      <!-- Columna de Productos -->
      <div class="lg:col-span-2 space-y-6">
        <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
            <CardHeader>
                <CardTitle>Selección de Productos</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="flex flex-wrap gap-2">
                    <Button 
                        v-for="category in props.categories"
                        :key="category.id"
                        @click="selectedCategoryId = category.id"
                        :style="{
                            backgroundColor: selectedCategoryId === category.id ? category.color : 'transparent',
                            borderColor: category.color,
                            color: selectedCategoryId === category.id ? 'white' : category.color
                        }"
                        class="transition-colors duration-200"
                    >
                    {{ category.name }}
                    </Button>
                </div>

                <div v-if="selectedCategory" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4 pt-4 border-t dark:border-gray-700">
                    <button
                        v-for="product in selectedCategory.products"
                        :key="product.id"
                        @click="addToCart(product)"
                        class="relative p-3 bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md text-left transition-all hover:scale-105"
                        :disabled="product.stock === 0"
                        :class="{ 'opacity-50 cursor-not-allowed': product.stock === 0 }"
                    >
                        <div class="text-sm font-semibold">{{ product.name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatCurrency(product.price) }}</div>
                        <span class="absolute top-1 right-1 text-xs px-2 py-0.5 rounded-full"
                            :class="product.stock > 10 ? 'bg-green-100 text-green-800' : product.stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                        >
                            Stock: {{ product.stock }}
                        </span>
                    </button>
                </div>
            </CardContent>
        </Card>
      </div>

      <!-- Columna de Carrito y Pago -->
      <div class="lg:col-span-1">
        <form @submit.prevent="submit">
            <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md sticky top-6">
                <CardHeader>
                    <CardTitle>Resumen de Venta</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <Label for="client_id">Cliente</Label>
                        <select id="client_id" v-model="form.client_id" :class="inputClass">
                            <option :value="null" disabled>-- Seleccione un cliente --</option>
                            <option v-for="client in props.clients" :key="client.id" :value="client.id">
                                {{ client.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.client_id" />
                    </div>

                    <div v-if="form.items.length === 0" class="text-center text-gray-500 py-8">
                        El carrito está vacío.
                    </div>

                    <div v-else class="space-y-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Producto</TableHead>
                                    <TableHead class="text-center">Cant.</TableHead>
                                    <TableHead class="text-right">Subtotal</TableHead>
                                    <TableHead></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in form.items" :key="item.product_id">
                                    <TableCell>
                                        <p class="font-medium">{{ item.name }}</p>
                                        <p class="text-xs text-gray-500">{{ formatCurrency(item.unit_price) }}</p>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex items-center justify-center gap-1">
                                            <Button type="button" @click="decreaseQuantity(item)" variant="ghost" size="icon" class="h-6 w-6">-</Button>
                                            <span>{{ item.quantity }}</span>
                                            <Button type="button" @click="increaseQuantity(item)" :disabled="item.quantity >= item.stock" variant="ghost" size="icon" class="h-6 w-6">+</Button>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right font-mono">{{ formatCurrency(item.quantity * item.unit_price) }}</TableCell>
                                    <TableCell>
                                        <Button type="button" @click="removeFromCart(item.product_id)" variant="destructive" size="icon" class="h-6 w-6">
                                            <Trash2 class="h-3 w-3" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div>
                        <Label for="payment_method_id">Método de Pago</Label>
                        <select id="payment_method_id" v-model="form.payment_method_id" :class="inputClass">
                            <option v-for="method in props.paymentMethods" :key="method.id" :value="method.id">
                                {{ method.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.payment_method_id" />
                    </div>
                </CardContent>
                <CardFooter class="flex flex-col gap-4 bg-gray-50 dark:bg-gray-800/50 p-4">
                    <div class="w-full flex justify-between items-center text-xl font-bold">
                        <span>Total:</span>
                        <span class="font-mono">{{ formatCurrency(total) }}</span>
                    </div>
                    <Button type="submit" class="w-full" size="lg" :disabled="form.processing || form.items.length === 0">
                        <span v-if="form.processing">Procesando...</span>
                        <span v-else>Registrar Venta</span>
                    </Button>
                </CardFooter>
            </Card>
        </form>
      </div>
    </div>
  </AppLayout>
</template>