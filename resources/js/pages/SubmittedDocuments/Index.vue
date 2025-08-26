<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { Button } from "@/components/ui/button";
import { ref } from "vue";
import { type BreadcrumbItem } from "@/types";

// Props desde el backend
const props = defineProps<{
  submittedDocuments?: {
    data: any[];
    meta: any;
  };
  templates?: {
    id: number;
    name: string;
  }[];
}>();

// Evitar errores si vienen vacíos
const submittedDocuments = ref(props.submittedDocuments?.data || []);
const templates = ref(props.templates || []);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: "Documentos", href: route("submitted-documents.index") },
];
</script>

<template>
  <Head title="Documentos Enviados" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-7xl mx-auto">
      <!-- Botones para crear documento desde plantilla -->
      <div class="flex flex-wrap gap-2 mb-6">
        <span class="font-semibold mr-2">Usar plantilla:</span>
        <Button
          v-for="template in templates"
          :key="template.id"
          as-child
          size="sm"
        >
          <Link :href="route('submitted-documents.create', template.id)">
            {{ template.name }}
          </Link>
        </Button>
      </div>

      <!-- Tabla de documentos enviados -->
      <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-md rounded-md border">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">#</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Plantilla</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Enviado por</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="(doc, index) in submittedDocuments" :key="doc.id">
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ doc.document_template.name }}</td>
              <td class="px-4 py-2">{{ doc.submitted_by_user?.name || 'Anónimo' }}</td>
              <td class="px-4 py-2">{{ new Date(doc.created_at).toLocaleString() }}</td>
              <td class="px-4 py-2">
                <Button size="sm" as-child>
                  <Link :href="route('submitted-documents.show', doc.id)">Ver</Link>
                </Button>
              </td>
            </tr>

            <tr v-if="submittedDocuments.length === 0">
              <td class="px-4 py-2 text-center" colspan="5">No hay documentos enviados.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="mt-4">
        <Link
          v-if="props.submittedDocuments?.meta?.prev_page_url"
          :href="props.submittedDocuments.meta.prev_page_url"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded mr-2"
        >
          Anterior
        </Link>
        <Link
          v-if="props.submittedDocuments?.meta?.next_page_url"
          :href="props.submittedDocuments.meta.next_page_url"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded"
        >
          Siguiente
        </Link>
      </div>
    </div>
  </AppLayout>
</template>