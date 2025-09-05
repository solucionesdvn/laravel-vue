<script setup lang="ts">
import { Head, useForm } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import TemplateForm from "./Partials/TemplateForm.vue";
import { type BreadcrumbItem } from "@/types";

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: "Plantillas", href: route("document-templates.index") },
  { title: "Crear Plantilla", href: null },
];

// Formulario
const form = useForm({
  name: "",
  description: "",
  content: "<p>Escribe aquí la plantilla y usa {{ variables }} donde quieras.</p>",
  fields: [] as { name: string; type: string }[],
});

// Submit
function submit() {
  form.post(route("document-templates.store"), { preserveScroll: true });
}
</script>

<template>
  <Head title="Nueva Plantilla" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-7xl mx-auto">
        <TemplateForm :form="form" @submit="submit" />
    </div>
  </AppLayout>
</template>
