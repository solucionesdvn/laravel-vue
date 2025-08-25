<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Building,
    Contact,
    FileText,
    Files,
    Folder,
    LayoutGrid,
    LogIn,
    LogOut,
    Package,
    Shield,
    ShoppingCart,
    Tags,
    Truck,
    User,
    Users,
    Wallet,
    CircleDollarSign,
} from 'lucide-vue-next';

import AppLogo from './AppLogo.vue';

const page = usePage();

const hasPermission = (perm: string): boolean => {
    return page.props.auth.permissions?.includes(perm);
};

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Employees',
        href: '/employees',
        icon: Users,
        permission: 'employee.view',
    },
    {
        title: 'Users',
        href: '/users',
        icon: Users,
        permission: 'users.view',
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: Shield,
        permission: 'roles.view',
    },
    {
        title: 'Empresas',
        href: '/companies',
        icon: Building,
        permission: 'companies.view',
    },
    { type: 'separator', title: 'separator-1' },
    {
        title: 'Categorias',
        href: '/categories',
        icon: Tags,
        permission: 'categories.view',
    },
    {
        title: 'Proveedores',
        href: '/suppliers',
        icon: Truck,
        permission: 'suppliers.view',
    },
    {
        title: 'Clientes',
        href: '/clients',
        icon: Contact,
        permission: 'clients.view',
    },
    {
        title: 'Productos',
        href: '/products',
        icon: Package,
        permission: 'products.view',
    },
    {
        title: 'Entradas',
        href: '/entries',
        icon: LogIn,
        permission: 'entries.view',
    },
    {
        title: 'Salidas',
        href: '/product-exits',
        icon: LogOut,
        permission: 'product-exits.view',
    },
    {
        title: 'Caja',
        href: '/cash-registers',
        icon: Wallet,
        permission: 'cash-registers.view',
    },
    {
        title: 'Metodos de pago',
        href: '/payment-methods',
        icon: CircleDollarSign,
        permission: 'payment-methods.view',
    },

    {
        title: 'Ventas',
        href: '/sales',
        icon: ShoppingCart,
        permission: 'sales.view',
    },
    { type: 'separator', title: 'separator-1' },

    {
        title: 'Carta R',
        href: '/resignation-forms',
        icon: Files,
        permission: 'resignation-forms.view',
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
