<script setup lang="ts">
import { Head, useForm } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import TemplateForm from "./Partials/TemplateForm.vue";
import { type BreadcrumbItem, type DocumentTemplate } from "@/types";

// Props
const props = defineProps<{
  template: DocumentTemplate;
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: "Plantillas", href: route("document-templates.index") },
  { title: "Editar Plantilla", href: null },
];

// Formulario
const form = useForm({
  name: props.template.name,
  description: props.template.description ?? '',
  content: props.template.content,
  fields: props.template.fields ?? [],
});

// Submit
function submit() {
  form.put(route("document-templates.update", props.template.id), { preserveScroll: true });
}
</script>

<template>
  <Head title="Editar Plantilla" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-7xl mx-auto">
        <TemplateForm :form="form" @submit="submit" :is-updating="true" />
    </div>
  </AppLayout>
</template>
