<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Métodos de Pago', href: route('payment-methods.index') },
  { title: 'Crear', href: null },
];

const form = useForm({
  name: '',
});

function submit() {
    form.post(route('payment-methods.store'));
}
</script>

<template>
  <Head title="Crear Método de Pago" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Crear Nuevo Método de Pago</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <Label for="name">Nombre</Label>
                        <Input 
                            id="name"
                            type="text"
                            v-model="form.name"
                            placeholder="Ej. Efectivo, Tarjeta de Crédito"
                            autofocus
                        />
                        <InputError :message="form.errors.name" />
                    </div>
                    
                </CardContent>
                <CardFooter class="flex justify-end gap-4">
                    <Button variant="ghost" as-child>
                        <Link :href="route('payment-methods.index')">Cancelar</Link>
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