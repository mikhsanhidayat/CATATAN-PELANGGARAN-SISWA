<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Spinner } from '@/components/ui/spinner';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        wide: true,
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const showPassword = ref(false);
</script>

<template>
    <Head title="Login - DisiplinKu" />

    <div
        class="flex min-h-svh items-center justify-center bg-[#edf2f9] px-4 py-8 font-sans text-slate-800 sm:px-6 lg:px-8"
    >
        <main class="w-full max-w-md lg:max-w-6xl">
            <header class="mb-5 text-center lg:hidden">
                <span class="text-2xl font-extrabold tracking-tight text-[#2f7bf6]">
                    Disiplin<span class="text-blue-800">Ku</span>
                </span>
            </header>

            <div
                class="grid min-h-[620px] overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-2xl shadow-blue-900/10 lg:grid-cols-2 lg:rounded-[2.5rem]"
            >
                <!-- Panel form -->
                <section class="flex flex-col justify-center p-7 sm:p-10 lg:p-14">
                    <div>
                        <div class="mb-12 hidden items-center gap-3 lg:flex">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-slate-900 text-lg font-black text-white"
                            >
                                D
                            </div>
                            <span class="text-xl font-extrabold tracking-tight text-slate-900">
                                Disiplin<span class="text-blue-600">Ku</span>
                            </span>
                        </div>

                        <h1
                            class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl"
                        >
                            Selamat datang kembali!
                        </h1>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">
                            Masuk dengan email terdaftar untuk mengelola data
                            pelanggaran dan ketertiban siswa.
                        </p>

                        <div
                            v-if="status"
                            class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                        >
                            {{ status }}
                        </div>

                        <Form
                            v-bind="store.form()"
                            :reset-on-success="['password']"
                            v-slot="{ errors, processing }"
                            class="mt-8 space-y-5"
                        >
                            <div>
                                <label
                                    for="email"
                                    class="mb-2 block text-sm font-semibold text-slate-700"
                                >
                                    Email
                                </label>

                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    v-focus
                                    autocomplete="email"
                                    placeholder="nama@sekolah.sch.id"
                                    class="w-full rounded-2xl border border-slate-200 bg-[#f4f7fb] px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="errors.email"
                                />
                            </div>

                            <div>
                                <label
                                    for="password"
                                    class="mb-2 block text-sm font-semibold text-slate-700"
                                >
                                    Password
                                </label>

                                <div class="relative">
                                    <input
                                        id="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="Masukkan password"
                                        class="w-full rounded-2xl border border-slate-200 bg-[#f4f7fb] py-3.5 pr-28 pl-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
                                    />

                                    <button
                                        type="button"
                                        class="absolute inset-y-0 right-0 px-4 text-xs font-semibold text-blue-600 hover:text-blue-800"
                                        :aria-label="
                                            showPassword
                                                ? 'Sembunyikan password'
                                                : 'Tampilkan password'
                                        "
                                        @click="showPassword = !showPassword"
                                    >
                                        {{ showPassword ? 'Sembunyikan' : 'Tampilkan' }}
                                    </button>
                                </div>

                                <InputError
                                    class="mt-2"
                                    :message="errors.password"
                                />
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <label
                                    for="remember"
                                    class="flex items-center gap-2 text-sm text-slate-600"
                                >
                                    <Checkbox
                                        id="remember"
                                        name="remember"
                                    />
                                    <span>Ingat saya</span>
                                </label>

                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="text-sm font-semibold text-blue-600"
                                >
                                    Lupa password?
                                </TextLink>
                            </div>

                            <button
                                type="submit"
                                :disabled="processing"
                                data-test="login-button"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#2f7bf6] px-6 py-3.5 text-sm font-bold text-white shadow-md shadow-blue-500/25 transition hover:bg-blue-600 disabled:cursor-wait disabled:opacity-70"
                            >
                                <Spinner v-if="processing" />
                                {{ processing ? 'Memproses...' : 'Masuk' }}
                            </button>
                        </Form>

                        <div class="mt-6">
                            <PasskeyVerify />
                        </div>
                    </div>
                </section>

                <!-- Panel ilustrasi; angka di bawah hanya contoh tampilan. -->
                <aside
                    class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-b from-[#1b6af5] via-[#1258d4] to-[#0c49b6] p-10 text-white lg:flex xl:p-12"
                >
                    <div
                        class="pointer-events-none absolute -top-12 -right-12 size-64 rounded-full bg-white/10 blur-3xl"
                    />
                    <div
                        class="pointer-events-none absolute -bottom-20 -left-20 size-80 rounded-full bg-blue-300/20 blur-3xl"
                    />

                    <div class="relative z-10 flex justify-end">
                        <div
                            class="flex size-11 items-center justify-center rounded-xl bg-white text-lg font-black text-blue-600 shadow-lg"
                        >
                            D
                        </div>
                    </div>

                    <div class="relative z-10 mx-auto w-full max-w-md">
                        <div
                            class="rounded-3xl border border-white/20 bg-white p-5 text-slate-800 shadow-2xl"
                        >
                            <div
                                class="flex items-center justify-between border-b border-slate-100 pb-4"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="size-2.5 rounded-full bg-rose-500" />
                                    <span class="size-2.5 rounded-full bg-amber-400" />
                                    <span class="size-2.5 rounded-full bg-emerald-500" />
                                    <span class="ml-2 text-xs font-bold">
                                        DisiplinKu Web
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-blue-600">
                                    BK
                                </span>
                            </div>

                            <div class="my-4 grid grid-cols-3 gap-2">
                                <div class="rounded-2xl bg-blue-50 p-3">
                                    <span class="block text-[10px] text-slate-500">
                                        Total Kasus
                                    </span>
                                    <strong class="text-lg text-blue-700">142</strong>
                                </div>
                                <div class="rounded-2xl bg-emerald-50 p-3">
                                    <span class="block text-[10px] text-slate-500">
                                        Selesai Dibina
                                    </span>
                                    <strong class="text-lg text-emerald-700">138</strong>
                                </div>
                                <div class="rounded-2xl bg-amber-50 p-3">
                                    <span class="block text-[10px] text-slate-500">
                                        Surat Panggilan
                                    </span>
                                    <strong class="text-lg text-amber-700">4</strong>
                                </div>
                            </div>

                            <div class="mb-3 rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-xs">
                                    <strong>Tingkat Ketertiban Siswa</strong>
                                    <strong class="text-emerald-600">98,2%</strong>
                                </div>
                                <svg
                                    class="mt-4 h-16 w-full"
                                    viewBox="0 0 300 70"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M0 60 H300"
                                        stroke="#e2e8f0"
                                        stroke-dasharray="3 3"
                                    />
                                    <path
                                        d="M10 50 Q 50 15, 90 40 T 170 30 T 230 18 T 290 28"
                                        stroke="#2563eb"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                    />
                                    <circle
                                        cx="230"
                                        cy="18"
                                        r="4"
                                        fill="#2563eb"
                                    />
                                </svg>
                            </div>

                            <div class="space-y-2 text-xs">
                                <div
                                    class="flex justify-between rounded-xl bg-slate-50 p-3"
                                >
                                    <span>Terlambat Masuk Kelas</span>
                                    <strong class="text-rose-600">-10 Poin</strong>
                                </div>
                                <div
                                    class="flex justify-between rounded-xl bg-slate-50 p-3"
                                >
                                    <span>Pelanggaran Seragam</span>
                                    <strong class="text-amber-600">-5 Poin</strong>
                                </div>
                            </div>
                        </div>

                        <p class="mt-3 text-center text-xs text-blue-100">
                            Contoh tampilan dashboard
                        </p>
                    </div>

                    <div class="relative z-10 text-center">
                        <h2 class="text-2xl font-extrabold">
                            Kelola kedisiplinan siswa dengan mudah.
                        </h2>
                        <p class="mt-2 text-sm text-blue-100">
                            Catat pelanggaran dan pantau pembinaan dalam satu tempat.
                        </p>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</template>
