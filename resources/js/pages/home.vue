<script setup lang="ts">
import { formatTanggal } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
// Icons
import HugeiconsTime02 from '@/icons/HugeiconsTime02.vue'; // Icon jam
import MaterialSymbolsCheckCircleUnreadOutline from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import { siwaQuizDetail } from '@/routes';
import { kerjakan } from '@/routes/siswa/tugas';

const page = usePage();
const user = computed(() => page.props.auth.user as any);
const pendingTasks = computed(() => (page.props.pending_tugas as any) || []);

// Helper hitung sisa hari
const getDaysLeft = (deadline: string) => {
    const today = new Date();
    const due = new Date(deadline);
    const diff = Math.ceil((due.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
    return diff;
};
const quizs = computed(() => {
    return usePage().props.pendingQuiz as any;
});
const remedials = computed(() => {
    return usePage().props.remedial as any;
});
</script>

<template>
    <div class="mx-auto mt-4 max-w-7xl pb-20">
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-neutral-800">Halo, {{ user.nama }}</h2>
                            <p class="mt-1 text-sm text-neutral-500">Selamat datang kembali di ruang belajar Anda.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="rounded border border-neutral-200 bg-neutral-100 px-3 py-1 text-xs font-semibold tracking-wider text-neutral-600 uppercase"
                            >
                                {{ user.role }}
                            </div>
                            <div v-if="user.kelas" class="rounded border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ user.kelas.nama }}
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="user.role === 'siswa'" class="sticky top-6 space-y-4 lg:col-span-4">
                    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-neutral-100 bg-blue-50/30 px-5 py-4">
                            <h3 class="text-sm font-bold text-neutral-800">Remedials Quiz</h3>
                            <span
                                v-if="remedials.active_quiz.length > 0"
                                class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-600"
                            >
                                {{ remedials.active_quiz.length }}
                            </span>
                        </div>

                        <div v-if="remedials.active_quiz.length > 0" class="divide-y divide-neutral-100">
                            <div v-for="quiz in remedials.active_quiz" :key="quiz.id" class="group relative p-4 transition-all hover:bg-neutral-50">
                                <div class="absolute top-0 bottom-0 left-0 w-[3px] bg-blue-500"></div>
                                <div class="pl-2">
                                    <div class="mb-1 flex items-start justify-between text-[10px] font-bold uppercase">
                                        <span class="text-blue-600">{{ quiz.durasi }} Menit</span>
                                        <span class="text-neutral-400">ID: #{{ quiz.id }}</span>
                                    </div>
                                    <h4 class="mb-3 text-sm font-semibold text-neutral-800">{{ quiz.judul }}</h4>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] text-neutral-500 italic">Hingga: {{ formatTanggal(quiz.selesai) }}</span>
                                        <Link
                                            :href="siwaQuizDetail({ id: quiz.id })"
                                            class="rounded bg-blue-600 px-3 py-1 text-xs font-bold text-white hover:bg-blue-700"
                                        >
                                            Kerjakan Remedial
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="px-5 py-6 text-center text-xs text-neutral-400 italic">Tidak ada quiz aktif.</div>
                    </div>
                </div>
                <div v-if="user.role === 'siswa'" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-neutral-200 bg-white p-4">
                        <p class="text-xs font-medium text-neutral-400 uppercase">Tugas Menunggu</p>
                        <p class="mt-1 text-2xl font-bold text-neutral-800">{{ pendingTasks.length }}</p>
                    </div>
                </div>
            </div>

            <div v-if="user.role === 'siswa'" class="sticky top-6 space-y-4 lg:col-span-4">
                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-neutral-100 bg-blue-50/30 px-5 py-4">
                        <h3 class="text-sm font-bold text-neutral-800">Quiz Aktif</h3>
                        <span v-if="quizs.active_quiz.length > 0" class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-600">
                            {{ quizs.active_quiz.length }}
                        </span>
                    </div>

                    <div v-if="quizs.active_quiz.length > 0" class="divide-y divide-neutral-100">
                        <div v-for="quiz in quizs.active_quiz" :key="quiz.id" class="group relative p-4 transition-all hover:bg-neutral-50">
                            <div class="absolute top-0 bottom-0 left-0 w-[3px] bg-blue-500"></div>
                            <div class="pl-2">
                                <div class="mb-1 flex items-start justify-between text-[10px] font-bold uppercase">
                                    <span class="text-blue-600">{{ quiz.durasi }} Menit</span>
                                    <span class="text-neutral-400">ID: #{{ quiz.id }}</span>
                                </div>
                                <h4 class="mb-3 text-sm font-semibold text-neutral-800">{{ quiz.judul }}</h4>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-neutral-500 italic">Hingga: {{ formatTanggal(quiz.selesai) }}</span>
                                    <Link
                                        :href="siwaQuizDetail({ id: quiz.id })"
                                        class="rounded bg-blue-600 px-3 py-1 text-xs font-bold text-white hover:bg-blue-700"
                                    >
                                        Kerjakan
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="px-5 py-6 text-center text-xs text-neutral-400 italic">Tidak ada quiz aktif.</div>
                </div>

                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
                        <h3 class="font-bold text-neutral-800">Tugas Anda</h3>
                        <Link href="/siswa/tugas" class="text-xs font-medium text-neutral-400 transition-colors hover:text-neutral-800">
                            Lihat Semua
                        </Link>
                    </div>

                    <div v-if="pendingTasks.length > 0" class="divide-y divide-neutral-100">
                        <div v-for="task in pendingTasks" :key="task.tugasID" class="group relative p-4 transition-all hover:bg-neutral-50">
                            <div
                                class="absolute top-0 bottom-0 left-0 w-[3px]"
                                :class="getDaysLeft(task.deadline) <= 1 ? 'bg-red-500' : 'bg-transparent group-hover:bg-blue-500'"
                            ></div>

                            <div class="pl-2">
                                <div class="mb-1 flex items-start justify-between">
                                    <span class="text-[10px] font-bold tracking-wide text-neutral-400 uppercase">
                                        {{ task.matpel?.nama }}
                                    </span>

                                    <span
                                        class="text-[10px] font-medium"
                                        :class="getDaysLeft(task.deadline) <= 1 ? 'text-red-600' : 'text-neutral-400'"
                                    >
                                        {{ getDaysLeft(task.deadline) === 0 ? 'Hari Ini' : getDaysLeft(task.deadline) + ' hari lagi' }}
                                    </span>
                                </div>

                                <Link :href="kerjakan({ id: task.tugasID })" class="mb-2 block">
                                    <h4 class="text-sm leading-snug font-semibold text-neutral-800 transition-colors group-hover:text-blue-600">
                                        {{ task.title }}
                                    </h4>
                                </Link>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-xs text-neutral-500">
                                        <HugeiconsTime02 class="text-neutral-400" />
                                        <span>{{ formatTanggal(task.deadline) }}</span>
                                    </div>

                                    <Link
                                        :href="kerjakan({ id: task.tugasID })"
                                        class="text-xs font-semibold text-blue-600 opacity-0 transition-opacity group-hover:opacity-100 hover:underline"
                                    >
                                        Kerjakan &rarr;
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="px-6 py-12 text-center">
                        <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-neutral-50 text-neutral-300">
                            <MaterialSymbolsCheckCircleUnreadOutline class="text-2xl" />
                        </div>
                        <p class="text-sm font-semibold text-neutral-900">Tidak ada tagihan tugas</p>
                        <p class="mt-1 text-xs text-neutral-500">Anda bisa bersantai sejenak.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
