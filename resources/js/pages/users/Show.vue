<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import ModalEditUser from '@/components/Users/ModalEditUser.vue';
import {
    ArrowLeft,
    User,
    Mail,
    Phone,
    MapPin,
    Calendar,
    CalendarDays,
    Shield,
    Briefcase,
    History,
    FileText,
    Pencil,
    Activity,
    Check,
    List,
    ChevronRight,
    Trash2,
    Loader2,
    ArrowRight,
} from '@lucide/vue';

type RoleOption = {
    id_role: number;
    nama_role: string;
};

type UserDetail = {
    id_users: number;
    id_role: number;
    nama_role: string;
    nama_users: string;
    jenis_kelamin: string;
    tempat_lahir: string;
    tanggal_lahir: string;
    alamat: string;
    no_hp: string;
    email: string;
    email_verified_at: string | null;
    create_at: string;
    create_by: string;
    status_delete: boolean | number | string;
};

type UserRiwayat = {
    id_riwayat_jabatan: number;
    id_users: number;
    id_tahun_ajaran: number;
    id_jabatan: number;
    nama_tahun_ajaran: string;
    nama_jabatan: string;
    status: string;
    awal_menjabat: string;
    akhir_menjabat: string | null;
    create_at: string;
    create_by: number;
};

type UserActivity = {
    id_activity_log: number;
    id_users: number;
    data_sebelum: string;
    data_sesudah: string;
    activity_at: string;
    activity_by: string;
};

const props = defineProps<{
    user: UserDetail;
    riwayat: UserRiwayat[];
    activity: UserActivity[];
    roles: RoleOption[];
}>();

const page = usePage();

// Cek apakah pengguna yang dibuka adalah akun admin yang sedang login
const isCurrentUser = computed(() => {
    const authId = (page.props.auth as any)?.user?.id_users;
    return authId && Number(authId) === Number(props.user.id_users);
});

// State modal edit & modal hapus
const isEditModalOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const isDeleting = ref(false);

// Inisial avatar dua huruf kapital
const userInitials = computed(() => {
    if (!props.user?.nama_users) return 'U';
    const parts = props.user.nama_users.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return parts[0].substring(0, 2).toUpperCase();
});

// Format tanggal standar
const formatDateStandard = (dateStr: string | null | undefined): string => {
    if (!dateStr) return '-';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return dateStr;
    }
};

// Format tanggal lahir (YYYY-MM-DD)
const formatBirthDate = (dateStr: string | null | undefined): string => {
    if (!dateStr) return '-';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    } catch {
        return dateStr;
    }
};

// Helper untuk parsing timestamp SQL Server (WIB / UTC+7) dengan aman di JavaScript
const parseWibDate = (dateStr: string | null | undefined): Date | null => {
    if (!dateStr) return null;
    try {
        let str = String(dateStr).trim().replace(' ', 'T');
        // Normalisasi pecahan detik DATETIME2 SQL Server jika > 3 digit: .4299627 -> .429
        str = str.replace(/(\.\d{3})\d+/, '$1');
        // Tambahkan offset +07:00 jika belum memiliki penanda timezone (Z, +HH:MM, -HH:MM)
        if (!str.includes('Z') && !str.includes('+') && !str.match(/-\d{2}:\d{2}$/)) {
            str += '+07:00';
        }
        const d = new Date(str);
        return isNaN(d.getTime()) ? null : d;
    } catch {
        return null;
    }
};

