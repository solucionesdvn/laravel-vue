<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  nit: '',
  address: '',
})

const submit = () => {
  form.post(route('companies.store'), {
    onSuccess: () => {
      form.reset()
    },
  })
}
</script>

<template>
  <Head title="Crear Empresa" />

  <AppLayout>
    <div class="max-w-xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Crear Empresa</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <Label for="name">Nombre</Label>
          <Input id="name" v-model="form.name" type="text" />
          <div v-if="form.errors.name" class="text-sm text-red-500 mt-1">{{ form.errors.name }}</div>
        </div>

        <div>
          <Label for="nit">NIT</Label>
          <Input id="nit" v-model="form.nit" type="text" />
          <div v-if="form.errors.nit" class="text-sm text-red-500 mt-1">{{ form.errors.nit }}</div>
        </div>

        <div>
          <Label for="address">Dirección</Label>
          <Input id="address" v-model="form.address" type="text" />
          <div v-if="form.errors.address" class="text-sm text-red-500 mt-1">{{ form.errors.address }}</div>
        </div>

        <div class="flex justify-end">
          <Button type="submit" :disabled="form.processing">Guardar</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>