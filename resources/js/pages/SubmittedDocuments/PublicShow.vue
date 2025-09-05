<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import type { SubmittedDocument } from '@/types';
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Printer } from 'lucide-vue-next';

const props = defineProps<{
  submittedDocument: SubmittedDocument;
}>();

const template = props.submittedDocument.document_template;

// Helpers para el renderizado seguro
function escapeRegex(str: string) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function escapeHtml(str: string) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

// Lógica de renderizado mejorada
const renderedContent = computed(() => {
  let content = template?.content || '';
  const data = props.submittedDocument.data || {};

  // Primero, reemplazamos los placeholders con los datos existentes
  for (const key in data) {
    const re = new RegExp(String.raw`{{\s*${escapeRegex(key)}\s*}}`, 'g');
    const value = data[key];
    content = content.replace(re, escapeHtml(String(value)));
  }

  // Luego, los placeholders que queden se marcan como no llenados
  content = content.replace(
    /{{(.*?)}}/g,
    '<span class="text-red-500 italic">[Campo no llenado: $1]</span>'
  );

  return content;
});

const printDocument = () => {
  window.print();
};
</script>

<template>
  <Head :title="`Documento: ${template?.name || 'Documento'}`" />

  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-end items-center print:hidden">
          <Button @click="printDocument">
            <Printer class="mr-2 h-4 w-4" />
            Imprimir
          </Button>
        </div>

        <Card class="shadow-lg printable-content">
          <CardContent class="p-0">
            <div
              class="prose dark:prose-invert max-w-none p-12 border rounded-md bg-white dark:bg-gray-100 text-black"
              v-html="renderedContent"
            ></div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .printable-content,
  .printable-content * {
    visibility: visible;
  }
  .printable-content {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    margin: 0;
    padding: 0;
    border: none;
    box-shadow: none;
  }
  .prose {
    background-color: transparent !important;
  }
}
</style>