// Format tanggal lengkap dengan jam & menit sesuai WIB
const formatDateTime = (dateStr: string | null | undefined): string => {
    if (!dateStr) return '-';
    try {
        const d = parseWibDate(dateStr);
        if (!d) return dateStr;
        const datePart = d.toLocaleDateString('id-ID', {
            timeZone: 'Asia/Jakarta',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
        const timePart = d.toLocaleTimeString('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        });
        return `${datePart}, ${timePart}`;
    } catch {
        return dateStr;
    }
};

// Status aktif pengguna: false, 0, atau "0" berarti Aktif; selain itu berarti Dihapus
const isUserActive = computed(() => {
    const val = props.user?.status_delete;
    return val === false || val === 0 || val === '0';
});

// Jabatan aktif/terakhir dari riwayat jabatan
const activeRiwayat = computed(() => {
    if (!props.riwayat || props.riwayat.length === 0) return null;
    const stillHolding = props.riwayat.find(r =>
        r.status?.toLowerCase().includes('aktif') ||
        r.status?.toLowerCase().includes('masih')
    );
    return stillHolding ?? props.riwayat[0];
});

// Helper check apakah status jabatan aktif
const isRiwayatAktif = (status: string | null | undefined): boolean => {
    if (!status) return false;
    const s = status.toLowerCase();
    return s.includes('aktif') || s.includes('masih');
};

// Eksekusi Soft Delete pengguna
const handleDeleteUser = () => {
    if (isCurrentUser.value || isDeleting.value) return;

    isDeleting.value = true;
    router.delete(`/users/${props.user.id_users}`, {
        preserveScroll: false,
        onFinish: () => {
            isDeleting.value = false;
            isDeleteDialogOpen.value = false;
        },
    });
};

// ==========================================
// Parsing & Komputasi Diff untuk Activity Log
// ==========================================

type FieldDiff = {
    field: string;
    label: string;
    sebelum: string;
    sesudah: string;
    isPassword?: boolean;
};

// Normalisasi perbandingan nilai field SQL Server agar tidak memicu diff palsu
const normalizeValue = (val: any): string => {
    if (val === null || val === undefined) return '';
    return String(val).trim();
};

// Format tampilan nilai field lama/baru
const formatDisplayValue = (field: string, val: any): string => {
    if (val === null || val === undefined || val === '') return '-';
    if (field === 'id_role') {
        const found = props.roles?.find(r => String(r.id_role) === String(val));
        return found ? found.nama_role : `Role #${val}`;
    }
    if (field === 'tanggal_lahir') {
        return formatBirthDate(val);
    }
    return String(val);
};

// Parsing setiap log update menjadi daftar perubahan field yang sebenarnya
const parseActivityDiff = (dataSebelumJson: string, dataSesudahJson: string): FieldDiff[] => {
    try {
        const sebelum = JSON.parse(dataSebelumJson || '{}');
        const sesudah = JSON.parse(dataSesudahJson || '{}');
        const diffs: FieldDiff[] = [];

        // 1. Cek perubahan password
        const passwordChanged =
            sesudah.password_diubah === true ||
            sesudah.password_diubah === 1 ||
            sesudah.password_diubah === '1';

        if (passwordChanged) {
            diffs.push({
                field: 'password',
                label: 'Password',
                sebelum: '',
                sesudah: '',
                isPassword: true,
            });
        }

        // 2. Daftar field profil dan label bahasa Indonesia
        const fieldLabels: Record<string, string> = {
            nama_users: 'Nama Pengguna',
            id_role: 'Role Sistem',
            jenis_kelamin: 'Jenis Kelamin',
            tempat_lahir: 'Tempat Lahir',
            tanggal_lahir: 'Tanggal Lahir',
            alamat: 'Alamat',
            no_hp: 'Nomor HP',
            email: 'Email',
        };

        for (const [field, label] of Object.entries(fieldLabels)) {
            const valSebelum = normalizeValue(sebelum[field]);
            const valSesudah = normalizeValue(sesudah[field]);

            if (valSebelum !== valSesudah) {
                diffs.push({
                    field,
                    label,
                    sebelum: formatDisplayValue(field, sebelum[field]),
                    sesudah: formatDisplayValue(field, sesudah[field]),
                });
            }
        }

        return diffs;
    } catch {
        return [];
    }
};

// Seluruh entri timeline tersusun kronologis dari yang terbaru
type TimelineItem = {
    id: string;
    type: 'create' | 'email_verified' | 'update';
    title: string;
    timestamp: string;
    actor: string;
    diffs?: FieldDiff[];
};

const fullTimeline = computed<TimelineItem[]>(() => {
    const list: TimelineItem[] = [];

    // 1. Akun Dibuat
    if (props.user?.create_at) {
        list.push({
            id: 'create',
            type: 'create',
            title: 'Akun dibuat',
            timestamp: props.user.create_at,
            actor: props.user.create_by || 'System',
        });
    }

    // 2. Email Diverifikasi (jika ada data aktual)
    if (props.user?.email_verified_at) {
        list.push({
            id: 'email_verified',
            type: 'email_verified',
            title: 'Email diverifikasi',
            timestamp: props.user.email_verified_at,
            actor: 'Terverifikasi',
        });
    }

    // 3. Seluruh riwayat UPDATE dari dbo.v_dash_users_activity
    if (props.activity && props.activity.length > 0) {
        for (const act of props.activity) {
            const diffs = parseActivityDiff(act.data_sebelum, act.data_sesudah);
            list.push({
                id: `activity-${act.id_activity_log}`,
                type: 'update',
                title: 'Profil diperbarui',
                timestamp: act.activity_at,
                actor: act.activity_by || 'System',
                diffs,
            });
        }
    }

    // Urutkan kronologis dari yang terbaru ke terlama (DESC)
    return list.sort((a, b) => {
        const timeA = parseWibDate(a.timestamp)?.getTime() || 0;
        const timeB = parseWibDate(b.timestamp)?.getTime() || 0;
        return timeB - timeA;
    });
});
</script>

<template>
    <Head :title="`Detail Pengguna - ${user.nama_users}`" />

    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col space-y-6 p-4 md:p-6 lg:p-8">
        <!-- 1. Header & Breadcrumb -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <!-- Breadcrumbs -->
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-neutral-400">
                    <Link href="/users" class="hover:text-blue-600 transition-colors">
                        Data Master
                    </Link>
                    <ChevronRight class="h-3 w-3 text-slate-400" />
                    <Link href="/users" class="hover:text-blue-600 transition-colors">
                        Users
                    </Link>
                    <ChevronRight class="h-3 w-3 text-slate-400" />
                    <span class="text-slate-800 dark:text-neutral-200">
                        Detail Pengguna
                    </span>
                </div>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                    Detail Pengguna
                </h1>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-neutral-400">
                    Informasi lengkap akun pengguna dan riwayat jabatan dalam sistem DisiplinKu.
                </p>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex items-center gap-2.5">
                <!-- Tombol Kembali -->
                <Button
                    as-child
                    variant="outline"
                    class="h-9 rounded-xl border-slate-200/80 px-4 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-800"
                >
                    <Link href="/users" class="inline-flex items-center gap-2">
                        <ArrowLeft class="h-3.5 w-3.5" />
                        <span>Kembali</span>
                    </Link>
                </Button>

                <!-- Tombol Hapus Pengguna (Soft Delete) -->
                <Button
                    v-if="isUserActive"
                    type="button"
                    variant="outline"
                    :disabled="isCurrentUser"
                    :title="isCurrentUser ? 'Tidak dapat menghapus akun Anda sendiri' : 'Hapus pengguna ini'"
                    class="h-9 inline-flex items-center gap-2 rounded-xl border-rose-200 bg-rose-50/50 px-3.5 text-xs font-semibold text-rose-600 hover:bg-rose-100/70 hover:text-rose-700 disabled:opacity-40 disabled:cursor-not-allowed dark:border-rose-900/40 dark:bg-rose-950/20 dark:text-rose-400"
                    @click="isDeleteDialogOpen = true"
                >
                    <Trash2 class="h-3.5 w-3.5" />
                    <span>Hapus</span>
                </Button>

                <!-- Tombol Edit Pengguna -->
                <Button
                    type="button"
                    class="h-9 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-semibold text-white shadow-xs hover:bg-blue-700 cursor-pointer dark:bg-blue-600 dark:hover:bg-blue-700"
                    @click="isEditModalOpen = true"
                >
                    <Pencil class="h-3.5 w-3.5" />
                    <span>Edit Pengguna</span>
                </Button>
            </div>
        </div>

        <!-- 2. Grid Bagian Atas: Informasi Pengguna (8 col) & Sidebar Info (4 col) -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Kolom Kiri Atas (8 cols): Card Informasi Pengguna -->
            <div class="lg:col-span-8">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <!-- Card Header -->
                    <div class="flex items-center gap-2.5 pb-5 border-b border-slate-100 dark:border-neutral-800">
                        <User class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        <h2 class="text-sm font-bold text-slate-900 dark:text-neutral-100">
                            Informasi Pengguna
                        </h2>
                    </div>

                    <!-- Profil User (Avatar Bundar Penuh & Header Info) -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-5 py-6 border-b border-slate-100 dark:border-neutral-800">
                        <!-- Avatar Bundar Bulat Penuh Sesuai Gambar Referensi -->
                        <div class="flex h-18 w-18 items-center justify-center rounded-full bg-blue-100 text-xl font-bold text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 shrink-0">
                            {{ userInitials }}
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-neutral-100">
                                {{ user.nama_users }}
                            </h3>

                            <!-- Badges: Role Sistem & Status Akun Aktif -->
                            <div class="mt-2.5 flex flex-wrap items-center gap-2.5">
                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                                    {{ user.nama_role }}
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="isUserActive
                                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300'
                                        : 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300'"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full"
                                        :class="isUserActive ? 'bg-emerald-500' : 'bg-rose-500'"
                                    ></span>
                                    <span>{{ isUserActive ? 'Aktif' : 'Dihapus' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Data Diri Pengguna (2 Kolom Presisi) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 pt-6">
                        <!-- 1. Nama Pengguna -->
                        <div class="flex items-start gap-3">
                            <User class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Nama Pengguna
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-900 dark:text-neutral-100">
                                    {{ user.nama_users }}
                                </span>
                            </div>
                        </div>

                        <!-- 2. Email -->
                        <div class="flex items-start gap-3">
                            <Mail class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Email
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-900 dark:text-neutral-100 break-all">
                                    {{ user.email }}
                                </span>
                            </div>
                        </div>

                        <!-- 3. Nomor HP -->
                        <div class="flex items-start gap-3">
                            <Phone class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Nomor HP
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-900 dark:text-neutral-100">
                                    {{ user.no_hp || '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- 4. Jenis Kelamin -->
                        <div class="flex items-start gap-3">
                            <User class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Jenis Kelamin
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-900 dark:text-neutral-100">
                                    {{ user.jenis_kelamin || '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- 5. Tempat Lahir -->
                        <div class="flex items-start gap-3">
                            <MapPin class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Tempat Lahir
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-900 dark:text-neutral-100">
                                    {{ user.tempat_lahir || '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- 6. Tanggal Lahir -->
                        <div class="flex items-start gap-3">
                            <Calendar class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Tanggal Lahir
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-900 dark:text-neutral-100">
                                    {{ formatBirthDate(user.tanggal_lahir) }}
                                </span>
                            </div>
                        </div>

                        <!-- 7. Alamat Lengkap -->
                        <div class="flex items-start gap-3">
                            <MapPin class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Alamat
                                </span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-900 dark:text-neutral-100 leading-relaxed">
                                    {{ user.alamat || '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- 8. Role -->
                        <div class="flex items-start gap-3">
                            <Shield class="h-4 w-4 text-slate-400 dark:text-neutral-500 mt-0.5 shrink-0" />
                            <div>
                                <span class="block text-xs font-medium text-slate-400 dark:text-neutral-500">
                                    Role
                                </span>
                                <div class="mt-1">
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                                        {{ user.nama_role }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan Atas (4 cols): Card Informasi Akun & Card Ringkasan Jabatan -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Card Informasi Akun -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center gap-2.5 pb-5 border-b border-slate-100 dark:border-neutral-800">
                        <FileText class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        <h2 class="text-sm font-bold text-slate-900 dark:text-neutral-100">
                            Informasi Akun
                        </h2>
                    </div>

                    <div class="space-y-4 pt-5 text-xs">
                        <!-- ID User -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <User class="h-3.5 w-3.5 text-slate-400" />
                                <span>ID User</span>
                            </div>
                            <span class="font-semibold text-slate-900 dark:text-neutral-100">
                                {{ user.id_users }}
                            </span>
                        </div>

                        <!-- Dibuat Pada -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <Calendar class="h-3.5 w-3.5 text-slate-400" />
                                <span>Dibuat Pada</span>
                            </div>
                            <span class="font-semibold text-slate-900 dark:text-neutral-100">
                                {{ formatDateTime(user.create_at) }}
                            </span>
                        </div>

                        <!-- Dibuat Oleh -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <User class="h-3.5 w-3.5 text-slate-400" />
                                <span>Dibuat Oleh</span>
                            </div>
                            <span class="font-semibold text-slate-900 dark:text-neutral-100">
                                {{ user.create_by || 'System' }}
                            </span>
                        </div>

                        <!-- Email Verified -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <Mail class="h-3.5 w-3.5 text-slate-400" />
                                <span>Email Verified</span>
                            </div>
                            <span
                                v-if="user.email_verified_at"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
                            >
                                <span class="flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-500 text-white">
                                    <Check class="h-2.5 w-2.5 stroke-[3]" />
                                </span>
                                <span>Ya</span>
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1 text-xs font-medium text-slate-400 dark:text-neutral-500"
                            >
                                Belum
                            </span>
                        </div>

                        <!-- Status Akun -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <Shield class="h-3.5 w-3.5 text-slate-400" />
                                <span>Status Akun</span>
                            </div>
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-semibold"
                                :class="isUserActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                            >
                                <span
                                    class="h-2 w-2 rounded-full"
                                    :class="isUserActive ? 'bg-emerald-500' : 'bg-rose-500'"
                                ></span>
                                <span>{{ isUserActive ? 'Aktif' : 'Dihapus' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Ringkasan Jabatan -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center gap-2.5 pb-5 border-b border-slate-100 dark:border-neutral-800">
                        <Briefcase class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        <h2 class="text-sm font-bold text-slate-900 dark:text-neutral-100">
                            Ringkasan Jabatan
                        </h2>
                    </div>

                    <div class="space-y-4 pt-5 text-xs">
                        <!-- Jabatan Terakhir -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <Briefcase class="h-3.5 w-3.5 text-slate-400" />
                                <span>Jabatan Terakhir</span>
                            </div>
                            <span class="font-semibold text-slate-900 dark:text-neutral-100">
                                {{ activeRiwayat ? activeRiwayat.nama_jabatan : 'Belum ada' }}
                            </span>
                        </div>

                        <!-- Tahun Ajaran Aktif -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <CalendarDays class="h-3.5 w-3.5 text-slate-400" />
                                <span>Tahun Ajaran Aktif</span>
                            </div>
                            <span class="font-semibold text-slate-900 dark:text-neutral-100">
                                {{ activeRiwayat ? activeRiwayat.nama_tahun_ajaran : '-' }}
                            </span>
                        </div>

                        <!-- Total Riwayat Jabatan -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                                <List class="h-3.5 w-3.5 text-slate-400" />
                                <span>Total Riwayat Jabatan</span>
                            </div>
                            <span class="font-semibold text-slate-900 dark:text-neutral-100">
                                {{ riwayat.length }} riwayat
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Grid Bagian Bawah: Tabel Riwayat Jabatan (8 col) & Card Aktivitas / Audit (4 col) -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Kolom Kiri Bawah (8 cols): Tabel Riwayat Jabatan -->
            <div class="lg:col-span-8">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <!-- Header Card -->
                    <div class="flex items-center gap-3 pb-5 border-b border-slate-100 dark:border-neutral-800">
                        <History class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-neutral-100">
                                Riwayat Jabatan
                            </h2>
                            <p class="text-[11px] text-slate-400 dark:text-neutral-500">
                                Daftar riwayat jabatan yang pernah diemban oleh pengguna ini.
                            </p>
                        </div>
                    </div>

                    <!-- Tabel Riwayat Jabatan -->
                    <div class="overflow-x-auto pt-4">
                        <table class="w-full border-collapse text-left text-xs">
                            <thead>
                                <tr class="text-xs font-medium text-slate-400 dark:text-neutral-500 border-b border-slate-100 dark:border-neutral-800">
                                    <th class="pb-3 pr-4 font-medium">No</th>
                                    <th class="pb-3 px-4 font-medium">Tahun Ajaran</th>
                                    <th class="pb-3 px-4 font-medium">Jabatan</th>
                                    <th class="pb-3 px-4 font-medium">Tanggal Mulai</th>
                                    <th class="pb-3 px-4 font-medium">Tanggal Selesai</th>
                                    <th class="pb-3 pl-4 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                                <tr
                                    v-for="(item, index) in riwayat"
                                    :key="item.id_riwayat_jabatan"
                                    class="transition-colors hover:bg-slate-50/50 dark:hover:bg-neutral-800/40"
                                >
                                    <!-- No -->
                                    <td class="py-3.5 pr-4 text-slate-500 dark:text-neutral-400">
                                        {{ index + 1 }}
                                    </td>

                                    <!-- Tahun Ajaran -->
                                    <td class="py-3.5 px-4 font-medium text-slate-900 dark:text-neutral-100 whitespace-nowrap">
                                        {{ item.nama_tahun_ajaran }}
                                    </td>

                                    <!-- Jabatan -->
                                    <td class="py-3.5 px-4 text-slate-700 dark:text-neutral-300 whitespace-nowrap">
                                        {{ item.nama_jabatan }}
                                    </td>

                                    <!-- Tanggal Mulai -->
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-neutral-400 whitespace-nowrap">
                                        {{ formatDateStandard(item.awal_menjabat) }}
                                    </td>

                                    <!-- Tanggal Selesai -->
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-neutral-400 whitespace-nowrap">
                                        {{ item.akhir_menjabat ? formatDateStandard(item.akhir_menjabat) : '-' }}
                                    </td>

                                    <!-- Status (Aktif / Selesai) -->
                                    <td class="py-3.5 pl-4 whitespace-nowrap">
                                        <div
                                            v-if="isRiwayatAktif(item.status)"
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </div>
                                        <div
                                            v-else
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 dark:text-neutral-500"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300 dark:bg-neutral-600"></span>
                                            <span>Selesai</span>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="riwayat.length === 0">
                                    <td colspan="6" class="py-10 text-center text-slate-400 dark:text-neutral-500">
                                        <div class="flex flex-col items-center justify-center space-y-1">
                                            <Briefcase class="h-6 w-6 text-slate-300 dark:text-neutral-600 mb-1" />
                                            <span class="font-medium text-xs">Belum ada riwayat jabatan</span>
                                            <span class="text-[11px]">Pengguna ini belum memiliki catatan riwayat jabatan dalam sistem.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan Bawah (4 cols): Card Aktivitas / Audit Kronologis -->
            <div class="lg:col-span-4">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <!-- Header Card -->
                    <div class="flex items-center gap-2.5 pb-5 border-b border-slate-100 dark:border-neutral-800">
                        <Activity class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-neutral-100">
                                Aktivitas / Audit
                            </h2>
                            <p class="text-[11px] text-slate-400 dark:text-neutral-500">
                                Catatan kronologis akun dan pembaruan data dari database.
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Audit Kronologis Dinamis -->
                    <div class="relative max-h-[460px] overflow-y-auto pl-6 pr-1 pt-5 space-y-6 before:absolute before:left-2 before:top-7 before:bottom-3 before:w-0.5 before:bg-slate-200 dark:before:bg-neutral-800">
                        <div
                            v-for="item in fullTimeline"
                            :key="item.id"
                            class="relative"
                        >
                            <!-- Titik Indikator Timeline -->
                            <div
                                class="absolute -left-6 top-1 h-2.5 w-2.5 rounded-full"
                                :class="{
                                    'bg-blue-600': item.type === 'create',
                                    'bg-emerald-500': item.type === 'email_verified',
                                    'bg-amber-500': item.type === 'update',
                                }"
                            ></div>

                            <!-- Header Tiap Item Timeline -->
                            <div class="flex items-start justify-between gap-2">
                                <span class="block text-xs font-bold text-slate-900 dark:text-neutral-100">
                                    {{ item.title }}
                                </span>
                                <div class="text-right">
                                    <span class="block text-xs font-semibold text-slate-700 dark:text-neutral-300">
                                        {{ formatDateTime(item.timestamp) }}
                                    </span>
                                    <span class="block text-[11px] text-slate-400 dark:text-neutral-500">
                                        {{ item.type === 'email_verified' ? item.actor : `Oleh ${item.actor}` }}
                                    </span>
                                </div>
                            </div>

                            <!-- Rincian Field yang Berubah (Hanya untuk tipe Update) -->
                            <div
                                v-if="item.type === 'update' && item.diffs && item.diffs.length > 0"
                                class="mt-2.5 space-y-1.5 rounded-xl border border-slate-100 bg-slate-50/70 p-3 text-[11px] dark:border-neutral-800/80 dark:bg-neutral-800/40"
                            >
                                <div
                                    v-for="(diff, dIdx) in item.diffs"
                                    :key="dIdx"
                                    class="leading-relaxed"
                                >
                                    <!-- Jika password yang berubah -->
                                    <template v-if="diff.isPassword">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                                            Password diperbarui
                                        </span>
                                    </template>

                                    <!-- Field profil biasa: Label, nilai sebelum -> nilai sesudah -->
                                    <template v-else>
                                        <span class="font-semibold text-slate-700 dark:text-neutral-300">
                                            {{ diff.label }}:
                                        </span>
                                        <span class="text-slate-400 line-through ml-1 dark:text-neutral-500">
                                            {{ diff.sebelum }}
                                        </span>
                                        <ArrowRight class="inline-block mx-1 h-2.5 w-2.5 text-slate-400" />
                                        <span class="font-medium text-slate-900 dark:text-neutral-100">
                                            {{ diff.sesudah }}
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State Jika Tidak Ada Timeline -->
                        <div v-if="fullTimeline.length === 0" class="text-center py-6 text-slate-400 text-xs">
                            Belum ada catatan aktivitas.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Modal Edit Pengguna -->
        <ModalEditUser
            v-model:open="isEditModalOpen"
            :user="user"
            :roles="roles"
        />

        <!-- 5. Dialog Konfirmasi Hapus Pengguna (Soft Delete) -->
        <Dialog :open="isDeleteDialogOpen" @update:open="(val) => { if (!val) isDeleteDialogOpen = false; }">
            <DialogContent class="max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800">
                <DialogHeader class="border-b border-slate-100 pb-3 dark:border-neutral-800">
                    <DialogTitle class="flex items-center gap-2 text-base font-bold text-rose-600 dark:text-rose-400">
                        <Trash2 class="h-4 w-4" />
                        <span>Hapus Pengguna</span>
                    </DialogTitle>
                    <DialogDescription class="text-xs text-slate-500 dark:text-neutral-400 pt-1">
                        Tindakan ini akan menonaktifkan akun pengguna (Soft Delete).
                    </DialogDescription>
                </DialogHeader>

                <div class="py-3 text-xs text-slate-700 dark:text-neutral-300 leading-relaxed">
                    Apakah Anda yakin ingin menghapus akun pengguna
                    <span class="font-bold text-slate-900 dark:text-neutral-100">"{{ user.nama_users }}"</span>?
                    Pengguna ini tidak akan dapat login kembali atau mengakses sistem.
                </div>

                <DialogFooter class="border-t border-slate-100 pt-4 gap-2 dark:border-neutral-800">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="isDeleting"
                        class="rounded-xl border-slate-200 text-xs text-slate-600 hover:bg-slate-50 dark:border-neutral-700 dark:text-neutral-300"
                        @click="isDeleteDialogOpen = false"
                    >
                        Batal
                    </Button>

                    <Button
                        type="button"
                        :disabled="isDeleting"
                        class="rounded-xl bg-rose-600 px-4 text-xs font-semibold text-white hover:bg-rose-700 focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:opacity-70 dark:bg-rose-600 dark:hover:bg-rose-700"
                        @click="handleDeleteUser"
                    >
                        <Loader2 v-if="isDeleting" class="mr-2 h-3.5 w-3.5 animate-spin" />
                        {{ isDeleting ? 'Menghapus...' : 'Ya, Hapus Pengguna' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
