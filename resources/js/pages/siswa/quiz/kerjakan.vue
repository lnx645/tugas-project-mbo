<script setup lang="ts">
import { autoSaveJawaban, selesaikanUjian } from '@/routes';
import { useForm, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Timer, UserCircle } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, shallowRef } from 'vue'; // Tambah onMounted
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
});
const proteksiUjian = () => {
    document.addEventListener('contextmenu', (e) => e.preventDefault());

    document.addEventListener('keydown', (e) => {
        if (e.key === 'F12') e.preventDefault();

        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) e.preventDefault();
        if (e.ctrlKey && e.key === 'u') e.preventDefault();

        if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 's')) e.preventDefault();
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            alert('PERINGATAN: Jangan meninggalkan halaman ujian! Aktivitas ini dicatat oleh sistem.');
        }
    });

    document.addEventListener('dragstart', (e) => e.preventDefault());
};

onMounted(() => {
    proteksiUjian();
});
onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});
</script>

<template>
    <div class="flex h-screen flex-col bg-[#F4F7F9] font-sans text-[#333]">
        <header class="flex h-16 items-center justify-between bg-[#21409A] px-6 text-white shadow-lg">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white p-1">
                    <img :src="logo" class="h-8 w-8 object-contain" alt="Logo" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold tracking-widest text-blue-200 uppercase">Asesmen Kompetensi Siswa</span>
                    <span class="text-sm font-black tracking-tight uppercase">{{ soalsData.jadwal_quiz.judul }}</span>
                </div>
            </div>

            <div
                class="flex items-center gap-3 rounded-full border border-blue-400/30 bg-[#1A337A] px-6 py-2 shadow-inner transition-colors"
                :class="{ 'border-red-500 bg-red-900': timeLeftSeconds < 300 }"
            >
                <Timer class="h-5 w-5 text-yellow-400" :class="{ 'animate-pulse': timeLeftSeconds < 300 }" />
                <span class="font-mono text-2xl font-black tracking-widest text-white">{{ timeLeft }}</span>
            </div>

            <div class="flex items-center gap-4 border-l border-blue-400/30 pl-6">
                <div class="text-right">
                    <p class="text-[10px] font-medium text-blue-200 uppercase">Nama Peserta</p>
                    <p class="text-xs font-bold uppercase">Siswa Pasundan 2</p>
                </div>
                <UserCircle class="h-8 w-8 text-blue-200" />
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                <div v-if="currentSoal" class="mx-auto max-w-5xl overflow-hidden rounded-xl border-t-4 border-[#21409A] bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b bg-[#F8FAFC] px-8 py-4">
                        <div class="flex items-center gap-4">
                            <span class="rounded bg-[#21409A] px-4 py-1 text-sm font-black text-white italic">SOAL NO. {{ currentIndex + 1 }}</span>
                        </div>
                    </div>

                    <div class="min-h-[350px] p-10">
                        <div class="mb-10 text-[20px] leading-relaxed font-medium text-[#2D3748]">
                            {{ currentSoal.pertanyaan }}
                        </div>

                        <div class="space-y-4">
                            <template v-if="currentSoal.tipe === 'pilihan_ganda' || currentSoal.tipe === 'benar_salah'">
                                <div v-for="(jawaban, idx) in currentSoal.jawaban" :key="jawaban.id">
                                    <label
                                        :class="{
                                            'border-[#21409A] bg-[#EEF2FF] shadow-md ring-1 ring-[#21409A]':
                                                form.jawaban[currentSoal.id] == jawaban.id,
                                            'border-slate-200 bg-white hover:border-slate-300': form.jawaban[currentSoal.id] != jawaban.id,
                                        }"
                                        class="group relative flex cursor-pointer items-center gap-5 rounded-lg border-2 p-5 transition-all duration-150"
                                    >
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md border-2 border-slate-300 font-black transition-colors group-hover:bg-slate-50"
                                            :class="
                                                form.jawaban[currentSoal.id] == jawaban.id
                                                    ? 'border-[#21409A] bg-[#21409A] text-white'
                                                    : 'text-slate-500'
                                            "
                                        >
                                            {{ String.fromCharCode(65 + (idx as number)) }}
                                        </div>
                                        <input type="radio" :value="jawaban.id" v-model="form.jawaban[currentSoal.id]" class="hidden" />
                                        <div class="text-[17px] font-semibold text-[#4A5568]">{{ jawaban.teks_jawaban }}</div>
                                    </label>
                                </div>
                            </template>
                            <div v-else>
                                <textarea class="h-full w-full border p-3" v-model="form.jawaban[currentSoal.id]" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 items-center border-t bg-[#F1F5F9] p-6">
                        <button
                            @click="prevSoal"
                            :disabled="currentIndex === 0"
                            class="border-2 border-[#21409A] bg-white px-8 py-3 text-sm font-black text-[#21409A] disabled:opacity-20"
                        >
                            SOAL SEBELUMNYA
                        </button>
                        <div class="text-center">
                            <label
                                class="inline-flex cursor-pointer items-center gap-3 rounded-lg border-2 border-yellow-500 bg-yellow-400 px-10 py-3 shadow-md"
                            >
                                <input type="checkbox" class="h-5 w-5 accent-[#21409A]" />
                                <span class="text-xs font-black text-[#1A337A] uppercase">Ragu-Ragu</span>
                            </label>
                        </div>
                        <div class="text-right">
                            <button
                                v-if="currentIndex < listSoal.length - 1"
                                @click="nextSoal"
                                class="bg-[#21409A] px-10 py-3 text-sm font-black text-white shadow-lg"
                            >
                                SOAL BERIKUTNYA
                            </button>
                            <button v-else @click="submitUjian(false)" class="bg-emerald-600 px-10 py-3 text-sm font-black text-white shadow-lg">
                                SELESAI UJIAN
                            </button>
                        </div>
                    </div>
                </div>
            </main>

            <aside
                class="hidden w-80 overflow-y-auto border-l-2 border-slate-200 bg-white p-6 shadow-[inset_10px_0_20px_-15px_rgba(0,0,0,0.1)] lg:block"
            >
                <div class="mb-6 flex items-center gap-3 border-b-2 border-slate-100 pb-4">
                    <LayoutGrid class="h-5 w-5 text-[#21409A]" />
                    <h3 class="text-xs font-black tracking-widest text-[#21409A] uppercase italic">Daftar Soal</h3>
                </div>
                <div class="grid grid-cols-5 gap-2">
                    <button
                        v-for="(soal, index) in listSoal"
                        :key="soal.id"
                        @click="jumpToSoal(index)"
                        :class="[
                            'relative h-11 rounded-md border-2 text-[14px] font-black transition-all',
                            index === currentIndex
                                ? 'z-10 scale-105 border-[#21409A] bg-[#21409A] text-white shadow-lg'
                                : form.jawaban[soal.id]
                                  ? 'border-[#10B981] bg-[#D1FAE5] text-[#065F46]'
                                  : 'border-slate-200 bg-white text-slate-400',
                        ]"
                    >
                        {{ (index as number) + 1 }}
                    </button>
                </div>
            </aside>
        </div>
    </div>
</template>
