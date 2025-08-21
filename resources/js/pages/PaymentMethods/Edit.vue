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

const props = defineProps<{
    paymentMethod: {
        id: number;
        name: string;
    };
}>();

const form = useForm({
  name: props.paymentMethod.name,
});

function submit() {
    form.put(route('payment-methods.update', props.paymentMethod.id));
}
</script>

<template>
  <Head title="Editar Método de Pago" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Editar Método de Pago</CardTitle>
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
                        <span v-else>Guardar Cambios</span>
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </div>
  </AppLayout>
</template>