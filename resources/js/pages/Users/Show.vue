<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Show',
        href: '/users',
    },
];

const props = defineProps({
  user: Object
});

</script>

<template>
    <Head title="Users Show" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3 ">
            <Link 
            :href="route('users.index')"
            class="cursor-pointer px-3 py-2 text-xs font-medium text-white bg-blue-700 rounded-lg">
            Volver
            </Link>
            <div>
                <p><strong>Name</strong>{{ user.name }}</p>
                <p><strong>Name</strong>{{ user.email }}</p>

            </div>
        </div>
    </AppLayout>
</template>
