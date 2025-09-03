<script setup lang="ts"> 
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { DocumentTemplate } from '@/types'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

// ✅ Props desde el backend
const { template } = defineProps<{ template: DocumentTemplate }>()

// ✅ Inicializamos el formulario de Inertia con keys planos tipo "data.campo"
const initialFormValues: Record<string, string> = {}
if (Array.isArray(template.fields)) {
  template.fields.forEach((field) => {
    initialFormValues[`data.${field.name}`] = ''
  })
}
const form = useForm(initialFormValues)

// Helpers
function escapeRegex(str: string) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
}
function escapeHtml(str: string) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
}

// ✅ Vista previa en tiempo real
const renderedContent = computed(() => {
  let content = template.content ?? ''
  if (Array.isArray(template.fields)) {
    template.fields.forEach((field) => {
      const key = `data.${field.name}`
      const re = new RegExp(String.raw`{{\s*${escapeRegex(field.name)}\s*}}`, 'g')
      const value = form[key]
      const safe =
        value?.trim()
          ? escapeHtml(value)
          : `<span class="text-red-500 italic">{{ ${escapeHtml(field.name)} }}</span>`
      content = content.replace(re, safe)
    })
  }
  return content
})

// ✅ Enviar datos al backend
const submit = () => {
  form.post(route('submitted-documents.store', template.id))
}
</script>

<template>
  <AppLayout title="Crear Documento">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Llenar Documento
      </h2>
    </template>

    <div class="py-8">
      <div
        class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8"
      >
        <!-- Columna izquierda: formulario -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6"
        >
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Complete los campos para la plantilla: {{ template.name }}
          </h3>

          <form @submit.prevent="submit" class="space-y-4">
            <div v-for="field in template.fields" :key="field.name">
              <Label :for="field.name" class="capitalize">
                {{ field.name.replace(/_/g, ' ') }}
              </Label>

              <!-- Input genérico -->
              <Input
                v-if="field.type !== 'textarea'"
                :id="field.name"
                v-model="form[`data.${field.name}`]"
                :type="field.type"
                class="w-full"
              />

              <!-- Textarea -->
              <textarea
                v-else
                :id="field.name"
                v-model="form[`data.${field.name}`]"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
              ></textarea>

              <!-- Errores -->
              <div
                v-if="form.errors[`data.${field.name}`]"
                class="text-red-500 text-sm mt-1"
              >
                {{ form.errors[`data.${field.name}`] }}
              </div>
            </div>

            <button
              type="submit"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              :disabled="form.processing"
            >
              Guardar Documento
            </button>
          </form>
        </div>

        <!-- Columna derecha: vista previa -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-center"
        >
          <div
            id="document-content"
            class="prose dark:prose-invert max-w-none border rounded-md"
            style="width: 210mm; min-height: 297mm; padding: 20mm;"
            v-html="renderedContent"
          ></div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
