<script setup lang="ts">
import { MoreVertical } from '@lucide/vue';

const followUpList = [
    {
        label: 'Menunggu Panggilan Ortu',
        count: '4 Kasus',
        percentage: 75,
        color: 'bg-rose-500',
    },
    {
        label: 'Dalam Pembinaan Guru BK',
        count: '12 Kasus',
        percentage: 60,
        color: 'bg-amber-500',
    },
    {
        label: 'Sanksi Disiplin Ringan',
        count: '24 Kasus',
        percentage: 45,
        color: 'bg-blue-600',
    },
    {
        label: 'Selesai Dibina (Tuntas)',
        count: '138 Kasus',
        percentage: 90,
        color: 'bg-emerald-500',
    },
    {
        label: 'Siswa Bebas Catatan',
        count: '850 Siswa',
        percentage: 98,
        color: 'bg-slate-700 dark:bg-slate-300',
    },
];
</script>

<template>
    <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 p-5 shadow-xs flex flex-col justify-between">
        <div>
            <!-- Header (Active Project Style) -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-neutral-100">
                    Statistik Tindak Lanjut
                </h3>
                <button
                    class="p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-neutral-200 transition-colors cursor-pointer"
                    title="Menu opsi"
                >
                    <MoreVertical class="w-4 h-4" />
                </button>
            </div>

            <!-- List Item dengan Progress Bar -->
            <div class="space-y-4">
                <div
                    v-for="item in followUpList"
                    :key="item.label"
                    class="space-y-1.5"
                >
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-700 dark:text-neutral-300 truncate pr-2">
                            {{ item.label }}
                        </span>
                        <span class="font-bold text-slate-900 dark:text-neutral-100 shrink-0">
                            {{ item.count }}
                        </span>
                    </div>

                    <!-- Progress Bar Tipis Sesuai Referensi -->
                    <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-neutral-800 overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-500"
                            :class="item.color"
                            :style="{ width: `${item.percentage}%` }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Total -->
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-neutral-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-neutral-400">
            <span>Total siswa terpantau</span>
            <span class="font-bold text-slate-800 dark:text-neutral-200">1.028 Siswa</span>
        </div>
    </div>
</template>

