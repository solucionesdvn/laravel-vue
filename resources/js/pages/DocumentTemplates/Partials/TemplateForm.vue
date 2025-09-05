<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import InputError from "@/components/InputError.vue";

import { ref, watch } from "vue";

// TipTap
import { EditorContent, useEditor } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Underline from "@tiptap/extension-underline";
import TextAlign from "@tiptap/extension-text-align";
import FontFamily from "@tiptap/extension-font-family";
import { TextStyle } from "@tiptap/extension-text-style";
import { Color } from "@tiptap/extension-color";

// Props & Emits
const props = defineProps<{
  form: any; // Inertia Form
  isUpdating?: boolean;
}>();

const emit = defineEmits(['submit']);

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
  if (!props.form.fields.some(f => f.name === name)) {
    props.form.fields.push({ name, type: newFieldType.value });
    newFieldName.value = "";
    newFieldType.value = "text";
  }
}
function removeField(fieldName: string) {
  props.form.fields = props.form.fields.filter(f => f.name !== fieldName);
}
function insertField(fieldName: string) {
  editor.value?.chain().focus().insertContent(`{{ ${fieldName} }}`).run();
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
  ],
  content: props.form.content,
  editorProps: {
    attributes: {
      class: "prose dark:prose-invert max-w-none p-6 focus:outline-none min-h-[297mm] break-words",
    },
  },
});

// Sincronizar el contenido del editor con el formulario
watch(() => props.form.content, (newContent) => {
  if (editor.value && editor.value.getHTML() !== newContent) {
    editor.value.commands.setContent(newContent);
  }
});

// Submit
function submit() {
  props.form.content = editor.value?.getHTML() || "";
  emit('submit');
}
</script>

<template>
  <form @submit.prevent="submit">
    <Card>
      <CardHeader class="flex justify-between items-center">
        <CardTitle class="text-xl sm:text-2xl font-bold">
          <span v-if="isUpdating">Editar Plantilla</span>
          <span v-else>Crear Nueva Plantilla</span>
        </CardTitle>
        <div class="flex justify-end gap-4">
          <Button variant="ghost" as-child>
            <Link :href="route('document-templates.index')">Cancelar</Link>
          </Button>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Guardando...</span>
            <span v-else>{{ isUpdating ? 'Actualizar' : 'Guardar' }}</span>
          </Button>
        </div>
      </CardHeader>

      <CardContent class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna izquierda: editor -->
        <div class="lg:col-span-2 space-y-6">
          <div>
            <Label for="name">Nombre</Label>
            <Input id="name" v-model="form.name" placeholder="Ej: Carta de renuncia" />
            <InputError :message="form.errors.name" />
          </div>

          <div>
            <Label for="description">Descripción</Label>
            <textarea id="description" v-model="form.description" placeholder="Añade una breve descripción de la plantilla" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"></textarea>
            <InputError :message="form.errors.description" />
          </div>

          <!-- Toolbar -->
          <div v-if="editor" class="flex flex-wrap items-center gap-2 border p-2 rounded-md bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm">Estilo</Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem @click="editor.chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }">Negrita</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }">Cursiva</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().toggleUnderline().run()" :class="{ 'is-active': editor.isActive('underline') }">Subrayado</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().toggleStrike().run()" :class="{ 'is-active': editor.isActive('strike') }">Tachado</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm">Encabezado</Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem @click="editor.chain().focus().setParagraph().run()" :class="{ 'is-active': editor.isActive('paragraph') }">Párrafo</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }">Título 1</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }">Título 2</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }">Título 3</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <div class="flex items-center gap-2 border-r pr-2">
              <Button size="sm" type="button" @click="editor.chain().focus().toggleBulletList().run()" :variant="editor?.isActive('bulletList') ? 'default' : 'outline'">• Lista</Button>
              <Button size="sm" type="button" @click="editor.chain().focus().toggleOrderedList().run()" :variant="editor?.isActive('orderedList') ? 'default' : 'outline'">1. Lista</Button>
              <Button size="sm" type="button" @click="editor.chain().focus().toggleBlockquote().run()" :variant="editor?.isActive('blockquote') ? 'default' : 'outline'">❝</Button>
            </div>

            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm">Alinear</Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem @click="editor.chain().focus().setTextAlign('left').run()" :class="{ 'is-active': editor.isActive({ textAlign: 'left' }) }">Izquierda</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setTextAlign('center').run()" :class="{ 'is-active': editor.isActive({ textAlign: 'center' }) }">Centro</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setTextAlign('right').run()" :class="{ 'is-active': editor.isActive({ textAlign: 'right' }) }">Derecha</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm">Fuente</Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem @click="editor.chain().focus().setFontFamily('Arial').run()">Arial</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setFontFamily('Georgia').run()">Georgia</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setFontFamily('Times New Roman').run()">Times New Roman</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setFontFamily('Courier New').run()">Courier New</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setFontFamily('Verdana').run()">Verdana</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm">Tamaño</Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem @click="editor.chain().focus().setMark('textStyle', { fontSize: '12px' }).run()">12px</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setMark('textStyle', { fontSize: '14px' }).run()">14px</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setMark('textStyle', { fontSize: '16px' }).run()">16px</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setMark('textStyle', { fontSize: '18px' }).run()">18px</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setMark('textStyle', { fontSize: '24px' }).run()">24px</DropdownMenuItem>
                <DropdownMenuItem @click="editor.chain().focus().setMark('textStyle', { fontSize: '32px' }).run()">32px</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <input type="color" @input="editor.chain().focus().setColor($event.target.value).run()" class="w-8 h-8 border rounded cursor-pointer" :value="editor.getAttributes('textStyle').color" />

          </div>

          <!-- Editor Hoja -->
          <div class="flex justify-center bg-gray-200 dark:bg-gray-900 p-6">
            <div class="bg-white dark:bg-gray-800 shadow-md border rounded-md w-[210mm] min-h-[297mm] p-10 break-words">
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
                <span v-html="'{{ ' + field.name + ' }}'" :title="'Esta variable se reemplazará con su valor al generar el documento.'"></span> — <em>{{ field.type }}</em>
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
    </Card>
  </form>
</template>