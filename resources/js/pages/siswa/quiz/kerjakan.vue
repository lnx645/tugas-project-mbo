<script setup lang="ts">
// (Logic Anda tetap sama, tidak ada yang diubah)
import { autoSaveJawaban, selesaikanUjian } from '@/routes';
import { useForm, usePage } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, LayoutGrid, Timer, UserCircle } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, shallowRef } from 'vue';
import logo from '../../../../img/logo.png';

const soalsData = computed(() => usePage().props.soals as any);
const listSoal = computed(() => soalsData.value.jadwal_quiz.bank_soal_group.bank_soals);
let loading = shallowRef(false);
const jawabanTersimpan = computed(() => soalsData.value.jawaban_tersimpan as any[]);

const form = useForm({
    history_id: soalsData.value.id,
    remaining_time: soalsData.value.remaining_time,
    jawaban: listSoal.value.reduce((acc: any, soal: any) => {
        const sudahDijawab = jawabanTersimpan.value.find((j) => j.quiz_bank_soal_id === soal.id);
        if (sudahDijawab) {
            acc[soal.id] = sudahDijawab.quiz_jawaban_soal_id ?? sudahDijawab.jawaban_texts ?? '';
        } else {
            acc[soal.id] = '';
        }
        return acc;
    }, {}),
});

const currentIndex = ref(0);
const currentSoal = computed(() => listSoal.value[currentIndex.value]);

function simpanJawaban() {
    form.remaining_time = Math.ceil(timeLeftSeconds.value / 60);
    form.post(
        autoSaveJawaban({
            id: soalsData.value.jadwal_quiz_id,
            history_id: soalsData.value.id,
        }).url,
        { preserveScroll: true },
    );
}

function jumpToSoal(index: any) {
    simpanJawaban();
    currentIndex.value = index;
}

function nextSoal() {
    simpanJawaban();
    if (currentIndex.value < listSoal.value.length - 1) currentIndex.value++;
}

function prevSoal() {
    simpanJawaban();
    if (currentIndex.value > 0) currentIndex.value--;
}

function submitUjian(isAuto = false) {
    if (isAuto || confirm('YAKIN AKAN MENGAKHIRI UJIAN? Periksa kembali jawaban Anda.')) {
        form.submit(
            selesaikanUjian({
                id: soalsData.value.jadwal_quiz_id,
                history_id: soalsData.value.id,
            }),
        );
    }
}

const timeLeftSeconds = ref(0);
let timerInterval: any = null;

const formatTimeDisplay = (seconds: number) => {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    return [h, m, s].map((v) => (v < 10 ? '0' + v : v)).join(':');
};

const timeLeft = computed(() => formatTimeDisplay(timeLeftSeconds.value));

onMounted(() => {
    const initialMinutes = soalsData.value.remaining_time ?? soalsData.value.jadwal_quiz.durasi;
    timeLeftSeconds.value = initialMinutes * 60;
    timerInterval = setInterval(() => {
        if (timeLeftSeconds.value > 0) {
            timeLeftSeconds.value--;
        } else {
            clearInterval(timerInterval);
            submitUjian(true);
        }
    }, 1000);
    proteksiUjian();
});

const proteksiUjian = () => {
    document.addEventListener('contextmenu', (e) => e.preventDefault());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F12') e.preventDefault();
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) e.preventDefault();
        if (e.ctrlKey && e.key === 'u') e.preventDefault();
        if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 's')) e.preventDefault();
    });
};

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});
</script>

