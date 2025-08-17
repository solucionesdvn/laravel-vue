<script setup lang="ts">
import { ref } from 'vue';
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

const openAccordion = ref('');

function toggleAccordion(title: string) {
    openAccordion.value = openAccordion.value === title ? '' : title;
}

function isSubmenuActive(children: NavItem[] | undefined) {
    if (!children) return false;
    return children.some(child => child.href === page.url);
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <SidebarSeparator v-if="item.type === 'separator'" class="my-2" />

                <!-- Regular Menu Item -->
                <SidebarMenuItem v-else-if="!item.children">
                    <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                        <Link :href="item.href!">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <!-- Accordion Menu Item -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        @click="toggleAccordion(item.title)"
                        :is-active="isSubmenuActive(item.children)"
                        :tooltip="item.title"
                        class="flex w-full items-center justify-between"
                    >
                        <div class="flex items-center">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </div>
                        <ChevronDown
                            class="h-4 w-4 transform transition-transform duration-200"
                            :class="{ 'rotate-180': openAccordion === item.title }"
                        />
                    </SidebarMenuButton>
                    <SidebarMenu v-if="openAccordion === item.title" class="ml-4 mt-1">
                        <SidebarMenuItem v-for="child in item.children" :key="child.title">
                            <SidebarMenuButton as-child :is-active="child.href === page.url" :tooltip="child.title">
                                <Link :href="child.href!" class="flex items-center">
                                    <component :is="child.icon" class="mr-2 h-4 w-4" v-if="child.icon" />
                                    <span>{{ child.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>