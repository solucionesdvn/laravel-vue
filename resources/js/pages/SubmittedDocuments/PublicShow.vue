<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { SubmittedDocument, DocumentTemplateField } from '@/types';
import { useForm, Head } from '@inertiajs/vue3';

const props = defineProps<{
  submittedDocument: SubmittedDocument;
}>();

const template = props.submittedDocument.document_template;

// --- Inicialización Plana del Formulario ---
const initializeData = () => {
  const initialData: Record<string, any> = {};
  if (template?.fields) {
    template.fields.forEach((field: DocumentTemplateField) => {
      initialData[field.name] = props.submittedDocument.data?.[field.name] || '';
    });
  }
  return initialData;
};

// El formulario ahora es plano, sin el objeto 'data' anidado.
const form = useForm(initializeData());
// --- Fin de la lógica de inicialización ---

const getFieldComponent = (type: string) => {
  switch (type) {
    case 'date':
    case 'email':
    case 'time':
      return Input;
    default:
      return Input;
  }
};

const getFieldType = (type: string) => {
    switch (type) {
        case 'date': return 'date';
        case 'email': return 'email';
        case 'time': return 'time';
        default: return 'text';
    }
}

const submit = () => {
  form.put(route('public.submitted-documents.update', props.submittedDocument.token), {
    onSuccess: () => {
      alert('¡Documento guardado exitosamente!');
    },
    onError: (errors) => {
      console.error('Errores de validación:', errors);
      alert('Error al guardar. Por favor, revisa los campos marcados e intenta de nuevo.');
    },
  });
};
</script>

<template>
  <Head :title="`Llenar: ${template?.name || 'Documento'}`" />

  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center py-12">
    <div class="mx-auto w-full max-w-2xl sm:px-6 lg:px-8">
      <form @submit.prevent="submit">
        <Card class="shadow-lg">
          <CardHeader>
            <CardTitle>{{ template.name }}</CardTitle>
            <CardDescription>
              {{ template.description || 'Por favor, completa los siguientes campos.' }}
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-6">
            <div
              v-for="field in template.fields"
              :key="field.name"
              class="space-y-2"
            >
              <Label :for="field.name">{{ field.label || field.name }}</Label>

              <!-- v-model ahora apunta directamente a form[field.name] -->
              <textarea
                v-if="field.type === 'textarea'"
                :id="field.name"
                v-model="form[field.name]"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                :class="{ 'border-red-500': form.errors[field.name] }"
              ></textarea>

              <component
                v-else
                :is="getFieldComponent(field.type)"
                :id="field.name"
                :type="getFieldType(field.type)"
                v-model="form[field.name]"
                class="w-full"
                :class="{ 'border-red-500': form.errors[field.name] }"
              />

              <p v-if="form.errors[field.name]" class="text-sm text-red-600">
                {{ form.errors[field.name] }}
              </p>
            </div>

            <div class="flex justify-end pt-4">
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Guardando...' : 'Guardar Documento' }}
              </Button>
            </div>
          </CardContent>
        </Card>
      </form>
       <div class="mt-4 text-center text-sm text-gray-500">
          <a :href="route('public.submitted-documents.pdf', submittedDocument.token)" target="_blank" class="underline">
              Ver y descargar el documento en PDF
          </a>
      </div>
    </div>
  </div>
</template>