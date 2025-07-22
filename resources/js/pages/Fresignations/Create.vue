<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Fresignations', href: '/fresignations' },
  { title: 'Create', href: '#' },
];

// Formulario con los campos requeridos
const form = useForm({
  resignation_date: '',
  city: '',
  company_name: '', // Escrito por el usuario
  position: '',
  start_date: '',
  end_date: '',
  reason: '',
  // company_id NO se incluye porque lo asigna el backend automáticamente
});

// Acción al enviar
function submit() {
  form.post(route('fresignations.store'));
}
</script>

<template>
  <Head title="Create Fresignation" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-3xl mx-auto p-6 space-y-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create Fresignation</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="form-label">Resignation Date</label>
            <input type="date" v-model="form.resignation_date" class="form-input" />
          </div>

          <div>
            <label class="form-label">City</label>
            <input type="text" v-model="form.city" class="form-input" />
          </div>

          <div>
            <label class="form-label">Company (written manually)</label>
            <input type="text" v-model="form.company_name" class="form-input" />
          </div>

          <div>
            <label class="form-label">Position</label>
            <input type="text" v-model="form.position" class="form-input" />
          </div>

          <div>
            <label class="form-label">Start Date</label>
            <input type="date" v-model="form.start_date" class="form-input" />
          </div>

          <div>
            <label class="form-label">End Date</label>
            <input type="date" v-model="form.end_date" class="form-input" />
          </div>

          <div class="md:col-span-2">
            <label class="form-label">Reason</label>
            <textarea v-model="form.reason" class="form-textarea" rows="4"></textarea>
          </div>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="btn-primary" :disabled="form.processing">
            Save
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
