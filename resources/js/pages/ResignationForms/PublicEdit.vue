<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  form: {
    id: number
    full_name: string
    resignation_date: string
    reason: string
    signature: string | null
    token: string
  }
}>()

const form = useForm({
  full_name: props.form.full_name || '',
  resignation_date: props.form.resignation_date || '',
  reason: props.form.reason || '',
  signature: props.form.signature || '',
})

function submit() {
  form.put(route('resignation-forms.public.update', props.form.token))
}
</script>

<template>
  <Head title="Formulario de renuncia" />

  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center px-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 w-full max-w-xl">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Formulario de renuncia</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-white">
            Nombre completo
          </label>
          <input
            v-model="form.full_name"
            type="text"
            class="w-full mt-1 px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          />
          <p v-if="form.errors.full_name" class="text-red-500 text-sm">{{ form.errors.full_name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-white">
            Fecha de renuncia
          </label>
          <input
            v-model="form.resignation_date"
            type="date"
            class="w-full mt-1 px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          />
          <p v-if="form.errors.resignation_date" class="text-red-500 text-sm">{{ form.errors.resignation_date }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-white">
            Motivo
          </label>
          <textarea
            v-model="form.reason"
            rows="3"
            class="w-full mt-1 px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          ></textarea>
          <p v-if="form.errors.reason" class="text-red-500 text-sm">{{ form.errors.reason }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-white">
            Firma (opcional)
          </label>
          <input
            v-model="form.signature"
            type="text"
            class="w-full mt-1 px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          />
        </div>

        <div class="pt-4">
          <button
            type="submit"
            class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg"
          >
            Enviar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>