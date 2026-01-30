<script setup lang="ts">
import { kerjakanQuiz, siwaQuizStart } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { AlertCircleIcon, ArrowRightIcon, BookOpenIcon, CheckCircle2Icon, ClockIcon, PlayIcon, RefreshCcwIcon } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<{
    quiz: any;
    pengerjaan: any;
}>();

// --- LOGIKA TIMER REAL-TIME ---
const totalSeconds = ref(0);
let timerInterval: any = null;

const calculateInitialSeconds = () => {
    if (!props.pengerjaan) return 0;
    if (props.pengerjaan.remaining_time !== null) {
        return props.pengerjaan.remaining_time * 60;
    }
    const startTime = new Date(props.pengerjaan.start_date).getTime();
    const durationInMs = props.quiz.durasi * 60 * 1000;
    const endTime = startTime + durationInMs;
    const now = new Date().getTime();
    return Math.max(0, Math.floor((endTime - now) / 1000));
};

const formatTime = (seconds: number) => {
    if (seconds <= 0) return '00:00:00';
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    return [h, m, s].map((v) => (v < 10 ? '0' + v : v)).join(':');
};

const displayTime = computed(() => formatTime(totalSeconds.value));

// --- LOGIKA STATUS (DIPERBARUI UNTUK REMEDIAL) ---
const statusPengerjaan = computed(() => {
    // 1. Jika belum pernah mengerjakan sama sekali
    if (!props.pengerjaan) return 'BELUM_MULAI';

    // 2. Jika sudah selesai, cek apakah nilainya sudah lulus (>= KKM)
    if (props.pengerjaan.end_date) {
        if (props.pengerjaan.score_result < props.quiz.kkm) {
            return 'REMEDIAL'; // Nilai di bawah KKM, izinkan mulai ulang
        }
        return 'SUDAH_SELESAI'; // Sudah lulus
    }

    // 3. Masih dalam proses pengerjaan (timer berjalan)
    return 'SEDANG_BERJALAN';
});

const formatTanggal = (dateString: string) => {
    return new Date(dateString).toLocaleString('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
    });
};

onMounted(() => {
    if (statusPengerjaan.value === 'SEDANG_BERJALAN') {
        totalSeconds.value = calculateInitialSeconds();
        timerInterval = setInterval(() => {
            if (totalSeconds.value > 0) {
                totalSeconds.value--;
            } else {
                clearInterval(timerInterval);
            }
        }, 1000);
    }
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});
</script>

