<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Loader2 } from '@lucide/vue';

type RoleOption = {
    id_role: number;
    nama_role: string;
};

const props = defineProps<{
    open: boolean;
    roles: RoleOption[];
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const showPassword = ref(false);

const form = useForm({
    id_role: '',
    nama_users: '',
    jenis_kelamin: 'Laki-laki',
    tempat_lahir: '',
    tanggal_lahir: '',
    alamat: '',
    no_hp: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const closeModal = () => {
    if (form.processing) return;
    emit('update:open', false);
    form.clearErrors();
};

const handleSubmit = () => {
    form.post('/users', {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:open', false);
            form.reset();
        },
    });
};
</script>

<template>
    <Dialog :open="open" @update:open="(val) => { if (!val) closeModal(); else emit('update:open', true); }">
        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800">
            <DialogHeader class="border-b border-slate-100 pb-4 dark:border-neutral-800">
                <DialogTitle class="text-lg font-bold text-slate-900 dark:text-neutral-100">
                    Tambah Pengguna Baru
                </DialogTitle>
                <DialogDescription class="text-xs text-slate-500 dark:text-neutral-400">
                    Lengkapi seluruh formulir data diri dan akun pengguna di bawah ini.
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4 py-2">
                <!-- Baris 1: Nama Pengguna & Role -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Nama Pengguna -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Nama Pengguna <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.nama_users"
                            type="text"
                            maxlength="150"
                            placeholder="Contoh: Budi Santoso, S.Pd."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.nama_users }"
                        />
                        <p v-if="form.errors.nama_users" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.nama_users }}
                        </p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Role Sistem <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.id_role"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition-colors focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.id_role }"
                        >
                            <option value="" disabled>Pilih Role Pengguna</option>
                            <option
                                v-for="role in roles"
                                :key="role.id_role"
                                :value="String(role.id_role)"
                            >
                                {{ role.nama_role }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_role" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.id_role }}
                        </p>
                    </div>
                </div>

                <!-- Baris 2: Jenis Kelamin & Nomor HP -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-4 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-neutral-300">
                                <input
                                    v-model="form.jenis_kelamin"
                                    type="radio"
                                    value="Laki-laki"
                                    class="text-blue-600 focus:ring-blue-500"
                                />
                                Laki-laki
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-neutral-300">
                                <input
                                    v-model="form.jenis_kelamin"
                                    type="radio"
                                    value="Perempuan"
                                    class="text-blue-600 focus:ring-blue-500"
                                />
                                Perempuan
                            </label>
                        </div>
                        <p v-if="form.errors.jenis_kelamin" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.jenis_kelamin }}
                        </p>
                    </div>

                    <!-- Nomor HP -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.no_hp"
                            type="text"
                            maxlength="20"
                            placeholder="Contoh: 08123456789"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.no_hp }"
                        />
                        <p v-if="form.errors.no_hp" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.no_hp }}
                        </p>
                    </div>
                </div>

                <!-- Baris 3: Tempat Lahir & Tanggal Lahir -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Tempat Lahir -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Tempat Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.tempat_lahir"
                            type="text"
                            maxlength="100"
                            placeholder="Contoh: Tasikmalaya"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.tempat_lahir }"
                        />
                        <p v-if="form.errors.tempat_lahir" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.tempat_lahir }}
                        </p>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.tanggal_lahir"
                            type="date"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.tanggal_lahir }"
                        />
                        <p v-if="form.errors.tanggal_lahir" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.tanggal_lahir }}
                        </p>
                    </div>
                </div>

                <!-- Baris 4: Alamat Domisili -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                        Alamat Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        v-model="form.alamat"
                        rows="2"
                        maxlength="500"
                        placeholder="Contoh: Jl. Sukamantri No. 12, Tasikmalaya"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 resize-none"
                        :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.alamat }"
                    ></textarea>
                    <p v-if="form.errors.alamat" class="mt-1 text-[11px] font-medium text-rose-500">
                        {{ form.errors.alamat }}
                    </p>
                </div>

                <!-- Pembatas Akun Login -->
                <div class="border-t border-slate-100 pt-3 dark:border-neutral-800">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-neutral-100 mb-3">
                        Informasi Akun Login
                    </h4>
                </div>

                <!-- Baris 5: Email Login -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        maxlength="255"
                        placeholder="Contoh: nama@sekolah.sch.id"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.email }"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-[11px] font-medium text-rose-500">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Baris 6: Password & Konfirmasi Password -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Minimal 8 karakter"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.password }"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.password_confirmation"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Ulangi password di atas"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 outline-none transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500': form.errors.password_confirmation }"
                        />
                        <p v-if="form.errors.password_confirmation" class="mt-1 text-[11px] font-medium text-rose-500">
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>
                </div>

                <!-- Checkbox Lihat Password -->
                <div class="flex items-center gap-2">
                    <input
                        id="showPass"
                        v-model="showPassword"
                        type="checkbox"
                        class="rounded text-blue-600 focus:ring-blue-500"
                    />
                    <label for="showPass" class="text-xs text-slate-600 dark:text-neutral-400 cursor-pointer">
                        Tampilkan karakter password
                    </label>
                </div>

                <!-- Footer Aksi -->
                <DialogFooter class="border-t border-slate-100 pt-4 gap-2 dark:border-neutral-800">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="form.processing"
                        class="rounded-xl border-slate-200 text-xs text-slate-600 hover:bg-slate-50 dark:border-neutral-700 dark:text-neutral-300"
                        @click="closeModal"
                    >
                        Batal
                    </Button>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-blue-600 px-5 text-xs font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-70 dark:bg-blue-600 dark:hover:bg-blue-700"
                    >
                        <Loader2 v-if="form.processing" class="mr-2 h-3.5 w-3.5 animate-spin" />
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pengguna' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
