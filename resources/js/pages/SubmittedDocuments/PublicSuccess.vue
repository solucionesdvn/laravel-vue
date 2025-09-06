<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CheckCircle } from 'lucide-vue-next';
</script>

<template>
    <Head title="Documento Enviado" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center text-center">
        <div class="mx-auto w-full max-w-md p-8">
            <CheckCircle class="mx-auto h-16 w-16 text-green-500" />
            <h1 class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">¡Gracias!</h1>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                Tu documento ha sido guardado y enviado correctamente.
            </p>
            <p class="mt-2 text-sm text-gray-500">
                Ya puedes cerrar esta ventana.
            </p>
        </div>
    </div>
</template>
