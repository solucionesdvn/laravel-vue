<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { type BreadcrumbItem } from '@/types'
import { ref, watch } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cartas de Renuncia', href: '/resignation-forms' },
  { title: 'Editar', href: '#' },
]

const props = defineProps<{
  formData: {
    id: number
    full_name: string
    resignation_date: string
    reason: string
    signature: string | null
  }
}>()

// Formulario inicial vacío
const form = useForm({
  full_name: '',
  resignation_date: '',
  reason: '',
  signature: '',
})

// Asignar datos al formulario cuando estén disponibles
watch(
  () => props.formData,
  (newVal) => {
    if (newVal) {
      form.full_name = newVal.full_name
      form.resignation_date = newVal.resignation_date
      form.reason = newVal.reason
      form.signature = newVal.signature ?? ''
    }
  },
  { immediate: true }
)

function submit() {
  router.put(`/resignation-forms/${props.formData.id}`, form)
}
</script>

<template>
  <Head title="Editar Carta de Renuncia" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-2xl mx-auto">
      <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white">Editar Carta de Renuncia</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="full_name" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Nombre completo</label>
          <input
            id="full_name"
            v-model="form.full_name"
            type="text"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white"
            required
          />
        </div>

        <div>
          <label for="resignation_date" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Fecha de renuncia</label>
          <input
            id="resignation_date"
            v-model="form.resignation_date"
            type="date"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white"
            required
          />
        </div>

        <div>
          <label for="reason" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Motivo</label>
          <textarea
            id="reason"
            v-model="form.reason"
            rows="4"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white"
            required
          ></textarea>
        </div>

        <div>
          <label for="signature" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Firma (opcional)</label>
          <input
            id="signature"
            v-model="form.signature"
            type="text"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white"
          />
        </div>

        <div class="mt-6">
          <Button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white">
            Guardar cambios
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>