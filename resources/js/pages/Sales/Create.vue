<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  categories: Array,
})

const selectedCategoryId = ref(null)
const paymentMethod = ref('Efectivo') // Puedes inicializar con "Efectivo" o vacío
const cart = ref([])

const selectedCategory = computed(() => {
  return props.categories.find(cat => cat.id === selectedCategoryId.value)
})

const addToCart = (product) => {
  const existing = cart.value.find(item => item.product_id === product.id)
  if (existing) {
    if (existing.quantity < product.stock) {
      existing.quantity++
    } else {
      alert('No puedes añadir más de la cantidad disponible en stock.')
    }
  } else {
    if (product.stock > 0) {
      cart.value.push({
        product_id: product.id,
        name: product.name,
        quantity: 1,
        unit_price: Number(product.price || 0),
        stock: product.stock,
      })
    }
  }
}

const removeFromCart = (productId) => {
  cart.value = cart.value.filter(item => item.product_id !== productId)
}

const increaseQuantity = (item) => {
  if (item.quantity < item.stock) {
    item.quantity++
  } else {
    alert('Has alcanzado el límite de stock para este producto.')
  }
}

const decreaseQuantity = (item) => {
  if (item.quantity > 1) item.quantity--
}

const total = computed(() =>
  cart.value.reduce((sum, item) => sum + item.quantity * Number(item.unit_price || 0), 0).toFixed(2)
)

const submit = () => {
  if (cart.value.length === 0) return

  router.post('/sales', {
    items: cart.value.map(item => ({
      product_id: item.product_id,
      quantity: item.quantity,
      unit_price: Number(item.unit_price || 0),
    })),
    payment_method: paymentMethod.value,
  })
}
</script>

<template>
  <AppLayout title="Registrar Venta">
    <div class="p-6 max-w-6xl mx-auto space-y-6">
      <h1 class="text-2xl font-semibold text-gray-800">Registrar Venta</h1>

      <!-- Categorías -->
      <div class="flex flex-wrap gap-2">
        <button
          v-for="category in props.categories"
          :key="category.id"
          @click="selectedCategoryId = category.id"
          :style="{ backgroundColor: selectedCategoryId === category.id ? category.color : 'white', borderColor: category.color, color: selectedCategoryId === category.id ? 'white' : 'black' }"
          class="px-4 py-2 rounded-lg border transition"
        >
          {{ category.name }}
        </button>
      </div>

      <!-- Productos -->
      <div v-if="selectedCategory" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
        <button
          v-for="product in selectedCategory.products"
          :key="product.id"
          @click="addToCart(product)"
          class="relative p-4 bg-white border rounded-xl shadow hover:shadow-md text-left"
          :disabled="product.stock === 0"
          :style="{ borderColor: selectedCategory.color }"
        >
          <div class="text-sm font-semibold">{{ product.name }}</div>
          <div class="text-xs text-gray-500">${{ Number(product.price || 0).toFixed(2) }}</div>
          <span class="absolute top-1 right-1 bg-gray-200 text-xs px-2 py-0.5 rounded-full">
            Stock: {{ product.stock }}
          </span>
        </button>
      </div>

      <!-- Carrito -->
      <div v-if="cart.length" class="bg-gray-50 rounded-xl p-4 shadow space-y-2">
        <h2 class="font-semibold text-lg">Carrito</h2>
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-600 border-b">
              <th class="py-1">Producto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in cart" :key="item.product_id">
              <td class="py-1">
                {{ item.name }}
                <div class="text-xs text-gray-500">
                  Disponible: {{ item.stock - item.quantity }}
                </div>
              </td>
              <td>
                <div class="flex items-center gap-1">
                  <button
                    class="px-2 bg-gray-200 rounded hover:bg-gray-300"
                    @click="decreaseQuantity(item)"
                  >−</button>
                  <span class="w-6 text-center">{{ item.quantity }}</span>
                  <button
                    class="px-2 bg-gray-200 rounded hover:bg-gray-300"
                    @click="increaseQuantity(item)"
                    :disabled="item.quantity >= item.stock"
                    :class="{ 'opacity-50 cursor-not-allowed': item.quantity >= item.stock }"
                  >+</button>
                </div>
              </td>
              <td>${{ Number(item.unit_price).toFixed(2) }}</td>
              <td>${{ (item.quantity * Number(item.unit_price)).toFixed(2) }}</td>
              <td>
                <button @click="removeFromCart(item.product_id)" class="text-red-500 hover:underline">
                  Quitar
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Método de pago -->
        <div class="mt-4">
          <label class="block font-medium text-sm text-gray-700 mb-1">Método de Pago</label>
          <select
            v-model="paymentMethod"
            class="w-full border rounded-lg px-3 py-2 text-sm"
          >
            <option value="Efectivo">Efectivo</option>
            <option value="Tarjeta">Tarjeta</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Otro">Otro</option>
          </select>
        </div>

        <!-- Total y botón -->
        <div class="flex justify-end items-center gap-4 mt-6">
          <div class="font-bold text-xl">Total: ${{ total }}</div>
          <button
            @click="submit"
            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700"
          >
            Registrar Venta
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>