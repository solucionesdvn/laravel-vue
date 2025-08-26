<script setup lang="ts">
import { Head, useForm } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { type BreadcrumbItem } from "@/types";
import { ref } from "vue";

// Props
const props = defineProps<{
  template: {
    id: number;
    name: string;
    fields: { name: string; type: string }[]; // ahora cada campo tiene tipo
  };
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: "Plantillas", href: route("document-templates.index") },
  { title: `Usar: ${props.template.name}`, href: null },
];

// Formulario
const form = useForm({
  data: {} as Record<string, any>,
});

// Inicializar campos
props.template.fields.forEach(field => {
  form.data[field.name] = "";
});

// Submit
function submit() {
  form.post(route("submitted-documents.store", props.template.id), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head :title="`Usar plantilla: ${props.template.name}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-3xl mx-auto">
      <h2 class="text-xl font-bold mb-4">Completa los campos</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <div v-for="field in props.template.fields" :key="field.name" class="space-y-1">
          <Label :for="field.name">{{ field.name }}</Label>
          <component
            :is="field.type === 'textarea' ? 'textarea' : 'input'"
            v-model="form.data[field.name]"
            :type="field.type !== 'textarea' ? field.type : undefined"
            class="border rounded w-full px-2 py-1"
            :placeholder="field.name"
          />
        </div>

        <Button type="submit" :disabled="form.processing">
          <span v-if="form.processing">Enviando...</span>
          <span v-else>Enviar</span>
        </Button>
      </form>
    </div>
  </AppLayout>
</template>