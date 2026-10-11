
<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import KartuStatistikUsers from '@/components/Users/KartuStatistikUsers.vue';
import TabelUsers from '@/components/Users/TabelUsers.vue';
import ModalTambahUser from '@/components/Users/ModalTambahUser.vue';
import { Button } from '@/components/ui/button';
import { UserPlus } from '@lucide/vue';

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

type UserCards = {
    total_users: number | string;
    total_admin: number | string;
    total_guru: number | string;
    total_petugas: number | string;
};

type RoleOption = {
    id_role: number;
    nama_role: string;
};

type UserFilters = {
    cari: string | null;
    id_role: number | null;
};

const props = defineProps<{
    users: UserRow[];
    cards: UserCards | null;
    roles: RoleOption[];
    filters: UserFilters;
}>();

// State modal tambah pengguna
const isModalOpen = ref(false);

// Mengirim parameter Search dan Filter Role ke Laravel.
const applyFilters = (
    cari: string,
    idRole: number | null
) => {
    router.get(
        '/users',
        {
            ...(cari ? { cari } : {}),
            ...(idRole !== null ? { id_role: idRole } : {}),
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['users', 'filters'],
        }
    );
};

// Mengubah pencarian tanpa menghapus Filter Role.
const handleSearch = (value: string) => {
    applyFilters(value, props.filters.id_role);
};

// Mengubah Filter Role tanpa menghapus pencarian.
const handleRoleChange = (idRole: number | null) => {
    applyFilters(props.filters.cari ?? '', idRole);
};
</script>

<template>
    <Head title="Data Users" />

    <div
        class="mx-auto flex w-full max-w-7xl flex-1 flex-col space-y-6 p-4 md:p-6 lg:p-8"
    >
        <!-- Header Halaman -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1
                    class="text-2xl font-bold tracking-tight text-slate-900 dark:text-neutral-100"
                >
                    Manajemen Pengguna
                </h1>

                <p
                    class="mt-1 text-xs text-slate-500 sm:text-sm dark:text-neutral-400"
                >
                    Kelola akun dan informasi pengguna sistem DisiplinKu.
                </p>
            </div>

            <!-- Tombol Tambah Pengguna -->
            <div>
                <Button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-blue-700 transition-colors cursor-pointer dark:bg-blue-600 dark:hover:bg-blue-700"
                    @click="isModalOpen = true"
                >
                    <UserPlus class="h-4 w-4" />
                    <span>Tambah Pengguna</span>
                </Button>
            </div>
        </div>

        <!-- Card Statistik Users -->
        <KartuStatistikUsers :cards="cards" />

        <!-- Tabel dan Filter Users -->
        <TabelUsers
            :users="users"
            :roles="roles"
            :cari="filters.cari"
            :id-role="filters.id_role"
            @search="handleSearch"
            @role-change="handleRoleChange"
        />

        <!-- Modal Tambah Pengguna -->
        <ModalTambahUser
            v-model:open="isModalOpen"
            :roles="roles"
        />
    </div>
</template>
