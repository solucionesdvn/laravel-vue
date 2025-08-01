<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Button } from '@/components/ui/button'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cajas', href: '/cash-registers' },
  { title: 'Abrir Caja' },
]

const form = useForm({
  initial_amount: ''
})

function submit() {
  form.post(route('cash-registers.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Abrir Caja" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-xl mx-auto space-y-6">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Abrir Caja</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="initial_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monto Inicial</label>
          <input
            v-model="form.initial_amount"
            type="number"
            step="0.01"
            id="initial_amount"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white"
            required
          />
          <div v-if="form.errors.initial_amount" class="text-sm text-red-500 mt-1">
            {{ form.errors.initial_amount }}
          </div>
        </div>

        <Button type="submit" :disabled="form.processing" class="bg-green-600 hover:bg-green-700 text-white">
          Abrir Caja
        </Button>
      </form>
    </div>
  </AppLayout>
</template>