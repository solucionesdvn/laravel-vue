<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardFooter,
} from "@/components/ui/card";
import InputError from "@/components/InputError.vue";
import { type BreadcrumbItem } from "@/types";

import { ref } from "vue";

// TipTap
import { EditorContent, useEditor } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Underline from "@tiptap/extension-underline";
import TextAlign from "@tiptap/extension-text-align";
import FontFamily from "@tiptap/extension-font-family";
import { TextStyle } from "@tiptap/extension-text-style";
import { Color } from "@tiptap/extension-color";
import Image from "@tiptap/extension-image";

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: "Plantillas", href: route("document-templates.index") },
  { title: "Crear Plantilla", href: null },
];

// Formulario
const form = useForm({
  name: "",
  content: "",
  fields: [] as { name: string; type: string }[],
});

// Variables dinámicas
const newFieldName = ref("");
const newFieldType = ref("text"); // tipo por defecto
const fieldTypes = [
  { label: "Texto corto", value: "text" },
  { label: "Texto largo", value: "textarea" },
  { label: "Fecha", value: "date" },
  { label: "Hora", value: "time" },
  { label: "Correo", value: "email" },
];

function addField() {
  const name = newFieldName.value.trim();
  if (!name) return;
  if (!form.fields.some(f => f.name === name)) {
    form.fields.push({ name, type: newFieldType.value });
    newFieldName.value = "";
    newFieldType.value = "text";
  }
}
function removeField(fieldName: string) {
  form.fields = form.fields.filter(f => f.name !== fieldName);
}
function insertField(fieldName: string) {
  editor.value?.chain().focus().insertContent(`{{ ${fieldName} }}`).run();
}

// Tabla
const tableSize = ref("2x2");
function insertTable() {
  if (!editor.value) return;
  const [rows, cols] = tableSize.value.split("x").map(n => parseInt(n));
  if (!rows || !cols) return;

  let tableHTML = `<table style="border-collapse: collapse;">`;
  for (let r = 0; r < rows; r++) {
    tableHTML += "<tr>";
    for (let c = 0; c < cols; c++) {
      tableHTML += `<td style="border: 2px solid green; width: 100px; height: 50px;"></td>`;
    }
    tableHTML += "</tr>";
  }
  tableHTML += "</table><p></p>";
  editor.value.chain().focus().insertContent(tableHTML).run();
}

// Editor
const editor = useEditor({
  extensions: [
    StarterKit,
    Underline,
    TextStyle,
    Color,
    FontFamily,
    TextAlign.configure({ types: ["heading", "paragraph"] }),
    Image,
  ],
  content: "<p>Escribe aquí la plantilla y usa {{ variables }} donde quieras.</p>",
  editorProps: {
    attributes: {
      class: "prose dark:prose-invert max-w-none p-6 focus:outline-none min-h-[297mm]",
    },
  },
});

// Añadir hoja
function addPage() {
  editor.value?.chain().focus().insertContent('<div style="page-break-after: always;"></div>').run();
}

// Insertar imagen
const imageUrl = ref("");
function insertImage(url: string) {
  if (!url.trim() || !editor.value) return;

  const html = `
    <div style="page-break-before: always; display: flex; justify-content: center; align-items: center; min-height: 297mm;">
      <img src="${url}" style="max-width: 100%; max-height: 100%;" />
    </div>
  `;
  editor.value.chain().focus().insertContent(html).run();
}

// Submit
function submit() {
  form.content = editor.value?.getHTML() || "";
  form.post(route("document-templates.store"), { preserveScroll: true });
}
</script>

