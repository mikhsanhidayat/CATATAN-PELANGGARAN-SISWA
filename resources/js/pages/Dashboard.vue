<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

// Data dummy — bukan dari database. Akan diganti data SQL Server saat fitur siap.
const dummyRows = [
    { nama: 'Ahmad Fauzi',    kelas: 'X IPA 1',  pelanggaran: 'Terlambat Masuk Kelas',  poin: '-10', status: 'Selesai' },
    { nama: 'Budi Santoso',   kelas: 'X IPS 2',  pelanggaran: 'Pelanggaran Seragam',    poin: '-5',  status: 'Proses'  },
    { nama: 'Citra Dewi',     kelas: 'XI IPA 3', pelanggaran: 'Tidak Hadir Tanpa Izin', poin: '-15', status: 'Selesai' },
    { nama: 'Dimas Prasetyo', kelas: 'XI IPS 1', pelanggaran: 'Membawa HP ke Sekolah',  poin: '-20', status: 'Proses'  },
    { nama: 'Eka Rahmawati',  kelas: 'XII IPA 2',pelanggaran: 'Terlambat Masuk Kelas',  poin: '-10', status: 'Selesai' },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 text-sm">

        <!-- Baris 1: Kartu Statistik -->
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-xs font-medium text-muted-foreground">Total Kasus</p>
                <p class="mt-1 text-3xl font-extrabold text-blue-600 dark:text-blue-400">142</p>
                <p class="mt-1 text-xs text-muted-foreground">Sepanjang tahun ajaran ini</p>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-xs font-medium text-muted-foreground">Selesai Dibina</p>
                <p class="mt-1 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">138</p>
                <p class="mt-1 text-xs text-muted-foreground">Siswa telah menyelesaikan pembinaan</p>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-xs font-medium text-muted-foreground">Surat Panggilan</p>
                <p class="mt-1 text-3xl font-extrabold text-amber-600 dark:text-amber-400">4</p>
                <p class="mt-1 text-xs text-muted-foreground">Menunggu tindak lanjut</p>
            </div>
        </div>

        <!-- Baris 2: Grafik Ketertiban -->
        <div class="rounded-2xl border bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-slate-800 dark:text-slate-100">Tingkat Ketertiban Siswa</p>
                    <p class="mt-0.5 text-xs text-muted-foreground">Tren 6 bulan terakhir (data contoh)</p>
                </div>
                <span class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">98,2%</span>
            </div>
            <svg class="mt-5 h-24 w-full" viewBox="0 0 300 70" fill="none" aria-hidden="true">
                <path d="M0 60 H300" stroke="currentColor" class="text-slate-200 dark:text-white/10" stroke-dasharray="3 3" />
                <path d="M0 30 H300" stroke="currentColor" class="text-slate-200 dark:text-white/10" stroke-dasharray="3 3" />
                <path d="M10 50 Q 50 15, 90 40 T 170 30 T 230 18 T 290 28 L290 70 L10 70 Z" fill="#2563eb" fill-opacity="0.08" />
                <path d="M10 50 Q 50 15, 90 40 T 170 30 T 230 18 T 290 28" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="230" cy="18" r="4" fill="#2563eb" />
                <circle cx="230" cy="18" r="7" fill="#2563eb" fill-opacity="0.2" />
            </svg>
            <div class="mt-2 flex justify-between text-xs text-muted-foreground">
                <span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span><span>Jul</span><span>Agt</span>
            </div>
        </div>

        <!-- Baris 3: Tabel Pelanggaran -->
        <div class="rounded-2xl border bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="flex items-center justify-between border-b p-5 dark:border-white/10">
                <div>
                    <p class="font-semibold text-slate-800 dark:text-slate-100">Data Pelanggaran Siswa</p>
                    <p class="mt-0.5 text-xs text-muted-foreground">Menampilkan {{ dummyRows.length }} data terbaru</p>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-white/10 dark:text-slate-300">
                    Data contoh
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b bg-slate-50 dark:border-white/10 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3 font-semibold text-muted-foreground">No</th>
                            <th class="px-5 py-3 font-semibold text-muted-foreground">Nama Siswa</th>
                            <th class="px-5 py-3 font-semibold text-muted-foreground">Kelas</th>
                            <th class="px-5 py-3 font-semibold text-muted-foreground">Pelanggaran</th>
                            <th class="px-5 py-3 font-semibold text-muted-foreground">Poin</th>
                            <th class="px-5 py-3 font-semibold text-muted-foreground">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-white/10">
                        <tr
                            v-for="(row, i) in dummyRows"
                            :key="row.nama"
                            class="hover:bg-slate-50 dark:hover:bg-white/5"
                        >
                            <td class="px-5 py-3 text-muted-foreground">{{ i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800 dark:text-slate-100">{{ row.nama }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ row.kelas }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ row.pelanggaran }}</td>
                            <td class="px-5 py-3 font-semibold text-rose-600 dark:text-rose-400">{{ row.poin }}</td>
                            <td class="px-5 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2.5 py-1 font-medium',
                                        row.status === 'Selesai'
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                            : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                    ]"
                                >
                                    {{ row.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>
