<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { ChevronRight } from '@lucide/vue';

const activeFilter = ref('Semua');
const filterOptions = ['Semua', 'Hari Ini', 'Bulan Ini'];

const recentViolations = [
    { id: 1, nama: 'Ahmad Fauzi', inisial: 'AF', kelas: 'X PPLG 1', pelanggaran: 'Terlambat Masuk (>15 mnt)', poin: '-10 Poin', status: 'Selesai', statusClass: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' },
    { id: 2, nama: 'Budi Santoso', inisial: 'BS', kelas: 'X TJKT 2', pelanggaran: 'Atribut Seragam Tidak Lengkap', poin: '-5 Poin', status: 'Dalam Proses', statusClass: 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' },
    { id: 3, nama: 'Citra Dewi', inisial: 'CD', kelas: 'XI AKL 3', pelanggaran: 'Meninggalkan Kelas Tanpa Izin', poin: '-15 Poin', status: 'Selesai', statusClass: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' },
    { id: 4, nama: 'Dimas Prasetyo', inisial: 'DP', kelas: 'XII TKR 1', pelanggaran: 'Membawa HP saat Jam KBM', poin: '-20 Poin', status: 'Panggilan Ortu', statusClass: 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' },
    { id: 5, nama: 'Eka Kurniawati', inisial: 'EK', kelas: 'XII PPLG 2', pelanggaran: 'Terlambat Masuk Kelas', poin: '-10 Poin', status: 'Selesai', statusClass: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' },
];
</script>

<template>
    <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 shadow-xs overflow-hidden">
        <!-- Header Tabel (Recent Orders Style) -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center p-5 border-b border-slate-100 dark:border-neutral-800 gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-neutral-100">
                    Pelanggaran Terbaru
                </h2>
                <p class="text-xs text-slate-500 dark:text-neutral-400 mt-0.5">
                    Catatan penanganan pelanggaran siswa terkini
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Time Filter Pills -->
                <div class="inline-flex bg-slate-100 dark:bg-neutral-800 p-1 rounded-lg text-xs font-medium">
                    <button
                        v-for="opt in filterOptions"
                        :key="opt"
                        @click="activeFilter = opt"
                        :class="[
                            'px-2.5 py-1 rounded-md transition-all cursor-pointer',
                            activeFilter === opt
                                ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-semibold shadow-xs'
                                : 'text-slate-600 dark:text-neutral-400 hover:text-slate-900 dark:hover:text-neutral-100'
                        ]"
                    >
                        {{ opt }}
                    </button>
                </div>

                <Button variant="ghost" size="sm" class="rounded-lg text-xs font-medium h-8 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30">
                    Semua
                    <ChevronRight class="w-3.5 h-3.5 ml-0.5" />
                </Button>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 dark:bg-neutral-800/30 border-b border-slate-100 dark:border-neutral-800/80 text-[11px] font-semibold text-slate-500 dark:text-neutral-400 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Siswa</th>
                        <th class="px-5 py-3.5">Kelas</th>
                        <th class="px-5 py-3.5">Pelanggaran</th>
                        <th class="px-5 py-3.5 text-center">Poin</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-neutral-800/80 text-xs">
                    <tr
                        v-for="item in recentViolations"
                        :key="item.id"
                        class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/30 transition-colors"
                    >
                        <!-- Siswa (Avatar + Nama) -->
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center text-[10px] font-bold text-slate-700 dark:text-neutral-200">
                                    {{ item.inisial }}
                                </div>
                                <span class="font-semibold text-slate-900 dark:text-neutral-100">
                                    {{ item.nama }}
                                </span>
                            </div>
                        </td>

                        <!-- Kelas -->
                        <td class="px-5 py-3.5 whitespace-nowrap text-slate-600 dark:text-neutral-300 font-medium">
                            {{ item.kelas }}
                        </td>

                        <!-- Pelanggaran -->
                        <td class="px-5 py-3.5 text-slate-700 dark:text-neutral-300">
                            {{ item.pelanggaran }}
                        </td>

                        <!-- Poin -->
                        <td class="px-5 py-3.5 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 font-bold text-[11px]">
                                {{ item.poin }}
                            </span>
                        </td>

                        <!-- Status (Badge Semantik Pill) -->
                        <td class="px-5 py-3.5 whitespace-nowrap text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold text-[11px]"
                                :class="item.statusClass"
                            >
                                {{ item.status }}
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-5 py-3.5 whitespace-nowrap text-right">
                            <button class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 cursor-pointer">
                                Detail
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
