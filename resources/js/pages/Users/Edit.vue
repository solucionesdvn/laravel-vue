<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Edit',
        href: '/users',
    },
];

const props = defineProps({
    user: Object,
    roles: Array,
    userRoles: Array,
    companies: Array
});

const form = useForm({
    "name": props.user.name,
    "email": props.user.email,
    "password": "",
    "roles": props.userRoles || [],
    "company_id": props.user.company_id || null
});

const showToast = ref(false);
const toastMessage = ref("");

const page = usePage();

function checkFlash() {
    if (page.props.flash && page.props.flash.success) {
        toastMessage.value = page.props.flash.success;
        showToast.value = true;
        setTimeout(() => {
            showToast.value = false;
        }, 3000);
    }
}

onMounted(checkFlash);
watch(() => page.props.flash.success, checkFlash);

function submit() {
    form.put(route('users.update', props.user.id));
}

</script>

<template>
    <Head title="Users Edit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Toast de éxito -->
        <div v-if="showToast" class="fixed top-5 right-5 z-50 bg-green-600 text-white px-4 py-2 rounded shadow-lg transition-all">
            {{ toastMessage }}
        </div>
        <div class="overflow-x-auto p-3 ">

            <Link 
            :href="route('users.index')"
            class="cursor-pointer px-3 py-2 text-xs font-medium text-white bg-blue-700 rounded-lg">
                
            Volver
            </Link>
            <form @submit.prevent="submit" class="space-y-6 mt-4 max-w-md mx-auto">
            <div class="grid gap-2">
                <label for="name" class="text-sm leading-none font-medium select-none peer-disabled:cu">
                Name:
                </label>
                <input
                id="name"
                name="name"
                v-model="form.name"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base sha"
                placeholder="Enter name"
                />
                <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{form.errors.name}}</p>
            </div>
                <div class="grid gap-2">
                <label for="email" class="text-sm leading-none font-medium select-none peer-disabled:cu">
                Email:
                </label>
                <input
                id="email"
                name="email"
                type="email"
                v-model="form.email"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base sha"
                placeholder="Enter email"
                />
                <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{form.errors.email}}</p>


                <div class="grid gap-2">
                    <label for="company_id" class="text-sm leading-none font-medium select-none">
                        Empresa:
                    </label>
                    <select
                        id="company_id"
                        v-model="form.company_id"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base"
                    >
                        <option value="" disabled>Selecciona una empresa</option>
                        <option
                        v-for="company in companies"
                        :key="company.id"
                        :value="company.id"
                        >
                        {{ company.name }}
                        </option>
                    </select>
                    <p v-if="form.errors.company_id" class="text-red-500 text-sm mt-1">{{ form.errors.company_id }}</p>
                </div>


            </div>
                <div class="grid gap-2">
                <label for="password" class="text-sm leading-none font-medium select-none peer-disabled:cu">
                Password:
                </label>
                <input
                id="password"
                name="password"
                type="password"
                v-model="form.password"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-base sha"
                placeholder="Enter password"
                />
                <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{form.errors.password}}</p>
            </div>

            <div class="grid gap-2">
            <label for="email" class="text-sm leading-none font-medium select-none peer-disabled:curso">
                Roles:
            </label>
            <label
                v-for="role in roles"
                class="flex items-center space-x-2"
            >
                <input
                :value="role"
                v-model="form.roles"
                type="checkbox"
                class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-2 focus:ring-blue"
                />
                <span class="text-gray-800 capitalize">{{ role }}</span>
            </label>
            <p v-if="form.errors.roles" class="text-red-500 text-sm mt-1">{{ form.errors.roles }}</p>
            </div>



            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md tra"
            >
                Submit
            </button>
            </form>
                        


        </div>

    </AppLayout>
</template>
