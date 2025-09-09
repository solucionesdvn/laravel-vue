<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import InputError from '@/components/InputError.vue'
import { Label } from '@/components/ui/label'
import { type BreadcrumbItem, type Company, type Client } from '@/types'
import { Trash2, PlusCircle } from 'lucide-vue-next'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog'

const props = defineProps<{
  categories: Array<any>
  clients: Array<Client>
  paymentMethods: Array<{ id: number; name: string }>
  company: Company
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Ventas', href: route('sales.index') },
  { title: 'Registrar Venta', href: null },
]

// State for global product search
const searchQuery = ref('')
const searchResults = ref<any[]>([])
const isLoadingSearch = ref(false)
let debounceTimer: number | undefined

// State for category selection
const selectedCategoryId = ref<number | null>(null)
const categoryProducts = ref<any[]>([])
const isLoadingCategoryProducts = ref(false)

// State for change calculation
const amountPaid = ref<number | null>(null)

// State for client modal
const isClientModalOpen = ref(false)
const clientList = ref(props.clients)

const saleForm = useForm({
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

const clientForm = useForm({
    name: '',
    email: '',
    phone: '',
    identification: '',
})

async function selectCategory(categoryId: number) {
  if (selectedCategoryId.value === categoryId) {
    // Deselect category if clicked again
    selectedCategoryId.value = null
    categoryProducts.value = []
    return
  }

  selectedCategoryId.value = categoryId
  isLoadingCategoryProducts.value = true
  categoryProducts.value = [] // Clear previous products

  try {
    const response = await axios.get(route('categories.products', { category: categoryId }))
    categoryProducts.value = response.data
  } catch (error) {
    console.error('Error fetching products for category:', error)
  } finally {
    isLoadingCategoryProducts.value = false
  }
}

const addToCart = (product: any) => {
  const existing = saleForm.items.find(item => item.product_id === product.id)
  if (existing) {
    if (existing.quantity < product.stock) {
      existing.quantity++
    }
  } else {
    if (product.stock > 0) {
      saleForm.items.push({
        product_id: product.id,
        name: product.name,
        quantity: 1,
        unit_price: Number(product.price || 0),
        stock: product.stock,
      })
    }
  }
}

const addFromSearch = (product: any) => {
    addToCart(product)
    searchQuery.value = ''
    searchResults.value = []
}

watch(searchQuery, (newValue) => {
    clearTimeout(debounceTimer)
    if (newValue.length < 2) {
        searchResults.value = []
        return
    }
    debounceTimer = setTimeout(async () => {
        isLoadingSearch.value = true
        try {
            const response = await axios.get('/products/search', { params: { term: newValue } })
            searchResults.value = response.data
        } catch (error) {
            console.error('Error searching products:', error)
            searchResults.value = []
        } finally {
            isLoadingSearch.value = false
        }
    }, 300)
})

const removeFromCart = (productId: number) => {
  saleForm.items = saleForm.items.filter(item => item.product_id !== productId)
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
  saleForm.items.reduce((sum, item) => sum + item.quantity * Number(item.unit_price || 0), 0)
)

const change = computed(() => {
    if (amountPaid.value === null || amountPaid.value <= 0 || amountPaid.value < total.value) {
        return 0
    }
    return amountPaid.value - total.value
})

const formatIntegerCurrency = (value: number) => {
    const locale = props.company?.locale || 'es-CO'
    const currency = props.company?.currency || 'COP'
    return new Intl.NumberFormat(locale, { style: 'currency', currency, minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};

const formatDecimalCurrency = (value: number) => {
    const locale = props.company?.locale || 'es-CO'
    const currency = props.company?.currency || 'COP'
    return new Intl.NumberFormat(locale, { style: 'currency', currency, minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};

function submitSale() {
  if (saleForm.items.length === 0) return

  // Transform the data to send only the required fields for items
  saleForm.transform(data => ({
    ...data,
    items: data.items.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        unit_price: item.unit_price,
    }))
  })).post('/sales', { preserveScroll: true })
}

async function submitNewClient() {
    clientForm.processing = true;
    try {
        const response = await axios.post(route('clients.api.store'), clientForm.data());
        
        // Manually handle success
        const newClient = response.data as Client;
        clientList.value.push(newClient);
        saleForm.client_id = newClient.id;
        
        isClientModalOpen.value = false;
        clientForm.reset();
        clientForm.clearErrors();

    } catch (error: any) {
        // Manually handle validation errors
        if (error.response && error.response.status === 422) {
            clientForm.setError(error.response.data.errors);
        } else {
            console.error('An unexpected error occurred:', error);
            // Optionally, show a generic error message to the user
        }
    } finally {
        clientForm.processing = false;
    }
}

const inputClass = "mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50";

</script>

<template>
  <Head title="Registrar Venta" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-4 sm:p-6">
      <!-- Columna de Productos -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Global Product Search Card -->
        <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
            <CardHeader>
                <CardTitle>Búsqueda de Productos</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="relative">
                    <Label for="global-search-product">Buscar por Nombre o SKU en todo el inventario</Label>
                    <input
                        id="global-search-product"
                        type="text"
                        v-model="searchQuery"
                        placeholder="Escribe para buscar..."
                        :class="inputClass"
                        class="mt-1"
                        autocomplete="off"
                    />
                    <div v-if="isLoadingSearch" class="mt-2 text-sm text-gray-500">Buscando...</div>
                    
                    <div v-if="searchResults.length > 0" class="absolute z-10 w-full bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-md shadow-lg mt-1">
                        <ul>
                            <li v-for="product in searchResults" :key="product.id">
                                <button 
                                    @click="addFromSearch(product)" 
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex justify-between items-center"
                                    type="button"
                                >
                                    <div>
                                        <p class="font-semibold">{{ product.name }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ product.sku || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-mono">{{ formatIntegerCurrency(product.price) }}</p>
                                        <p class="text-xs text-right" :class="product.stock > 0 ? 'text-green-500' : 'text-red-500'">Stock: {{ product.stock }}</p>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Category Selection Card -->
        <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
            <CardHeader>
                <CardTitle>Selección de Productos por Categoría</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="category in props.categories"
                        :key="category.id"
                        @click="selectCategory(category.id)"
                        :style="{
                            backgroundColor: selectedCategoryId === category.id ? category.color : 'transparent',
                            borderColor: category.color,
                            color: selectedCategoryId === category.id ? 'white' : category.color
                        }"
                        class="w-32 justify-center transition-all duration-200 hover:shadow-lg hover:scale-105"
                    >
                        <span class="truncate">{{ category.name }}</span>
                    </Button>
                </div>

                <div v-if="isLoadingCategoryProducts" class="mt-4 pt-4 border-t dark:border-gray-700 text-center">
                    Cargando productos...
                </div>

                <div v-else-if="selectedCategoryId && categoryProducts.length === 0" class="mt-4 pt-4 border-t dark:border-gray-700 text-center">
                    No hay productos en stock para esta categoría.
                </div>

                <div v-else-if="categoryProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4 pt-4 border-t dark:border-gray-700">
                    <button
                        v-for="product in categoryProducts"
                        :key="product.id"
                        @click="addToCart(product)"
                        class="relative p-3 bg-gray-100 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl shadow-md hover:shadow-xl hover:border-gray-300 text-left transition-all hover:scale-105"
                        :disabled="product.stock === 0"
                        :class="{ 'opacity-50 cursor-not-allowed': product.stock === 0 }"
                    >
                        <div class="text-sm font-semibold">{{ product.name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatIntegerCurrency(product.price) }}</div>
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
        <form @submit.prevent="submitSale">
            <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md sticky top-6">
                <CardHeader>
                    <CardTitle>Resumen de Venta</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <Label for="client_id">Cliente</Label>
                        <div class="flex items-center gap-2">
                            <select id="client_id" v-model="saleForm.client_id" class="flex-grow" :class="inputClass">
                                <option :value="null">-- Seleccione un cliente --</option>
                                <option v-for="client in clientList" :key="client.id" :value="client.id">
                                    {{ client.name }}
                                </option>
                            </select>
                            <Button @click="isClientModalOpen = true" type="button" variant="outline" size="icon">
                                <PlusCircle class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="saleForm.errors.client_id" />
                    </div>

                    <div v-if="saleForm.items.length === 0" class="text-center text-gray-500 py-8">
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
                                <TableRow v-for="item in saleForm.items" :key="item.product_id">
                                    <TableCell>
                                        <p class="font-medium">{{ item.name }}</p>
                                        <p class="text-xs text-gray-500">{{ formatIntegerCurrency(item.unit_price) }}</p>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex items-center justify-center gap-1">
                                            <Button type="button" @click="decreaseQuantity(item)" variant="ghost" size="icon" class="h-6 w-6">-</Button>
                                            <span>{{ item.quantity }}</span>
                                            <Button type="button" @click="increaseQuantity(item)" :disabled="item.quantity >= item.stock" variant="ghost" size="icon" class="h-6 w-6">+</Button>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right font-mono">{{ formatIntegerCurrency(item.quantity * item.unit_price) }}</TableCell>
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
                        <select id="payment_method_id" v-model="saleForm.payment_method_id" :class="inputClass">
                            <option v-for="method in props.paymentMethods" :key="method.id" :value="method.id">
                                {{ method.name }}
                            </option>
                        </select>
                        <InputError :message="saleForm.errors.payment_method_id" />
                    </div>
                </CardContent>
                <CardFooter class="flex flex-col gap-4 bg-gray-50 dark:bg-gray-800/50 p-4">
                    <div class="border-t w-full pt-4 space-y-2">
                        <div>
                            <Label for="amount_paid">Paga con</Label>
                            <input 
                                id="amount_paid"
                                type="number"
                                v-model.number="amountPaid"
                                placeholder="Opcional..."
                                :class="inputClass"
                            />
                        </div>
                        <div class="w-full flex justify-between items-center text-lg">
                            <span>Total:</span>
                            <span class="font-mono">{{ formatDecimalCurrency(total) }}</span>
                        </div>
                        <div v-if="change > 0" class="w-full flex justify-between items-center text-lg font-semibold text-indigo-600 dark:text-indigo-400">
                            <span>Vueltas:</span>
                            <span class="font-mono">{{ formatDecimalCurrency(change) }}</span>
                        </div>
                    </div>
                    <Button type="submit" class="w-full" size="lg" :disabled="saleForm.processing || saleForm.items.length === 0">
                        <span v-if="saleForm.processing">Procesando...</span>
                        <span v-else>Registrar Venta</span>
                    </Button>
                </CardFooter>
            </Card>
        </form>
      </div>
    </div>

    <!-- Client Create Modal -->
    <Dialog :open="isClientModalOpen" @update:open="isClientModalOpen = $event">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Crear Nuevo Cliente</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="submitNewClient">
                <div class="grid gap-4 py-4">
                    <div class="space-y-1">
                        <Label for="name">Nombre</Label>
                        <input id="name" v-model="clientForm.name" :class="inputClass" />
                        <InputError :message="clientForm.errors.name" />
                    </div>
                    <div class="space-y-1">
                        <Label for="email">Email</Label>
                        <input id="email" v-model="clientForm.email" :class="inputClass" />
                        <InputError :message="clientForm.errors.email" />
                    </div>
                    <div class="space-y-1">
                        <Label for="phone">Teléfono</Label>
                        <input id="phone" v-model="clientForm.phone" :class="inputClass" />
                        <InputError :message="clientForm.errors.phone" />
                    </div>
                    <div class="space-y-1">
                        <Label for="identification">Identificación</Label>
                        <input id="identification" v-model="clientForm.identification" :class="inputClass" />
                        <InputError :message="clientForm.errors.identification" />
                    </div>
                </div>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">Cancelar</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="clientForm.processing">Guardar Cliente</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

  </AppLayout>
</template>