<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card'
import InputError from '@/components/InputError.vue'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  expense: {
    id: number;
    cash_register_id: number;
    amount: number;
    description: string;
    notes: string | null;
  };
  cashRegisters: Array<{ id: number; opened_at: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gastos', href: route('expenses.index') },
  { title: `Editar Gasto #${props.expense.id}`, href: null }
];

const form = useForm({
  _method: 'put',
  cash_register_id: props.expense.cash_register_id,
  amount: props.expense.amount,
  description: props.expense.description,
  notes: props.expense.notes || '',
})

function submit() {
  form.post(route('expenses.update', props.expense.id), {
    preserveScroll: true,
  })
}

const inputClass = "mt-1 flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50";

const formatCashRegisterDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
  <Head :title="`Editar Gasto #${expense.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 sm:p-6 max-w-2xl mx-auto">
        <form @submit.prevent="submit">
            <Card class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
                <CardHeader>
                    <CardTitle class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Editar Gasto #{{ expense.id }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <Label for="cash_register_id">Caja Registradora</Label>
                        <select id="cash_register_id" v-model="form.cash_register_id" :class="inputClass">
                            <option :value="null">-- Seleccione una caja --</option>
                            <option v-for="register in props.cashRegisters" :key="register.id" :value="register.id">
                                Caja #{{ register.id }} (Abierta desde: {{ formatCashRegisterDate(register.opened_at) }})
                            </option>
                        </select>
                        <InputError :message="form.errors.cash_register_id" />
                    </div>
                    <div>
                        <Label for="amount">Monto</Label>
                        <Input id="amount" type="number" min="0.01" step="0.01" v-model.number="form.amount" />
                        <InputError :message="form.errors.amount" />
                    </div>
                    <div>
                        <Label for="description">Descripción</Label>
                        <Input id="description" type="text" v-model="form.description" placeholder="Ej. Compra de útiles de oficina" />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div>
                        <Label for="notes">Notas Adicionales</Label>
                        <textarea id="notes" v-model="form.notes" rows="3" :class="inputClass"></textarea>
                        <InputError :message="form.errors.notes" />
                    </div>
                </CardContent>
                <CardFooter class="flex justify-end bg-gray-50 dark:bg-gray-800/50 py-4 px-6">
                    <div class="flex gap-4">
                        <Button variant="ghost" as-child>
                            <Link :href="route('expenses.index')">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Guardando...</span>
                            <span v-else>Actualizar Gasto</span>
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </form>
    </div>
  </AppLayout>
</template>