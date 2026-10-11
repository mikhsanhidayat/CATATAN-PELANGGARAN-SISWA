<script setup lang="ts">
import { ref } from 'vue';

const activeFilter = ref('Bulanan');
const filterOptions = ['Semua', 'Harian', 'Bulanan', 'Tahunan'];
</script>

<template>
    <div class="bg-white dark:bg-neutral-900 rounded-xl border border-slate-200/80 dark:border-neutral-800 p-6 shadow-xs flex flex-col justify-between">
        <div>
            <!-- Header Grafik: Title + Time Filter Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-neutral-100">
                        Tren Kasus Pelanggaran & Pembinaan
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400 mt-0.5">
                        Perbandingan kasus pelanggaran baru vs siswa yang menyelesaikan pembinaan
                    </p>
                </div>

                <!-- Time Filter Buttons (Seperti All, 1M, 6M, 1Y di referensi) -->
                <div class="inline-flex bg-slate-100 dark:bg-neutral-800 p-1 rounded-lg text-xs font-medium self-start sm:self-auto">
                    <button
                        v-for="opt in filterOptions"
                        :key="opt"
                        @click="activeFilter = opt"
                        :class="[
                            'px-3 py-1 rounded-md transition-all cursor-pointer',
                            activeFilter === opt
                                ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-semibold shadow-xs'
                                : 'text-slate-600 dark:text-neutral-400 hover:text-slate-900 dark:hover:text-neutral-100'
                        ]"
                    >
                        {{ opt }}
                    </button>
                </div>
            </div>

            <!-- Summary Numbers (Seperti $584k - Revenue & $497k - Expenses di referensi) -->
            <div class="flex flex-wrap items-center gap-6 text-xs mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    <span class="font-bold text-slate-900 dark:text-neutral-100 text-sm">142 Kasus</span>
                    <span class="text-slate-500 dark:text-neutral-400">- Pelanggaran Baru</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="font-bold text-slate-900 dark:text-neutral-100 text-sm">138 Siswa</span>
                    <span class="text-slate-500 dark:text-neutral-400">- Selesai Dibina</span>
                </div>
            </div>
        </div>

        <!-- Area Grafik Garis SVG Modern -->
        <div class="relative w-full h-64 mt-2">
            <svg class="w-full h-full" viewBox="0 0 800 220" preserveAspectRatio="none">
                <defs>
                    <!-- Gradient Biru (Pelanggaran) -->
                    <linearGradient id="blueGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#2563eb" stop-opacity="0.16" />
                        <stop offset="100%" stop-color="#2563eb" stop-opacity="0.0" />
                    </linearGradient>
                </defs>

                <!-- Grid Horizontal Halus -->
                <line x1="0" y1="35" x2="800" y2="35" stroke="#f1f5f9" stroke-width="1.2" class="dark:stroke-neutral-800" />
                <line x1="0" y1="90" x2="800" y2="90" stroke="#f1f5f9" stroke-width="1.2" class="dark:stroke-neutral-800" />
                <line x1="0" y1="145" x2="800" y2="145" stroke="#f1f5f9" stroke-width="1.2" class="dark:stroke-neutral-800" />
                <line x1="0" y1="200" x2="800" y2="200" stroke="#f1f5f9" stroke-width="1.2" class="dark:stroke-neutral-800" />

                <!-- Area Gradien Biru -->
                <path
                    d="M 20,150 C 140,140 220,110 320,120 C 420,130 500,80 620,65 C 700,55 740,85 780,75 L 780,200 L 20,200 Z"
                    fill="url(#blueGradient)"
                />

                <!-- Garis Hijau: Selesai Dibina -->
                <path
                    d="M 20,170 C 140,165 220,145 320,135 C 420,125 500,105 620,85 C 700,75 740,80 780,70"
                    fill="none"
                    stroke="#10b981"
                    stroke-width="2.5"
                    stroke-linecap="round"
                />

                <!-- Garis Biru: Pelanggaran Baru -->
                <path
                    d="M 20,150 C 140,140 220,110 320,120 C 420,130 500,80 620,65 C 700,55 740,85 780,75"
                    fill="none"
                    stroke="#2563eb"
                    stroke-width="3"
                    stroke-linecap="round"
                />

                <!-- Marker Titik Data Pilihan -->
                <circle cx="320" cy="120" r="4.5" fill="#2563eb" stroke="#ffffff" stroke-width="2" />
                <circle cx="620" cy="65" r="4.5" fill="#2563eb" stroke="#ffffff" stroke-width="2" />
                <circle cx="780" cy="75" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2.5" />

                <circle cx="320" cy="135" r="4" fill="#10b981" stroke="#ffffff" stroke-width="1.8" />
                <circle cx="620" cy="85" r="4" fill="#10b981" stroke="#ffffff" stroke-width="1.8" />
                <circle cx="780" cy="70" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
            </svg>

            <!-- Label Sumbu X -->
            <div class="absolute -bottom-1 w-full flex justify-between text-xs font-medium text-slate-400 dark:text-neutral-500 px-3">
                <span>Mar</span>
                <span>Apr</span>
                <span>Mei</span>
                <span>Jun</span>
                <span>Jul</span>
                <span>Agt</span>
            </div>
        </div>
    </div>
</template>
