<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Show',
        href: '/roles',
    },
];

const props = defineProps({
    "role": Object,
    "rolePermissions": Array,

})
s</script>
<template>
    <Head title="Roles Show" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3 ">
            <Link 
            :href="route('roles.index')"
            class="cursor-pointer px-3 py-2 text-xs font-medium text-white bg-blue-700 rounded-lg">
            Volver
            </Link>
            <div>
                <p><strong>Name:</strong> {{ role.name }}</p>
                <p><strong>Permissions:</strong>
                    <span
                    v-for="permission in rolePermissions"
                    key="1"
                    class="mr-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded"
                    >
                    {{ permission }}
                    </span>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
