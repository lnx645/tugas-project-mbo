<script setup lang="ts">
import { kerjakanQuiz, siwaQuizStart } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { AlertCircleIcon, ArrowRightIcon, BookOpenIcon, CheckCircle2Icon, ClockIcon, PlayIcon, TimerIcon } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<{
    quiz: any;
    pengerjaan: any; // Data dari tabel quiz_siswa_histories
}>();
// --- LOGIKA TIMER REAL-TIME ---
const totalSeconds = ref(0);
let timerInterval: any = null;

const calculateInitialSeconds = () => {
    if (!props.pengerjaan) return 0;

    // Jika ada remaining_time di DB (asumsi menit), konversi ke detik
    if (props.pengerjaan.remaining_time !== null) {
        return props.pengerjaan.remaining_time * 60;
    }

    // Jika null, hitung selisih: (start_date + durasi) - sekarang
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
    // Padding nol (misal 9 jadi 09)
    return [h, m, s].map((v) => (v < 10 ? '0' + v : v)).join(':');
};

const displayTime = computed(() => formatTime(totalSeconds.value));

// --- LOGIKA STATUS ---
const statusPengerjaan = computed(() => {
    if (!props.pengerjaan) return 'BELUM_MULAI';
    if (props.pengerjaan.end_date) return 'SUDAH_SELESAI';
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
    <div class="min-h-screen bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-8">
                    <div class="mb-2 flex items-center gap-2 text-blue-600">
                        <span class="text-xs font-bold tracking-widest uppercase">Konfirmasi Ujian</span>
                    </div>
                    <h1 class="mb-6 text-3xl leading-tight font-black text-gray-900">{{ quiz.judul }}</h1>

                    <div class="flex flex-wrap gap-6 text-sm">
                        <div class="flex items-center gap-2 font-medium text-gray-700">
                            <div class="rounded-lg bg-blue-50 p-2 text-blue-600">
                                <ClockIcon class="h-4 w-4" />
                            </div>
                            <span>Durasi: {{ quiz.durasi }} Menit</span>
                        </div>
                        <div class="flex items-center gap-2 font-medium text-gray-700">
                            <div class="rounded-lg bg-indigo-50 p-2 text-indigo-600">
                                <BookOpenIcon class="h-4 w-4" />
                            </div>
                            <span>{{ quiz.total_soal }} Butir Soal</span>
                        </div>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 divide-y divide-gray-100 bg-gray-50/50 text-center md:grid-cols-2 md:divide-x md:divide-y-0 md:text-left"
                >
                    <div class="p-6">
                        <span class="mb-1 block text-[10px] font-bold tracking-wider text-gray-400 uppercase">Quiz Dibuka</span>
                        <p class="text-sm font-bold text-gray-700">{{ formatTanggal(quiz.mulai) }}</p>
                    </div>
                    <div class="p-6">
                        <span class="mb-1 block text-[10px] font-bold tracking-wider text-gray-400 uppercase">Quiz Ditutup</span>
                        <p class="text-sm font-bold text-gray-700">{{ formatTanggal(quiz.selesai) }}</p>
                    </div>
                </div>

                <div class="space-y-8 p-8">
                    <div class="rounded-lg border-l-4 border-blue-500 bg-blue-50/50 p-4">
                        <div class="flex">
                            <AlertCircleIcon class="mr-3 h-5 w-5 shrink-0 text-blue-500" />
                            <div>
                                <h3 class="text-sm font-bold text-blue-900">Peringatan:</h3>
                                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-blue-800/80">
                                    <li>Koneksi internet harus stabil selama pengerjaan.</li>
                                    <li>Waktu berjalan secara otomatis sejak Anda menekan tombol mulai.</li>
                                    <li>
                                        Skor minimal kelulusan (KKM) adalah <strong>{{ quiz.kkm }}</strong
                                        >.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <div v-if="statusPengerjaan === 'BELUM_MULAI'" class="w-full text-center">
                            <Link
                                :href="siwaQuizStart({ id: quiz.id })"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-10 py-4 text-base font-bold text-white shadow-lg shadow-blue-200 transition-all hover:bg-blue-700 active:scale-95 sm:w-auto"
                            >
                                <PlayIcon class="mr-2 h-5 w-5 fill-current" />
                                Mulai Kerjakan Sekarang
                            </Link>
                            <p class="mt-4 text-xs text-gray-400">Pastikan Anda sudah siap sebelum memulai.</p>
                        </div>

                        <div v-else-if="statusPengerjaan === 'SEDANG_BERJALAN'" class="w-full">
                            <div class="mb-8 rounded-2xl border border-orange-200 bg-orange-50 p-8 text-center shadow-inner">
                                <div class="mb-3 flex items-center justify-center gap-2 text-orange-600">
                                    <TimerIcon class="h-5 w-5 animate-pulse" />
                                    <span class="text-xs font-black tracking-widest uppercase">Sisa Waktu Anda</span>
                                </div>
                                <div class="md:text-md font-mono text-5xl font-black tracking-tight text-orange-700">
                                    {{ displayTime }}
                                </div>
                                <div class="mt-4 flex justify-center gap-8 text-[10px] font-bold tracking-tighter text-orange-400 uppercase">
                                    <span>Jam</span>
                                    <span>Menit</span>
                                    <span>Detik</span>
                                </div>
                            </div>

                            <div class="text-center">
                                <Link
                                    :href="
                                        kerjakanQuiz({
                                            id: quiz.id,
                                            history_id: pengerjaan.id,
                                        })
                                    "
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-orange-500 px-12 py-4 text-base font-bold text-white shadow-lg shadow-orange-200 transition-all hover:bg-orange-600 active:scale-95 sm:w-auto"
                                >
                                    Lanjutkan Ujian
                                    <ArrowRightIcon class="ml-2 h-5 w-5" />
                                </Link>
                                <p class="mt-4 text-xs text-gray-500">Sesi pengerjaan Anda masih aktif.</p>
                            </div>
                        </div>

                        <div v-else-if="statusPengerjaan === 'SUDAH_SELESAI'" class="w-full">
                            <div class="flex flex-col items-center rounded-2xl border border-green-100 bg-green-50/50 p-10 text-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                                    <CheckCircle2Icon class="h-10 w-10" />
                                </div>
                                <h3 class="text-xl font-bold text-green-900">Quiz Telah Diselesaikan</h3>
                                <p class="mt-2 text-sm text-green-700/70 italic">Data jawaban Anda sudah kami simpan di sistem.</p>

                                <div class="my-8 h-[1px] w-full bg-green-100"></div>

                                <span class="text-xs font-bold tracking-widest text-green-500 uppercase">Skor Akhir</span>
                                <div class="text-5xl font-black text-green-600">
                                    {{ pengerjaan.score_result }}
                                </div>

                                <Link href="/siswa/dashboard" class="mt-10 text-sm font-bold text-gray-400 transition-colors hover:text-gray-600">
                                    &larr; Kembali ke Dashboard
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Opsional: Mencegah angka timer bergeser saat berubah (tabular nums) */
.font-mono {
    font-variant-numeric: tabular-nums;
}
</style>
