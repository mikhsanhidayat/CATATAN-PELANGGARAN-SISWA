<script setup lang="ts">
import { AlertTriangle, CheckCircle2, Mail, Award, ArrowUpRight, ArrowDownRight } from '@lucide/vue';
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 mb-6">
        <!-- 1. Total Kasus -->
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-neutral-400">
                    Total Kasus
                </span>
                <div class="w-8 h-8 rounded-lg border border-slate-200/80 dark:border-neutral-800 flex items-center justify-center text-slate-600 dark:text-neutral-300 bg-slate-50/50 dark:bg-neutral-800/50">
                    <AlertTriangle class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
            </div>

            <div class="mt-3">
                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                    142
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[11px] font-semibold">
                        <ArrowUpRight class="w-3 h-3" />
                        12.4%
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-neutral-500">
                        vs. last month
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Selesai Dibina -->
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-neutral-400">
                    Selesai Dibina
                </span>
                <div class="w-8 h-8 rounded-lg border border-slate-200/80 dark:border-neutral-800 flex items-center justify-center text-slate-600 dark:text-neutral-300 bg-slate-50/50 dark:bg-neutral-800/50">
                    <CheckCircle2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>

            <div class="mt-3">
                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                    138
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[11px] font-semibold">
                        <ArrowUpRight class="w-3 h-3" />
                        8.7%
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-neutral-500">
                        vs. last month
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. Surat Panggilan (Perlu Tindakan) -->
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-neutral-400">
                    Surat Panggilan
                </span>
                <div class="w-8 h-8 rounded-lg border border-slate-200/80 dark:border-neutral-800 flex items-center justify-center text-slate-600 dark:text-neutral-300 bg-slate-50/50 dark:bg-neutral-800/50">
                    <Mail class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                </div>
            </div>

            <div class="mt-3">
                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                    4
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 text-[11px] font-semibold">
                        <ArrowDownRight class="w-3 h-3" />
                        Perlu Tindakan
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-neutral-500">
                        Wali murid
                    </span>
                </div>
            </div>
        </div>

        <!-- 4. Tingkat Ketertiban -->
        <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-neutral-400">
                    Tingkat Ketertiban
                </span>
                <div class="w-8 h-8 rounded-lg border border-slate-200/80 dark:border-neutral-800 flex items-center justify-center text-slate-600 dark:text-neutral-300 bg-slate-50/50 dark:bg-neutral-800/50">
                    <Award class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
            </div>

            <div class="mt-3">
                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                    98.2%
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[11px] font-semibold">
                        <ArrowUpRight class="w-3 h-3" />
                        1.5%
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-neutral-500">
                        target 99.0%
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
