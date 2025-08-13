<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
  role: {
    id: number;
    name: string;
  };
  permissions: string[];
  rolePermissions: string[];
}>();

const form = useForm({
  name: props.role.name,
  permissions: props.rolePermissions || [],
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Roles', href: route('roles.index') },
  { title: 'Editar Rol', href: route('roles.edit', props.role.id) },
];

const groupedPermissions = computed(() => {
    const grouped: Record<string, string[]> = {};
    props.permissions.forEach(p => {
        const [group] = p.split('.');
        if (!grouped[group]) {
            grouped[group] = [];
        }
        grouped[group].push(p);
    });
    return grouped;
});

function submit() {
  form.put(route('roles.update', props.role.id));
}
</script>

<template>
  <Head title="Editar Rol" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-4xl mx-auto bg-white dark:bg-gray-900 rounded-lg shadow-md">
      <h1 class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 dark:text-white">Editar Rol</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Nombre del Rol -->
        <div>
          <Label for="name">Nombre del Rol</Label>
          <Input
            id="name"
            v-model="form.name"
            type="text"
            autofocus
            placeholder="Ej. Editor"
            :class="{ 'border-red-500': form.errors.name }"
          />
          <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Permisos -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white">Permisos</h3>
            <div v-if="form.errors.permissions" class="text-sm text-red-600">{{ form.errors.permissions }}</div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <fieldset v-for="(permissionGroup, groupName) in groupedPermissions" :key="groupName" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <legend class="px-2 font-medium capitalize text-gray-800 dark:text-white">{{ groupName.replace('-', ' ') }}</legend>
                    <div class="space-y-2 mt-2">
                        <div v-for="permission in permissionGroup" :key="permission" class="flex items-center">
                            <input
                                :id="permission"
                                :value="permission"
                                v-model="form.permissions"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <Label :for="permission" class="ml-3 text-sm text-gray-600 dark:text-gray-300">{{ permission.split('.')[1] }}</Label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4 pt-4">
          <Button variant="ghost" as-child>
            <Link :href="route('roles.index')">Cancelar</Link>
          </Button>
          <Button type="submit" :disabled="form.processing">
            <span v-if="form.processing">Actualizando...</span>
            <span v-else>Actualizar Rol</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>