<template>
    <div class="flex h-screen flex-col bg-[#e1e4e8] font-serif text-[#333]">
        <header class="flex h-14 items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] px-4 text-white shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-sm bg-white p-1">
                    <img :src="logo" class="h-6 w-6 object-contain" alt="Logo" />
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-[10px] font-bold tracking-tighter text-[#ffcc00] uppercase italic">CBT - SMK PASUNDAN 2 BDG</span>
                    <span class="text-xs font-black tracking-wider uppercase">{{ soalsData.jadwal_quiz.judul }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3 border border-blue-400/30 bg-[#002244] px-4 py-1.5">
                <Timer class="h-4 w-4 text-yellow-400" />
                <span class="font-mono text-xl font-black tracking-[0.2em] text-white tabular-nums">{{ timeLeft }}</span>
            </div>

            <div class="flex items-center gap-3 font-mono text-[10px] uppercase italic opacity-80">
                <span>Peserta: {{ $page.props.auth.user.nama || 'SISWA' }}</span>
                <UserCircle class="h-5 w-5" />
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                <div v-if="currentSoal" class="mx-auto max-w-4xl border border-gray-400 bg-white shadow-[4px_4px_0px_rgba(0,0,0,0.1)]">
                    <div class="flex items-center justify-between border-b border-gray-300 bg-[#f4f4f4] px-6 py-3">
                        <span class="bg-[#003366] px-4 py-1 font-mono text-xs font-bold tracking-widest text-white uppercase italic">
                            SOAL NOMOR: {{ currentIndex + 1 }}
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Ukuran Font:</span>
                            <div class="flex gap-1">
                                <button class="h-6 w-6 border border-gray-400 text-[10px] font-bold">A</button>
                                <button class="h-6 w-6 border border-gray-400 bg-white text-xs font-bold">A</button>
                                <button class="h-6 w-6 border border-gray-400 text-sm font-bold">A</button>
                            </div>
                        </div>
                    </div>

                    <div class="min-h-[300px] p-8 lg:p-12">
                        <div class="mb-10 font-sans text-[18px] leading-relaxed text-gray-800">
                            <p v-html="currentSoal.pertanyaan"></p>
                        </div>

                        <div class="space-y-3 font-sans">
                            <template v-if="currentSoal.tipe === 'pilihan_ganda' || currentSoal.tipe === 'benar_salah'">
                                <div v-for="(jawaban, idx) in currentSoal.jawaban" :key="jawaban.id">
                                    <label
                                        :class="[
                                            'group flex cursor-pointer items-center gap-4 border p-3 transition-colors duration-75',
                                            form.jawaban[currentSoal.id] == jawaban.id
                                                ? 'border-blue-600 bg-blue-50'
                                                : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-yellow-50',
                                        ]"
                                    >
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center border border-gray-400 font-black"
                                            :class="
                                                form.jawaban[currentSoal.id] == jawaban.id
                                                    ? 'border-black bg-[#003366] text-white'
                                                    : 'bg-white text-gray-500'
                                            "
                                        >
                                            {{ String.fromCharCode(65 + (idx as number)) }}
                                        </div>
                                        <input type="radio" :value="jawaban.id" v-model="form.jawaban[currentSoal.id]" class="hidden" />
                                        <div class="text-[15px] leading-snug font-bold text-gray-700 uppercase">{{ jawaban.teks_jawaban }}</div>
                                    </label>
                                </div>
                            </template>

                            <div v-else class="border border-gray-400 bg-[#f9f9f9] p-2">
                                <span class="mb-2 block text-[10px] font-bold text-gray-400 uppercase italic">Lembar Jawaban Isian:</span>
                                <textarea
                                    class="min-h-[150px] w-full border border-gray-300 p-4 font-sans text-sm outline-none focus:bg-white"
                                    v-model="form.jawaban[currentSoal.id]"
                                    placeholder="Ketik jawaban anda di sini..."
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 items-center border-t border-gray-300 bg-[#e9ecef] p-4">
                        <button
                            @click="prevSoal"
                            :disabled="currentIndex === 0"
                            class="flex items-center justify-center gap-2 border border-black bg-[#333] px-6 py-2.5 text-[10px] font-bold text-white uppercase shadow-[2px_2px_0px_rgba(0,0,0,0.5)] active:translate-y-[1px] active:shadow-none disabled:opacity-20"
                        >
                            <ChevronLeft :size="14" /> SOAL SEBELUMNYA
                        </button>

                        <div class="text-center">
                            <label
                                class="inline-flex cursor-pointer items-center gap-2 border border-black bg-[#ffcc00] px-6 py-2.5 shadow-[2px_2px_0px_rgba(0,0,0,0.5)] active:translate-y-[1px] active:shadow-none"
                            >
                                <input type="checkbox" class="h-4 w-4 accent-[#003366]" />
                                <span class="text-[10px] font-black text-[#003366] uppercase">Ragu-Ragu</span>
                            </label>
                        </div>

                        <div class="text-right">
                            <button
                                v-if="currentIndex < listSoal.length - 1"
                                @click="nextSoal"
                                class="ml-auto flex items-center justify-center gap-2 border border-black bg-[#003366] px-6 py-2.5 text-[10px] font-bold text-white uppercase shadow-[2px_2px_0px_rgba(0,0,0,0.5)] active:translate-y-[1px] active:shadow-none"
                            >
                                SOAL BERIKUTNYA <ChevronRight :size="14" />
                            </button>
                            <button
                                v-else
                                @click="submitUjian(false)"
                                class="ml-auto border border-black bg-[#006633] px-6 py-2.5 text-[10px] font-bold text-white uppercase shadow-[2px_2px_0px_rgba(0,0,0,0.5)]"
                            >
                                SELESAI UJIAN
                            </button>
                        </div>
                    </div>
                </div>
            </main>

            <aside class="hidden w-72 flex-col border-l border-gray-400 bg-[#f4f4f4] lg:flex">
                <div class="flex items-center gap-2 bg-[#003366] p-3 text-white uppercase">
                    <LayoutGrid :size="16" />
                    <h3 class="font-mono text-[10px] leading-none font-bold tracking-widest">Navigasi Soal</h3>
                </div>

                <div class="flex-1 overflow-y-auto p-4">
                    <div class="grid grid-cols-5 gap-2">
                        <button
                            v-for="(soal, index) in listSoal"
                            :key="soal.id"
                            @click="jumpToSoal(index)"
                            :class="[
                                'relative h-10 border text-[12px] font-black shadow-[1px_1px_0px_rgba(0,0,0,0.1)] transition-all',
                                index === currentIndex
                                    ? 'z-10 border-black bg-[#ffcc00] text-black ring-2 ring-black'
                                    : form.jawaban[soal.id]
                                      ? 'border-gray-400 bg-black text-white'
                                      : 'border-gray-400 bg-white text-gray-400',
                            ]"
                        >
                            {{ (index as number) + 1 }}
                            <div v-if="false" class="absolute -top-1 -right-1 h-2 w-2 rounded-full border border-black bg-yellow-400"></div>
                        </button>
                    </div>
                </div>

                <div class="border-t border-gray-400 bg-gray-200 p-4">
                    <div class="text-[9px] font-bold text-gray-500 uppercase italic">
                        Keterangan:
                        <div class="mt-2 flex items-center gap-2">
                            <div class="h-3 w-3 bg-black"></div>
                            Dijawab
                        </div>
                        <div class="mt-1 flex items-center gap-2">
                            <div class="h-3 w-3 border border-gray-400 bg-white"></div>
                            Belum Dijawab
                        </div>
                        <div class="mt-1 flex items-center gap-2">
                            <div class="h-3 w-3 bg-[#ffcc00]"></div>
                            Sedang Dibuka
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</template>

<style scoped>
/* Reset Font untuk simulasi UBK/ANBK */
button,
label,
h1,
h3,
p {
    font-family: 'Arial', sans-serif;
}

.font-mono {
    font-family: 'Courier New', Courier, monospace;
}

/* Hilangkan rounded corner paksa */
* {
    border-radius: 0px !important;
}

/* Khas ANBK: Nomor soal yang sudah dijawab berubah warna gelap */
</style>
