<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import type { DocumentTemplate } from '@/types'

// Props desde el backend
const props = defineProps<{ template: DocumentTemplate }>()

// Inicializamos los campos vacíos a partir de los placeholders de la plantilla
const formFields = ref<Record<string, string>>({})
const regex = /{{\s*(.*?)\s*}}/g
let match
while ((match = regex.exec(props.template.content ?? '')) !== null) {
  const field = match[1]
  if (!(field in formFields.value)) {
    formFields.value[field] = ''
  }
}

// Creamos el formulario de Inertia con esos campos
const form = useForm({ ...formFields.value })

// Vista previa en tiempo real
const renderedContent = computed(() => {
  let content = props.template.content ?? ''
  for (const key in formFields.value) {
    const regexField = new RegExp(`{{\\s*${key}\\s*}}`, 'g')
    content = content.replace(
      regexField,
      formFields.value[key]?.trim()
        ? formFields.value[key]
        : `<span class="text-red-500 italic">${key}</span>`
    )
  }
  return content
})

// Enviar datos al backend
const submit = () => {
  Object.assign(form, { ...formFields.value })
  form.post(route('submitted-documents.store', props.template.id))
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
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Complete los campos para la plantilla: {{ template.name }}
          </h3>

          <form @submit.prevent="submit">
            <div v-for="(value, key) in formFields" :key="key" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ key }}
              </label>
              <input
                v-model="formFields[key]"
                type="text"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
              />
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

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-center">
          <div
            id="document-content"
            class="prose dark:prose-invert max-w-none p-4 border rounded-md min-h-[297mm]"
            style="width: 210mm; min-height: 297mm; padding: 20mm;"
            v-html="renderedContent"
          ></div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>