<template>
  <Head title="Nueva Plantilla" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-7xl mx-auto">
      <form @submit.prevent="submit">
        <Card>
          <CardHeader>
            <CardTitle class="text-xl sm:text-2xl font-bold">Crear Nueva Plantilla</CardTitle>
          </CardHeader>

          <CardContent class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna izquierda: editor -->
            <div class="lg:col-span-2 space-y-6">
              <div>
                <Label for="name">Nombre</Label>
                <Input id="name" v-model="form.name" placeholder="Ej: Carta de renuncia" />
                <InputError :message="form.errors.name" />
              </div>

              <!-- Toolbar -->
              <div v-if="editor" class="flex flex-wrap items-center gap-2 border p-2 rounded-md bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                <!-- Botones y selects del editor (igual que antes) -->
                <Button size="sm" type="button" @click="editor.chain().focus().toggleBold().run()" :variant="editor?.isActive('bold') ? 'default' : 'outline'">B</Button>
                <Button size="sm" type="button" @click="editor.chain().focus().toggleItalic().run()" :variant="editor?.isActive('italic') ? 'default' : 'outline'">I</Button>
                <Button size="sm" type="button" @click="editor.chain().focus().toggleUnderline().run()" :variant="editor?.isActive('underline') ? 'default' : 'outline'">U</Button>
                <Button size="sm" type="button" @click="editor.chain().focus().toggleStrike().run()" :variant="editor?.isActive('strike') ? 'default' : 'outline'">S</Button>

                <select class="ml-2 border rounded px-2 py-1 text-sm" @change="editor.chain().focus().toggleHeading({ level: parseInt($event.target.value) }).run()">
                  <option value="0">Párrafo</option>
                  <option value="1">Título 1</option>
                  <option value="2">Título 2</option>
                  <option value="3">Título 3</option>
                </select>

                <Button size="sm" type="button" @click="editor.chain().focus().toggleBulletList().run()" :variant="editor?.isActive('bulletList') ? 'default' : 'outline'">• Lista</Button>
                <Button size="sm" type="button" @click="editor.chain().focus().toggleOrderedList().run()" :variant="editor?.isActive('orderedList') ? 'default' : 'outline'">1. Lista</Button>
                <Button size="sm" type="button" @click="editor.chain().focus().toggleBlockquote().run()" :variant="editor?.isActive('blockquote') ? 'default' : 'outline'">❝</Button>

                <Button size="sm" type="button" @click="editor.chain().focus().setTextAlign('left').run()" :variant="editor?.isActive({ textAlign: 'left' }) ? 'default' : 'outline'">⬅</Button>
                <Button size="sm" type="button" @click="editor.chain().focus().setTextAlign('center').run()" :variant="editor?.isActive({ textAlign: 'center' }) ? 'default' : 'outline'">⬍</Button>
                <Button size="sm" type="button" @click="editor.chain().focus().setTextAlign('right').run()" :variant="editor?.isActive({ textAlign: 'right' }) ? 'default' : 'outline'">➡</Button>

                <input type="color" @input="editor.chain().focus().setColor($event.target.value).run()" class="ml-2 w-8 h-8 border rounded cursor-pointer" />

                <select class="ml-2 border rounded px-2 py-1 text-sm" @change="editor.chain().focus().setFontFamily($event.target.value).run()">
                  <option value="Arial">Arial</option>
                  <option value="Georgia">Georgia</option>
                  <option value="Times New Roman">Times New Roman</option>
                  <option value="Courier New">Courier New</option>
                  <option value="Verdana">Verdana</option>
                </select>

                <select class="ml-2 border rounded px-2 py-1 text-sm" @change="editor.chain().focus().setMark('textStyle', { fontSize: $event.target.value }).run()">
                  <option value="12px">12</option>
                  <option value="14px">14</option>
                  <option value="16px">16</option>
                  <option value="18px">18</option>
                  <option value="24px">24</option>
                  <option value="32px">32</option>
                </select>

                <div class="flex items-center gap-2 ml-2">
                  <input type="text" v-model="tableSize" placeholder="2x2" class="border rounded px-2 py-1 text-sm w-16" />
                  <Button size="sm" type="button" @click="insertTable">Tabla</Button>
                </div>

                <div class="flex items-center gap-2 ml-2">
                  <Button size="sm" type="button" @click="addPage">Añadir Hoja</Button>
                  <Input v-model="imageUrl" placeholder="URL imagen" class="w-32" />
                  <Button size="sm" type="button" @click="insertImage(imageUrl.value)">Insertar Imagen</Button>
                </div>
              </div>

              <!-- Editor Hoja -->
              <div class="flex justify-center bg-gray-200 dark:bg-gray-900 p-6">
                <div class="bg-white dark:bg-gray-800 shadow-md border rounded-md w-[210mm] min-h-[297mm] p-10">
                  <EditorContent :editor="editor" />
                </div>
              </div>
              <InputError :message="form.errors.content" />
            </div>

            <!-- Columna derecha: variables dinámicas -->
            <div class="space-y-4">
              <Label>Variables dinámicas</Label>
              <div class="flex gap-2">
                <Input v-model="newFieldName" placeholder="Nombre de variable" @keyup.enter.prevent="addField" />
                <select v-model="newFieldType" class="border rounded px-2 py-1 text-sm">
                  <option v-for="type in fieldTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
                </select>
                <Button type="button" @click="addField">Agregar</Button>
              </div>

              <ul class="space-y-2">
                <li v-for="field in form.fields" :key="field.name" class="flex items-center justify-between bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-md">
                  <div>
                    <span v-html="'{{ ' + field.name + ' }}'"></span> — <em>{{ field.type }}</em>
                  </div>
                  <div class="flex gap-2">
                    <Button variant="outline" size="sm" type="button" @click="insertField(field.name)">Insertar</Button>
                    <Button variant="ghost" size="sm" type="button" @click="removeField(field.name)">✕</Button>
                  </div>
                </li>
              </ul>
              <InputError :message="form.errors.fields" />
            </div>
          </CardContent>

          <CardFooter class="flex justify-end gap-4">
            <Button variant="ghost" as-child>
              <Link :href="route('document-templates.index')">Cancelar</Link>
            </Button>
            <Button type="submit" :disabled="form.processing">
              <span v-if="form.processing">Guardando...</span>
              <span v-else>Guardar</span>
            </Button>
          </CardFooter>
        </Card>
      </form>
    </div>
  </AppLayout>
</template>