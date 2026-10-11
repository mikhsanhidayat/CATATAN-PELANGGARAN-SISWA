<script setup lang="ts">
import { MoreVertical } from '@lucide/vue';

const topViolators = [
    {
        id: 1,
        nama: 'Ryan Collins',
        inisial: 'RC',
        kelas: 'XI PPLG 1',
        poin: '45 Poin',
        kasus: '3 Kasus',
    },
    {
        id: 2,
        nama: 'Milo Nash',
        inisial: 'MN',
        kelas: 'X TJKT 2',
        poin: '30 Poin',
        kasus: '2 Kasus',
    },
    {
        id: 3,
        nama: 'Elara Marlowe',
        inisial: 'EM',
        kelas: 'XII AKL 3',
        poin: '25 Poin',
        kasus: '2 Kasus',
    },
    {
        id: 4,
        nama: 'Lila Luna',
        inisial: 'LL',
        kelas: 'XI TKR 1',
        poin: '20 Poin',
        kasus: '1 Kasus',
    },
    {
        id: 5,
        nama: 'Milo Nash',
        inisial: 'MN',
        kelas: 'X PPLG 2',
        poin: '15 Poin',
        kasus: '1 Kasus',
    },
];
</script>

<template>
    <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 p-5 shadow-xs">
        <!-- Header (Top Customer Style) -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-neutral-100">
                Siswa Pelanggar Terbanyak
            </h3>
            <button
                class="p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-neutral-200 transition-colors cursor-pointer"
                title="Menu opsi"
            >
                <MoreVertical class="w-4 h-4" />
            </button>
        </div>

        <!-- List Siswa -->
        <div class="space-y-3.5">
            <div
                v-for="siswa in topViolators"
                :key="siswa.id"
                class="flex items-center justify-between text-xs"
            >
                <div class="flex items-center gap-2.5 min-w-0">
                    <!-- Avatar Lingkaran -->
                    <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center text-[10px] font-bold text-slate-700 dark:text-neutral-200 shrink-0">
                        {{ siswa.inisial }}
                    </div>

                    <div class="truncate">
                        <div class="font-semibold text-slate-900 dark:text-neutral-100 truncate">
                            {{ siswa.nama }}
                        </div>
                        <div class="text-[10px] text-slate-400 dark:text-neutral-500 truncate">
                            {{ siswa.kelas }}
                        </div>
                    </div>
                </div>

                <!-- Badge Akumulasi Poin (Rose Style Sesuai Instruksi) -->
                <div class="shrink-0 pl-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 font-semibold text-[11px]">
                        {{ siswa.poin }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

