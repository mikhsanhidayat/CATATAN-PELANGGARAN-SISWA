
<script setup lang="ts">
import { Users, ShieldCheck, GraduationCap, UserRoundCheck } from '@lucide/vue';

type UserCards = {
    total_users: number | string;
    total_admin: number | string;
    total_guru: number | string;
    total_petugas: number | string;
};

defineProps<{
    cards: UserCards | null;
}>();

const statistics = [
    { key: 'total_users', title: 'Total Pengguna', description: 'Pengguna terdaftar', icon: Users, color: 'blue' },
    { key: 'total_admin', title: 'Admin', description: 'Akun administrator', icon: ShieldCheck, color: 'emerald' },
    { key: 'total_guru', title: 'Guru', description: 'Akun dengan role Guru', icon: GraduationCap, color: 'amber' },
    { key: 'total_petugas', title: 'Petugas', description: 'Akun dengan role Petugas', icon: UserRoundCheck, color: 'purple' },
] as const;

const iconColors = {
    blue: 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    emerald: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
    amber: 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
    purple: 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
};
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-5">
        <div
            v-for="item in statistics"
            :key="item.key"
            class="flex flex-col justify-between rounded-2xl border border-slate-200/70 bg-white p-5 shadow-xs transition-shadow hover:shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
        >
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-neutral-400">
                    {{ item.title }}
                </span>
                <div
                    :class="iconColors[item.color]"
                    class="flex h-8 w-8 items-center justify-center rounded-xl"
                >
                    <component :is="item.icon" class="h-4 w-4" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight text-slate-900 lg:text-[28px] dark:text-neutral-100">
                {{ cards?.[item.key] ?? '—' }}
            </div>
            <p class="mt-3 border-t border-slate-100 pt-2 text-xs text-slate-400 dark:border-neutral-800/80 dark:text-neutral-500">
                {{ item.description }}
            </p>
        </div>
    </div>
</template>
