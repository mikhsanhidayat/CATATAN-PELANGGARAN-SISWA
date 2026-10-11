
<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown } from '@lucide/vue';

import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubItem,
    SidebarMenuSubButton,
} from '@/components/ui/sidebar';

import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

type MenuItem = NavItem & {
    children?: NavItem[];
};

defineProps<{
    items: MenuItem[];
}>();

const page = usePage();
const { isCurrentUrl, isCurrentOrParentUrl } = useCurrentUrl();

// Mengambil path URL tanpa query parameter.
const currentPath = computed(() => page.url.split('?')[0]);

// Memeriksa apakah salah satu submenu sedang aktif.
const isChildActive = (item: MenuItem) => {
    return (
        item.children?.some(
            (child) =>
                child.href !== '#' &&
                (currentPath.value === child.href ||
                    currentPath.value.startsWith(`${child.href}/`)),
        ) ?? false
    );
};

// Membuka Data Master secara otomatis ketika berada di halaman Users.
const masterOpen = ref(
    currentPath.value === '/users' ||
        currentPath.value.startsWith('/users/'),
);

// Membuka dan menutup submenu Data Master.
const toggleSubmenu = () => {
    masterOpen.value = !masterOpen.value;
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel
            class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"
        >
            Platform
        </SidebarGroupLabel>

        <SidebarMenu class="mt-1 space-y-1">
            <SidebarMenuItem
                v-for="item in items"
                :key="item.title"
            >
                <!-- Menu yang mempunyai submenu -->
                <template v-if="item.children?.length">
                    <SidebarMenuButton
                        :is-active="isChildActive(item)"
                        :tooltip="item.title"
                        :aria-expanded="masterOpen"
                        class="rounded-lg text-slate-600 transition-all duration-150 hover:bg-slate-50 hover:text-slate-900 data-[active=true]:bg-blue-50 data-[active=true]:font-semibold data-[active=true]:text-blue-700 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100 dark:data-[active=true]:bg-blue-950/40 dark:data-[active=true]:text-blue-400"
                        @click="toggleSubmenu"
                    >
                        <component
                            :is="item.icon"
                            class="h-4 w-4"
                        />

                        <span class="flex-1 text-left">
                            {{ item.title }}
                        </span>

                        <ChevronDown
                            class="ml-auto h-4 w-4 transition-transform duration-200"
                            :class="{ 'rotate-180': masterOpen }"
                        />
                    </SidebarMenuButton>

                    <!-- Submenu Data Master -->
                    <SidebarMenuSub
                        v-show="masterOpen"
                        class="mt-1 group-data-[collapsible=icon]:hidden"
                    >
                        <SidebarMenuSubItem
                            v-for="child in item.children"
                            :key="child.title"
                        >
                            <!-- Submenu yang memiliki tujuan URL -->
                            <SidebarMenuSubButton
                                v-if="child.href !== '#'"
                                as-child
                                :is-active="isCurrentOrParentUrl(child.href)"
                                class="rounded-lg text-slate-600 transition-all duration-150 hover:bg-slate-50 hover:text-slate-900 data-[active=true]:bg-blue-50 data-[active=true]:font-semibold data-[active=true]:text-blue-700 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100 dark:data-[active=true]:bg-blue-950/40 dark:data-[active=true]:text-blue-400"
                            >
                                <Link
                                    :href="child.href"
                                    class="flex items-center gap-2.5"
                                >
                                    <component
                                        :is="child.icon"
                                        class="h-4 w-4"
                                    />

                                    <span>
                                        {{ child.title }}
                                    </span>
                                </Link>
                            </SidebarMenuSubButton>

                            <!-- Submenu yang belum memiliki halaman -->
                            <SidebarMenuSubButton
                                v-else
                                aria-disabled="true"
                                class="cursor-not-allowed rounded-lg text-slate-400 opacity-60 dark:text-neutral-500"
                            >
                                <component
                                    :is="child.icon"
                                    class="h-4 w-4"
                                />

                                <span>
                                    {{ child.title }}
                                </span>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                    </SidebarMenuSub>
                </template>

                <!-- Menu biasa tanpa submenu -->
                <template v-else>
                    <SidebarMenuButton
                        v-if="item.href !== '#'"
                        as-child
                        :is-active="isCurrentUrl(item.href)"
                        :tooltip="item.title"
                        class="rounded-lg text-slate-600 transition-all duration-150 hover:bg-slate-50 hover:text-slate-900 data-[active=true]:bg-blue-50 data-[active=true]:font-semibold data-[active=true]:text-blue-700 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100 dark:data-[active=true]:bg-blue-950/40 dark:data-[active=true]:text-blue-400"
                    >
                        <Link
                            :href="item.href"
                            class="flex items-center gap-2.5"
                        >
                            <component
                                :is="item.icon"
                                class="h-4 w-4"
                            />

                            <span>
                                {{ item.title }}
                            </span>
                        </Link>
                    </SidebarMenuButton>

                    <!-- Menu yang belum memiliki halaman -->
                    <SidebarMenuButton
                        v-else
                        disabled
                        :tooltip="item.title"
                        class="rounded-xl opacity-60"
                    >
                        <component
                            :is="item.icon"
                            class="h-4 w-4"
                        />

                        <span>
                            {{ item.title }}
                        </span>
                    </SidebarMenuButton>
                </template>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
