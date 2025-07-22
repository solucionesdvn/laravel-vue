<script setup lang="ts">
import DeleteUser from '@/components/DeleteUser.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { can } from '@/lib/can';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];

defineProps({
    roles : Array
});

function deleteRole(id) {
  if (confirm("Are you sure you want to delete this role?")) {
    router.delete(route('roles.destroy', id));
  }
}


</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="overflow-x-auto p-3 ">

            <Link
            v-if="can('roles.create')"
            :href="route('roles.create')"
            class="cursor-pointer px-3 py-2 text-xs font-medium text-white bg-blue-700 rounded-lg">
                
            Create
            </Link>

            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">permissions</th>
                    <th scope="col" class="px-6 w-70 py-3">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="role in roles" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800">
                    <td class="px-6 py-2 font-medium text-gray-900 dark:text-white">{{ role.id }}</td>
                    <td class="px-6 py-2 text-gray-600 dark:text-gray-300">{{ role.name }}</td>
                    <td class="px-6 py-2 text-gray-600 dark:text-gray-300">
                        <span
                            v-for="permission in role.permissions"
                            key="1"
                            class="mr-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 roun"
                        >
                            {{ permission.name }}<br>
                        </span>
                    </td>

                    <td class="px-6 py-2">
                    <Link
                        :href="route('roles.show', role.id)"
                        class="mr-1 cursor-pointer px-3 py-2 text-xs font-medium text-white bg-gray-700">
                        Show
                    </Link>

                    <Link
                        v-if="can('roles.edit')"
                        :href="route('roles.edit', role.id)"
                        class="cursor-pointer px-3 py-2 text-xs font-medium text-white bg-blue-700">
                        Edit
                    </Link>
                    <button
                        v-if="can('roles.delete')"
                        @click="deleteRole(role.id)"
                        class="cursor-pointer px-3 py-2 text-xs font-medium text-white bg-red-700">
                            Delete
                    </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </AppLayout>
</template>
