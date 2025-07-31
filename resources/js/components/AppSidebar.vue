<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Notebook, User, Users, Blinds, Bus} from 'lucide-vue-next';


import { usePage } from '@inertiajs/vue3';

const page = usePage();

const hasPermission = (perm: string): boolean => {
  return page.props.auth.permissions?.includes(perm);
};



import AppLogo from './AppLogo.vue';


const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
     {
        title: 'Employees',
        href: '/employees',
        icon: User ,
        permission: 'employee.view',

    },   
    {
        title: 'Users',
        href: '/users',
        icon: Users ,
        permission: 'users.view',

    },  

    {
        title: 'Roles',
        href: '/roles',
        icon: Notebook,
        permission: 'roles.view',
    },

    {
        title: 'Empresas',
        href: '/companies',
        icon: Notebook,
        permission: 'companies.view',
    },
    {
        //title: 'Formato Carta renuncia',
        //href: '/fresignations',
        //icon: Notebook,
        //permission: 'fresignations.view',
    },
    {
        title: 'Categorias',
        href: '/categories',
        icon: Blinds,
        permission: 'categories.view',
    },
    {
        title: 'Proveedores',
        href: '/suppliers',
        icon: Bus,
        permission: 'suppliers.view',
    },
    {
        title: 'Productos',
        href: '/products',
        icon: Bus,
        permission: 'products.view',
    },
    {
        title: 'Entradas',
        href: '/entries',
        icon: Bus,
        permission: 'entries.view',
    },

    {
        title: 'Salidas',
        href: '/product-exits',
        icon: Bus,
        permission: 'product-exits.view',
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

const visibleMainNavItems = mainNavItems.filter(item => {
    return !item.permission || hasPermission(item.permission);
});

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="visibleMainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
