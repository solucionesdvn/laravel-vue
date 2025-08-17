<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gestión de Caja', href: route('cash-registers.index') },
  { title: 'Abrir Caja', href: null },
];

const form = useForm({
  opening_amount: '0',
  notes: '',
});

function submit() {
    form.post(route('cash-registers.store'));
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Abrir Caja" />

    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
        <form @submit.prevent="submit">
            <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
                <CardHeader>
                    <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Abrir Nueva Caja</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <Label for="opening_amount">Monto Inicial</Label>
                        <Input 
                            id="opening_amount"
                            type="number"
                            step="0.01"
                            v-model="form.opening_amount"
                            placeholder="0.00"
                            autofocus
                        />
                        <InputError :message="form.errors.opening_amount" />
                    </div>
                    <div>
                        <Label for="notes">Notas (opcional)</Label>
                        <textarea 
                            id="notes"
                            v-model="form.notes"
                            rows="4"
                            class="mt-1 flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring"
                        ></textarea>
                        <InputError :message="form.errors.notes" />
                    </div>
                </CardContent>
                <CardFooter class="flex justify-end bg-gray-50 dark:bg-gray-800/50 py-4 px-6">
                    <div class="flex gap-4">
                        <Button variant="ghost" as-child>
                            <Link :href="route('cash-registers.index')">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Abriendo...</span>
                            <span v-else>Abrir Caja</span>
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </form>
    </div>
  </AppLayout>
</template>