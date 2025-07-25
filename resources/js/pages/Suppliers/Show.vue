<!-- resources/js/Pages/Suppliers/Show.vue -->
<script setup lang="ts">
import { ref, watch } from 'vue';
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
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Información del Proveedor</DialogTitle>
      </DialogHeader>

      <div class="space-y-2">
        <p><strong>Nombre:</strong> {{ supplier?.name }}</p>
        <p><strong>Contacto:</strong> {{ supplier?.contact_name }}</p>
        <p><strong>Email:</strong> {{ supplier?.email }}</p>
        <p><strong>Teléfono:</strong> {{ supplier?.phone }}</p>
        <p><strong>Dirección:</strong> {{ supplier?.address }}</p>
        <p><strong>NIT:</strong> {{ supplier?.nit }}</p>
        <p><strong>Notas:</strong> {{ supplier?.notes }}</p>
      </div>
    </DialogContent>
  </Dialog>
</template>
