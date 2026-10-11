
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Users,
    AlertTriangle,
    FileText,
    Settings,
    LayoutGrid,
    Database,
    ShieldCheck,
    BriefcaseBusiness,
    CalendarDays,
    GraduationCap,
    School,
    ListChecks,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

type MenuItem = NavItem & {
    children?: NavItem[];
};

const mainNavItems: MenuItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Data Master',
        href: '#',
        icon: Database,
        children: [
            { title: 'Users', href: '/users', icon: Users },
            { title: 'Role', href: '#', icon: ShieldCheck },
            { title: 'Jabatan', href: '#', icon: BriefcaseBusiness },
            { title: 'Tahun Ajaran', href: '#', icon: CalendarDays },
            { title: 'Jurusan', href: '#', icon: GraduationCap },
            { title: 'Kelas', href: '#', icon: School },
            { title: 'Jenis Pelanggaran', href: '#', icon: ListChecks },
        ],
    },
    {
        title: 'Data Siswa',
        href: '#',
        icon: Users,
    },
    {
        title: 'Catat Pelanggaran',
        href: '#',
        icon: AlertTriangle,
    },
    {
        title: 'Data Pelanggaran',
        href: '#',
        icon: BookOpen,
    },
    {
        title: 'Laporan',
        href: '#',
        icon: FileText,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Pengaturan',
        href: '/settings/profile',
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>
        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
