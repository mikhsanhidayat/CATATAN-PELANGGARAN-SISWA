<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import type { BreadcrumbItem } from '@/types';
import { Moon, Sun, Bell, Search, Settings } from '@lucide/vue';
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { resolvedAppearance, updateAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-4 border-b border-slate-200/80 dark:border-neutral-800 px-4 md:px-6 bg-white dark:bg-neutral-900 shadow-xs"
    >
        <!-- Kiri: Sidebar trigger, Breadcrumb, dan Search bar tipis -->
        <div class="flex items-center gap-3 min-w-0">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" class="hidden sm:flex" />
            </template>

            <!-- Search Bar Tipis seperti Referensi -->
            <div class="relative hidden md:block">
                <Search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                <input
                    type="text"
                    placeholder="Search here..."
                    class="w-48 lg:w-64 pl-8 pr-3 py-1.5 text-xs rounded-lg bg-slate-100 dark:bg-neutral-800 border-none text-slate-800 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-slate-400 dark:focus:ring-neutral-600 placeholder:text-slate-400"
                />
            </div>
        </div>

        <!-- Kanan: Icon Buttons (Theme, Settings, Notification) -->
        <div class="flex items-center gap-1.5 sm:gap-2">
            <!-- Theme Toggle -->
            <Button
                variant="ghost"
                size="icon"
                class="h-8 w-8 rounded-lg text-slate-500 hover:text-slate-800 dark:hover:text-neutral-200 hover:bg-slate-100 dark:hover:bg-neutral-800 cursor-pointer"
                @click="toggleTheme"
                :title="resolvedAppearance === 'dark' ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
            >
                <Sun v-if="resolvedAppearance === 'dark'" class="h-4 w-4" />
                <Moon v-else class="h-4 w-4" />
                <span class="sr-only">Toggle theme</span>
            </Button>

            <!-- Settings -->
            <Button
                as-child
                variant="ghost"
                size="icon"
                class="h-8 w-8 rounded-lg text-slate-500 hover:text-slate-800 dark:hover:text-neutral-200 hover:bg-slate-100 dark:hover:bg-neutral-800 cursor-pointer"
                title="Pengaturan"
            >
                <Link href="/settings/profile">
                    <Settings class="h-4 w-4" />
                    <span class="sr-only">Pengaturan</span>
                </Link>
            </Button>

            <!-- Notification dengan Dot Badge -->
            <Button
                variant="ghost"
                size="icon"
                class="relative h-8 w-8 rounded-lg text-slate-500 hover:text-slate-800 dark:hover:text-neutral-200 hover:bg-slate-100 dark:hover:bg-neutral-800 cursor-pointer"
                title="Notifikasi"
            >
                <Bell class="h-4 w-4" />
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-neutral-900"></span>
                <span class="sr-only">Notifikasi</span>
            </Button>
        </div>
    </header>
</template>
