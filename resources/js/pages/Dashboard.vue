<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import KartuStatistik from '@/components/Dashboard/KartuStatistik.vue';
import GrafikKetertiban from '@/components/Dashboard/GrafikKetertiban.vue';
import TabelPelanggaranTerbaru from '@/components/Dashboard/TabelPelanggaranTerbaru.vue';
import StatistikTindakLanjut from '@/components/Dashboard/StatistikTindakLanjut.vue';
import SiswaPelanggarTerbanyak from '@/components/Dashboard/SiswaPelanggarTerbanyak.vue';
import { Calendar } from '@lucide/vue';
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col p-4 md:p-6 lg:p-8 w-full max-w-7xl mx-auto space-y-6 bg-slate-50 dark:bg-neutral-950 min-h-screen">
        <!-- 1. Page Header (Breadcrumb Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="text-xs font-semibold text-blue-600 dark:text-blue-400">
                    Dashboard <span class="text-slate-400 dark:text-neutral-500">/</span> Ringkasan
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-neutral-100 mt-1">
                    Ringkasan Ketertiban Siswa
                </h1>
                <p class="text-xs text-slate-500 dark:text-neutral-400">
                    Sistem pemantauan ketertiban, pembinaan, dan disiplin siswa secara real-time
                </p>
            </div>

            <!-- Periode Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white dark:bg-neutral-900 border border-slate-200/80 dark:border-neutral-800 text-xs font-medium text-slate-600 dark:text-neutral-300 shadow-xs self-start sm:self-auto">
                <Calendar class="w-3.5 h-3.5 text-slate-400" />
                <span>Semester Ganjil 2026/2027</span>
            </div>
        </div>

        <!-- 2. Top Stat Cards (4 Kartu Meniru Struktur Referensi) -->
        <KartuStatistik />

        <!-- 3. Main Grid Layout 2 Kolom (Sesuai Referensi) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6">
            <!-- AREA KIRI: Main Chart & Tabel Pelanggaran (8 Kolom di LG) -->
            <div class="lg:col-span-8 space-y-5 md:space-y-6">
                <!-- Grafik Tren (Balance Overview) -->
                <GrafikKetertiban />

                <!-- Tabel Pelanggaran Terbaru (Recent Orders) -->
                <TabelPelanggaranTerbaru />
            </div>

            <!-- AREA KANAN: Side Lists (4 Kolom di LG) -->
            <div class="lg:col-span-4 space-y-5 md:space-y-6">
                <!-- List 1: Statistik Tindak Lanjut (Active Project) -->
                <StatistikTindakLanjut />

                <!-- List 2: Siswa Pelanggar Terbanyak (Top Customer) -->
                <SiswaPelanggarTerbanyak />
            </div>
        </div>
    </div>
</template>
