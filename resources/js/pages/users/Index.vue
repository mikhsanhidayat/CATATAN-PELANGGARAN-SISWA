<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

type UserRow = {
    id_users: number;
    nama_users: string;
    nama_role: string | null;
    nama_jabatan: string | null;
    nama_tahun_ajaran: string | null;
    email: string;
    no_hp: string;
};

defineProps<{
    users: UserRow[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Data Users',
                href: '/users',
            },
        ],
    },
});
</script>

<template>
    <Head title="Data Users" />

    <div class="p-4">
        <h1 class="mb-2 text-2xl font-semibold">Data Users</h1>
        <p class="mb-6 text-sm text-muted-foreground">
            Daftar akun yang terdaftar dalam aplikasi.
        </p>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-left text-sm">
                <thead class="border-b">
                    <tr>
                        <th class="p-4">Nama</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Jabatan terbaru</th>
                        <th class="p-4">Tahun ajaran</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Nomor HP</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="user in users"
                        :key="user.id_users"
                        class="border-b last:border-b-0"
                    >
                        <td class="p-4">{{ user.nama_users }}</td>
                        <td class="p-4">{{ user.nama_role ?? '-' }}</td>
                        <td class="p-4">
                            {{ user.nama_jabatan ?? 'Belum ada' }}
                        </td>
                        <td class="p-4">
                            {{ user.nama_tahun_ajaran ?? '-' }}
                        </td>
                        <td class="p-4">{{ user.email }}</td>
                        <td class="p-4">{{ user.no_hp }}</td>
                    </tr>

                    <tr v-if="users.length === 0">
                        <td colspan="6" class="p-6 text-center">
                            Belum ada user.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
