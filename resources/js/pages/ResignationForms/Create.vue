<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { CirclePlus } from 'lucide-vue-next'

const form = useForm({
  full_name: '',
  resignation_date: '',
  reason: '',
  signature: '',
})

const token = ref('')
const publicUrl = ref('')

function submitForm() {
  form.post(route('resignation-forms.store'))
}

function copyLink() {
  if (publicUrl.value) {
    navigator.clipboard.writeText(publicUrl.value)
    alert('Enlace copiado al portapapeles')
  }
}
</script>

<template>
  <Head title="Crear carta de renuncia" />

  <AppLayout>
    <div class="max-w-3xl mx-auto p-6 space-y-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Crear carta de renuncia</h1>
        <Link
          href="/resignation-forms"
          class="text-sm text-gray-600 hover:underline dark:text-gray-300"
        >
          ← Volver al listado
        </Link>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-white">
            Nombre completo
          </label>
          <Input
            v-model="form.full_name"
            id="full_name"
            type="text"
            class="mt-1 w-full"
            :class="{ 'border-red-500': form.errors.full_name }"
          />
          <p v-if="form.errors.full_name" class="text-red-500 text-sm">{{ form.errors.full_name }}</p>
        </div>

        <div>
          <label for="resignation_date" class="block text-sm font-medium text-gray-700 dark:text-white">
            Fecha de renuncia
          </label>
          <Input
            v-model="form.resignation_date"
            id="resignation_date"
            type="date"
            class="mt-1 w-full"
            :class="{ 'border-red-500': form.errors.resignation_date }"
          />
          <p v-if="form.errors.resignation_date" class="text-red-500 text-sm">{{ form.errors.resignation_date }}</p>
        </div>

        <div>
          <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-white">
            Motivo
          </label>
          <textarea
            v-model="form.reason"
            id="reason"
            rows="3"
            class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
            :class="{ 'border-red-500': form.errors.reason }"
          ></textarea>
          <p v-if="form.errors.reason" class="text-red-500 text-sm">{{ form.errors.reason }}</p>
        </div>

        <div>
          <label for="signature" class="block text-sm font-medium text-gray-700 dark:text-white">
            Firma (opcional)
          </label>
          <Input
            v-model="form.signature"
            id="signature"
            type="text"
            class="mt-1 w-full"
          />
        </div>

        <div class="flex items-center gap-4">
          <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-800">
            <CirclePlus class="w-4 h-4 mr-1" /> Guardar carta
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>