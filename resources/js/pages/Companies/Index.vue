<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'

defineProps<{
  companies: Array<{
    id: number
    name: string
    nit: string | null
    address: string | null
  }>
}>()

const deleteCompany = (id: number) => {
  if (confirm('¿Estás seguro de eliminar esta empresa?')) {
    router.delete(route('companies.destroy', id))
  }
}
</script>

<template>
  <Head title="Empresas" />

  <AppLayout>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Empresas</h1>
      <Link :href="route('companies.create')">
        <Button>Crear empresa</Button>
      </Link>
    </div>

    <div class="overflow-auto bg-white rounded-xl shadow p-4">
      <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="text-left text-sm font-semibold border-b">
            <th class="p-2">Nombre</th>
            <th class="p-2">NIT</th>
            <th class="p-2">Dirección</th>
            <th class="p-2 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="company in companies" :key="company.id" class="border-b hover:bg-gray-50">
            <td class="p-2">{{ company.name }}</td>
            <td class="p-2">{{ company.nit ?? '-' }}</td>
            <td class="p-2">{{ company.address ?? '-' }}</td>
            <td class="p-2 text-right space-x-2">
              <Link :href="route('companies.edit', company.id)">
                <Button variant="outline" size="sm">Editar</Button>
              </Link>
              <Button variant="destructive" size="sm" @click="() => deleteCompany(company.id)">
                Eliminar
              </Button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="companies.length === 0" class="text-center text-gray-500 py-8">
        No hay empresas registradas.
      </div>
    </div>
  </AppLayout>
</template>