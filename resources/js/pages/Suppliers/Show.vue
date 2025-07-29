<script setup lang="ts">
import { watch } from 'vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const props = defineProps({
  supplier: Object,
  show: Boolean
});

const emit = defineEmits(['close']);

watch(() => props.show, (val) => {
  if (!val) emit('close');
});
</script>

<template>
  <Dialog :open="show" @update:open="emit('close')">
    <DialogContent class="max-w-md">
      <DialogHeader>
        <DialogTitle class="text-lg font-semibold text-gray-800 dark:text-white">
          Información del Proveedor
        </DialogTitle>
      </DialogHeader>

      <div class="mt-4 space-y-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
          <span class="font-medium text-gray-900 dark:text-white">Nombre:</span>
          <span class="ml-1">{{ supplier?.name || '—' }}</span>
        </div>

        <div>
          <span class="font-medium text-gray-900 dark:text-white">Nombre de Contacto:</span>
          <span class="ml-1">{{ supplier?.contact_name || '—' }}</span>
        </div>

        <div>
          <span class="font-medium text-gray-900 dark:text-white">Correo Electrónico:</span>
          <span class="ml-1">{{ supplier?.email || '—' }}</span>
        </div>

        <div>
          <span class="font-medium text-gray-900 dark:text-white">Teléfono:</span>
          <span class="ml-1">{{ supplier?.phone || '—' }}</span>
        </div>

        <div>
          <span class="font-medium text-gray-900 dark:text-white">Dirección:</span>
          <span class="ml-1">{{ supplier?.address || '—' }}</span>
        </div>

        <div>
          <span class="font-medium text-gray-900 dark:text-white">NIT:</span>
          <span class="ml-1">{{ supplier?.nit || '—' }}</span>
        </div>

        <div>
          <span class="font-medium text-gray-900 dark:text-white">Notas:</span>
          <span class="ml-1 whitespace-pre-line">{{ supplier?.notes || '—' }}</span>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
