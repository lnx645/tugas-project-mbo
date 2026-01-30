<script setup lang="ts">
import { kerjakanQuiz, siwaQuizStart } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { AlertCircleIcon, ArrowRightIcon, BookOpenIcon, CheckCircle2Icon, ClockIcon, PlayIcon } from 'lucide-vue-next';
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
    <div class="px-4 py-12 font-serif text-[#333]">
        <div class="mx-auto">
            <div class="overflow-hidden border-2 border-[#003366] bg-white shadow-[8px_8px_0px_rgba(0,0,0,0.1)]">
                <div class="flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] p-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="rounded-sm bg-white p-1 text-xs font-black text-[#003366] italic">QUIZ</div>
                        <h2 class="text-xs font-bold tracking-widest uppercase">Konfirmasi Data Quiz</h2>
                    </div>
                    <span class="font-mono text-[10px] uppercase italic opacity-70">v.2026.1.1</span>
                </div>

                <div class="p-8">
                    <div class="mb-8 border-b-2 border-dashed border-gray-300 pb-6 text-center md:text-left">
                        <span class="text-[10px] font-bold tracking-widest text-blue-600 uppercase">Mata Uji Yang Dipilih:</span>
                        <h1 class="mt-1 text-3xl leading-tight font-black text-gray-900 uppercase">{{ quiz.judul }}</h1>
                    </div>

                    <div class="mb-8 grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 border border-gray-300 bg-[#f8f9fa] p-3">
                            <ClockIcon class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-[9px] font-bold text-gray-500 uppercase">Alokasi Waktu</p>
                                <p class="text-sm font-bold">{{ quiz.durasi }} Menit</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 border border-gray-300 bg-[#f8f9fa] p-3">
                            <BookOpenIcon class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-[9px] font-bold text-gray-500 uppercase">Jumlah Soal</p>
                                <p class="text-sm font-bold">{{ quiz.total_soal }} Butir</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 border border-yellow-400 bg-yellow-50 p-4">
                        <div class="flex gap-3">
                            <AlertCircleIcon class="h-5 w-5 shrink-0 text-yellow-600" />
                            <div>
                                <h3 class="text-xs font-bold text-yellow-900 uppercase underline">Petunjuk Pengerjaan:</h3>
                                <ul class="mt-2 list-inside list-disc space-y-1 text-[11px] font-medium text-yellow-800">
                                    <li>Klik tombol <strong>MULAI</strong> untuk memulai ujian.</li>
                                    <li>Waktu akan dihitung mundur secara otomatis oleh sistem.</li>
                                    <li>Pastikan indikator koneksi di pojok kanan atas berwarna hijau.</li>
                                    <li>
                                        Batas Nilai Minimal (KKM): <strong>{{ quiz.kkm }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center border-t-2 border-gray-100 pt-8">
                        <div v-if="statusPengerjaan === 'BELUM_MULAI'" class="w-full text-center">
                            <Link
                                method="post"
                                :href="siwaQuizStart({ id: quiz.id }).url"
                                class="inline-flex w-full items-center justify-center border-b-4 border-black bg-[#006633] px-10 py-4 text-sm font-bold tracking-widest text-white uppercase shadow-lg transition-all hover:bg-black active:translate-y-[2px] active:border-b-0 sm:w-auto"
                            >
                                <PlayIcon class="mr-2 h-4 w-4 fill-current" />
                                Mulai Ujian Sekarang
                            </Link>
                            <p class="mt-4 text-center font-mono text-[9px] font-bold tracking-widest text-gray-400 uppercase italic">
                                *** Tombol aktif saat jadwal dimulai ***
                            </p>
                        </div>

                        <div v-else-if="statusPengerjaan === 'SEDANG_BERJALAN'" class="w-full">
                            <div class="relative mb-8 overflow-hidden border-2 border-orange-400 bg-orange-50 p-6 text-center">
                                <div class="absolute top-0 left-0 bg-orange-400 px-2 py-0.5 text-[8px] font-bold text-white uppercase">
                                    Sisa Waktu
                                </div>
                                <div class="py-2 font-mono text-5xl font-black tracking-tighter text-orange-700">
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
                            <div class="border-2 border-green-600 bg-green-50 p-8">
                                <CheckCircle2Icon class="mx-auto mb-4 h-12 w-12 text-green-600" />
                                <h3 class="text-lg font-black text-green-900 uppercase">Ujian Telah Selesai</h3>
                                <p class="mt-1 text-xs font-bold tracking-tighter text-green-700 uppercase italic">
                                    Hasil pengerjaan sudah terkirim ke server pusat
                                </p>

                                <div class="my-6 border-t border-green-200"></div>

                                <span class="text-[9px] font-bold tracking-widest text-green-500 uppercase">Perolehan Skor</span>
                                <div class="mt-1 font-mono text-6xl font-black text-green-700">
                                    {{ Math.round(pengerjaan.score_result) }}
                                </div>

                                <Link
                                    href="/"
                                    class="mt-8 inline-block border-b border-gray-300 text-[10px] font-bold text-gray-400 uppercase hover:text-blue-600"
                                >
                                    &larr; Kembali ke halaman utama
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between border-t border-gray-300 bg-gray-100 p-2 px-4 font-mono text-[9px] text-gray-400 uppercase">
                    <span>Server: OK | Client: OK</span>
                    <span>LOG_TIME: {{ new Date().toLocaleTimeString() }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Reset font untuk look jadul */
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
    font-variant-numeric: tabular-nums;
}

/* Hilangkan rounded secara paksa */
.rounded-xl,
.rounded-lg,
.rounded-2xl {
    border-radius: 0px !important;
}
</style>
