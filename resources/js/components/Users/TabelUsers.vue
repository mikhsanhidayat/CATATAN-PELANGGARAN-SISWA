
<script setup lang="ts">
import { ref, watch, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Search, X, ChevronDown } from '@lucide/vue';

type UserRow = {
    id_users: number;
    nama_users: string;
    id_role: number | null;
    nama_role: string | null;
    nama_jabatan: string | null;
    nama_tahun_ajaran: string | null;
    email: string;
    no_hp: string | null;
};

type RoleOption = {
    id_role: number;
    nama_role: string;
};

const props = defineProps<{
    users: UserRow[];
    roles: RoleOption[];
    cari: string | null;
    idRole: number | null;
}>();

const emit = defineEmits<{
    (e: 'search', value: string): void;
    (e: 'role-change', value: number | null): void;
}>();

// State lokal untuk input pencarian.
const searchInput = ref(props.cari ?? '');

// State lokal untuk dropdown Role.
const selectedRole = ref(
    props.idRole !== null ? String(props.idRole) : ''
);

// Timer debounce pencarian.
let searchTimeout: ReturnType<typeof setTimeout> | undefined;

// Memantau perubahan input Search.
watch(searchInput, (value) => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        const keyword = value.trim();

        if (keyword !== (props.cari ?? '')) {
            emit('search', keyword);
        }
    }, 400);
});

// Menyinkronkan Search dengan props dari Laravel.
watch(
    () => props.cari,
    (value) => {
        if ((value ?? '') !== searchInput.value.trim()) {
            searchInput.value = value ?? '';
        }
    }
);

// Menyinkronkan dropdown dengan filter dari Laravel.
watch(
    () => props.idRole,
    (value) => {
        selectedRole.value =
            value !== null ? String(value) : '';
    }
);

// Mengirim ID Role yang dipilih ke Index.vue.
const handleRoleChange = () => {
    const idRole = selectedRole.value
        ? Number(selectedRole.value)
        : null;

    emit('role-change', idRole);
};

// Menghapus teks pencarian.
const resetSearch = () => {
    searchInput.value = '';
};

// Membersihkan timer saat komponen dilepas.
onBeforeUnmount(() => {
    clearTimeout(searchTimeout);
});

// Menentukan warna badge berdasarkan Role.
const roleClass = (role: string | null) => {
    switch (role) {
        case 'Admin':
            return 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400';
        case 'Guru':
            return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400';
        case 'Petugas':
            return 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400';
        default:
            return 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-400';
    }
};
</script>

<template>
    <div
        class="mb-6 overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-xs dark:border-neutral-800 dark:bg-neutral-900"
    >
        <!-- Header Tabel -->
        <div
            class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 lg:flex-row lg:items-center dark:border-neutral-800"
        >
            <div>
                <h2
                    class="text-base font-bold text-slate-900 dark:text-neutral-100"
                >
                    Daftar Pengguna
                </h2>

                <p
                    class="mt-0.5 text-xs text-slate-500 dark:text-neutral-400"
                >
                    Informasi akun pengguna yang terdaftar dalam sistem
                </p>
            </div>

            <!-- Search dan Filter -->
            <div class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">

                <!-- Input Search -->
                <div class="relative w-full sm:w-60">
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    />

                    <input
                        v-model="searchInput"
                        type="search"
                        maxlength="150"
                        placeholder="Cari nama / email..."
                        aria-label="Cari nama atau email pengguna"
                        class="w-full rounded-xl border border-slate-200/70 bg-slate-50 py-2 pl-9 pr-9 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                    />

                    <button
                        v-if="searchInput"
                        type="button"
                        aria-label="Hapus pencarian"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-neutral-200"
                        @click="resetSearch"
                    >
                        <X class="h-3.5 w-3.5" />
                    </button>
                </div>

                <!-- Dropdown Filter Role -->
                <div class="relative w-full sm:w-40">
                    <select
                        v-model="selectedRole"
                        aria-label="Filter berdasarkan Role"
                        class="w-full appearance-none rounded-xl border border-slate-200/70 bg-slate-50 py-2 pl-3 pr-9 text-xs text-slate-700 outline-none transition-colors focus:border-blue-400 focus:ring-1 focus:ring-blue-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        @change="handleRoleChange"
                    >
                        <option value="">Semua Role</option>

                        <option
                            v-for="role in roles"
                            :key="role.id_role"
                            :value="String(role.id_role)"
                        >
                            {{ role.nama_role }}
                        </option>
                    </select>

                    <ChevronDown
                        class="pointer-events-none absolute right-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"
                    />
                </div>
            </div>
        </div>

        <!-- Tabel Data Users -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr
                        class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:border-neutral-800/80 dark:bg-neutral-800/30 dark:text-neutral-400"
                    >
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Role</th>
                        <th class="px-5 py-3.5">Jabatan</th>
                        <th class="px-5 py-3.5">Tahun Ajaran</th>
                        <th class="px-5 py-3.5">Nomor HP</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody
                    class="divide-y divide-slate-100 text-xs dark:divide-neutral-800/80"
                >
                    <tr
                        v-for="user in users"
                        :key="user.id_users"
                        class="transition-colors hover:bg-slate-50/60 dark:hover:bg-neutral-800/30"
                    >
                        <!-- Nama dan Email -->
                        <td class="px-5 py-3.5">
                            <div
                                class="font-semibold text-slate-900 dark:text-neutral-100"
                            >
                                {{ user.nama_users }}
                            </div>

                            <div
                                class="mt-0.5 text-[11px] text-slate-400 dark:text-neutral-500"
                            >
                                {{ user.email }}
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="whitespace-nowrap px-5 py-3.5">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                                :class="roleClass(user.nama_role)"
                            >
                                {{ user.nama_role ?? 'Belum ada' }}
                            </span>
                        </td>

                        <!-- Jabatan -->
                        <td
                            class="px-5 py-3.5 text-slate-700 dark:text-neutral-300"
                        >
                            {{ user.nama_jabatan ?? 'Belum ada' }}
                        </td>

                        <!-- Tahun Ajaran -->
                        <td
                            class="whitespace-nowrap px-5 py-3.5 text-slate-600 dark:text-neutral-400"
                        >
                            {{ user.nama_tahun_ajaran ?? '-' }}
                        </td>

                        <!-- Nomor HP -->
                        <td
                            class="whitespace-nowrap px-5 py-3.5 text-slate-600 dark:text-neutral-400"
                        >
                            {{ user.no_hp ?? '-' }}
                        </td>

                        <!-- Aksi -->
                        <td class="whitespace-nowrap px-5 py-3.5 text-right">
                            <Link
                                :href="`/users/${user.id_users}`"
                                class="text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors cursor-pointer"
                            >
                                Detail
                            </Link>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-if="users.length === 0">
                        <td
                            colspan="6"
                            class="px-5 py-10 text-center text-sm text-slate-400"
                        >
                            {{
                                cari || idRole !== null
                                    ? 'Tidak ada pengguna yang sesuai dengan filter.'
                                    : 'Belum ada pengguna yang terdaftar.'
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
