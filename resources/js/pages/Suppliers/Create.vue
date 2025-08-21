<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';

const form = useForm({
  name: '',
  contact_name: '',
  email: '',
  phone: '',
  address: '',
  nit: '',
  notes: '',
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Proveedores', href: route('suppliers.index') },
  { title: 'Crear Proveedor', href: null },
];

function submit() {
  form.post(route('suppliers.store'));
}
</script>

<template>
  <Head title="Crear Proveedor" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Crear Nuevo Proveedor</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <Label for="name">Nombre</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autofocus
                            placeholder="Nombre de la empresa o proveedor"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <Label for="contact_name">Nombre de Contacto</Label>
                        <Input
                            id="contact_name"
                            v-model="form.contact_name"
                            type="text"
                            placeholder="Ej. Juan Pérez"
                        />
                        <InputError :message="form.errors.contact_name" />
                    </div>

                    <div>
                        <Label for="email">Correo Electrónico</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="proveedor@correo.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div>
                        <Label for="phone">Teléfono</Label>
                        <Input
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            placeholder="Ej. +57 300 123 4567"
                        />
                        <InputError :message="form.errors.phone" />
                    </div>

                    <div>
                        <Label for="address">Dirección</Label>
                        <Input
                            id="address"
                            v-model="form.address"
                            type="text"
                            placeholder="Calle 123 #45-67"
                        />
                        <InputError :message="form.errors.address" />
                    </div>

                    <div>
                        <Label for="nit">NIT</Label>
                        <Input
                            id="nit"
                            v-model="form.nit"
                            type="text"
                            placeholder="900123456-7"
                        />
                        <InputError :message="form.errors.nit" />
                    </div>

                    <div>
                        <Label for="notes">Notas</Label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="4"
                            placeholder="Observaciones o detalles adicionales del proveedor"
                            class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        ></textarea>
                        <InputError :message="form.errors.notes" />
                    </div>
                </CardContent>
                <CardFooter class="flex justify-end gap-4">
                    <Button variant="ghost" as-child>
                        <Link :href="route('suppliers.index')">Cancelar</Link>
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