<template>
    <div class="px-4 py-12 font-serif text-[#333]">
        <div class="mx-auto max-w-2xl">
            <div class="overflow-hidden border-2 border-[#003366] bg-white shadow-[8px_8px_0px_rgba(0,0,0,0.1)]">
                <div class="flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] p-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="rounded-sm bg-white p-1 text-xs leading-none font-black text-[#003366] italic">UBK</div>
                        <h2 class="text-xs font-bold tracking-widest uppercase">Konfirmasi Data Ujian</h2>
                    </div>
                    <span class="font-mono text-[10px] uppercase italic opacity-70">v.2026.1.1</span>
                </div>

                <div class="p-8">
                    <div class="mb-8 border-b-2 border-dashed border-gray-300 pb-6 text-center md:text-left">
                        <span class="text-[10px] font-bold tracking-widest text-blue-600 uppercase">Mata Uji:</span>
                        <h1 class="mt-1 text-3xl leading-tight font-black text-gray-900 uppercase">{{ quiz.judul }}</h1>
                    </div>

                    <div class="mb-8 grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 border border-gray-300 bg-[#f8f9fa] p-3 shadow-inner">
                            <ClockIcon class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-[9px] font-bold text-gray-500 uppercase">Durasi</p>
                                <p class="text-sm font-bold">{{ quiz.durasi }} Menit</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 border border-gray-300 bg-[#f8f9fa] p-3 shadow-inner">
                            <BookOpenIcon class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-[9px] font-bold text-gray-500 uppercase">Total Soal</p>
                                <p class="text-sm font-bold">{{ quiz.total_soal }} Butir</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="statusPengerjaan === 'REMEDIAL'" class="mb-8 animate-pulse border border-red-400 bg-red-50 p-4">
                        <div class="flex gap-3">
                            <RefreshCcwIcon class="h-5 w-5 shrink-0 text-red-600" />
                            <div>
                                <h3 class="text-xs font-bold text-red-900 uppercase underline">Kesempatan Remedial:</h3>
                                <p class="text-[11px] font-medium text-red-800">
                                    Nilai sebelumnya (<strong>{{ Math.round(pengerjaan.score_result) }}</strong
                                    >) belum mencapai KKM (<strong>{{ quiz.kkm }}</strong
                                    >). Anda diperbolehkan mengerjakan ulang ujian ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="mb-8 border border-yellow-400 bg-yellow-50 p-4 shadow-sm">
                        <div class="flex gap-3">
                            <AlertCircleIcon class="h-5 w-5 shrink-0 text-yellow-600" />
                            <div>
                                <h3 class="text-xs font-bold text-yellow-900 uppercase underline">Peringatan Penting:</h3>
                                <ul class="mt-2 list-inside list-disc space-y-1 text-[11px] font-medium text-yellow-800">
                                    <li>Pastikan koneksi internet Anda stabil.</li>
                                    <li>Waktu akan berjalan saat tombol <strong>MULAI</strong> ditekan.</li>
                                    <li>
                                        Ambang Batas Kelulusan (KKM): <strong>{{ quiz.kkm }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center border-t-2 border-gray-100 pt-8">
                        <div v-if="statusPengerjaan === 'BELUM_MULAI' || statusPengerjaan === 'REMEDIAL'" class="w-full text-center">
                            <Link
                                method="post"
                                :href="siwaQuizStart({ id: quiz.id }).url"
                                class="inline-flex w-full items-center justify-center border-b-4 border-black px-10 py-4 text-sm font-bold tracking-widest text-white uppercase shadow-lg transition-all active:translate-y-[2px] active:border-b-0 sm:w-auto"
                                :class="statusPengerjaan === 'REMEDIAL' ? 'bg-orange-600 hover:bg-black' : 'bg-[#006633] hover:bg-black'"
                            >
                                <PlayIcon v-if="statusPengerjaan === 'BELUM_MULAI'" class="mr-2 h-4 w-4 fill-current" />
                                <RefreshCcwIcon v-else class="mr-2 h-4 w-4" />
                                {{ statusPengerjaan === 'REMEDIAL' ? 'Mulai Remedial' : 'Mulai Ujian Sekarang' }}
                            </Link>
                        </div>

                        <div v-else-if="statusPengerjaan === 'SEDANG_BERJALAN'" class="w-full">
                            <div class="relative mb-8 overflow-hidden border-2 border-orange-400 bg-orange-50 p-6 text-center font-mono shadow-inner">
                                <span class="absolute top-0 left-0 bg-orange-400 px-2 py-0.5 text-[8px] font-bold text-white uppercase"
                                    >Sisa Waktu</span
                                >
                                <div class="py-2 text-5xl font-black tracking-tighter text-orange-700 tabular-nums">
                                    {{ displayTime }}
                                </div>
                            </div>
                            <div class="flex justify-center">
                                <Link
                                    :href="kerjakanQuiz({ id: quiz.id, history_id: pengerjaan.id }).url"
                                    class="inline-flex w-full items-center justify-center border-b-4 border-black bg-[#003366] px-12 py-4 text-sm font-bold tracking-widest text-white uppercase shadow-lg transition-all hover:bg-black active:translate-y-[2px] active:border-b-0 sm:w-auto"
                                >
                                    Lanjutkan Pengerjaan
                                    <ArrowRightIcon class="ml-2 h-4 w-4" />
                                </Link>
                            </div>
                        </div>

                        <div v-else-if="statusPengerjaan === 'SUDAH_SELESAI'" class="w-full text-center">
                            <div class="border-2 border-green-600 bg-green-50 p-8 shadow-sm">
                                <CheckCircle2Icon class="mx-auto mb-4 h-12 w-12 text-green-600" />
                                <h3 class="text-lg font-black text-green-900 uppercase">Ujian Telah Lulus</h3>
                                <div class="my-6 border-t border-green-200"></div>
                                <span class="text-[9px] font-bold tracking-widest text-green-500 uppercase">Skor Kompetensi</span>
                                <div class="mt-1 font-mono text-6xl font-black text-green-700">
                                    {{ Math.round(pengerjaan.score_result) }}
                                </div>
                                <Link
                                    href="/"
                                    class="mt-8 inline-block border-b border-gray-300 text-[10px] font-bold text-gray-400 uppercase italic hover:text-blue-600"
                                >
                                    &larr; Kembali ke dashboard
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-between border-t border-gray-300 bg-gray-100 p-2 px-4 font-mono text-[9px] tracking-tighter text-gray-400 uppercase"
                >
                    <span>Server Status: Terkoneksi</span>
                    <span>Waktu: {{ new Date().toLocaleTimeString() }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
button,
input,
h1,
h2,
h3,
p,
div,
span,
a {
    font-family: 'Arial', sans-serif;
    border-radius: 0px !important;
}

.font-mono {
    font-family: 'Courier New', Courier, monospace;
}

.rounded-sm {
    border-radius: 2px !important;
}
</style>
