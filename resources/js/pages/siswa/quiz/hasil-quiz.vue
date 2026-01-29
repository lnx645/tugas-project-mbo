<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({
    history: Object, // Data JSON tunggal yang Anda kirim
});

// Hitung total benar untuk ringkasan header
const totalBenar = computed(() => {
    return props.history?.jawaban_tersimpan.filter((j: any) => j.is_correct).length;
});

const totalSoal = computed(() => props.history?.jawaban_tersimpan.length);
</script>

<template>
    <div class="mx-auto max-w-5xl p-4 font-sans text-slate-800">
        <div class="mb-6 border border-slate-300 bg-white">
            <div class="flex items-center justify-between border-b border-slate-300 bg-slate-50 p-3">
                <h2 class="text-sm font-bold tracking-wide text-slate-600 uppercase">Hasil Ujian Detail</h2>
                <div class="rounded bg-blue-600 px-3 py-1 text-[11px] font-bold text-white">SKOR: {{ Math.round(history?.score_result) }}</div>
            </div>
            <div class="grid grid-cols-2 gap-4 p-4 text-sm md:grid-cols-4">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Nama Quiz</p>
                    <p class="font-bold text-blue-600">{{ history?.jadwal_quiz.judul }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Status Lulus</p>
                    <p :class="['font-bold', history?.score_result >= history?.jadwal_quiz.kkm ? 'text-green-600' : 'text-red-600']">
                        {{ history?.score_result >= history?.jadwal_quiz.kkm ? 'LULUS (KKM ' + history?.jadwal_quiz.kkm + ')' : 'TIDAK LULUS' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Benar / Total</p>
                    <p class="font-bold">{{ totalBenar }} / {{ totalSoal }} Soal</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Selesai Pada</p>
                    <p class="font-bold">{{ new Date(history?.end_date).toLocaleString('id-ID') }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div
                v-for="(item, index) in history?.jawaban_tersimpan"
                :key="item.id"
                class="overflow-hidden rounded border border-slate-300 bg-white shadow-sm"
            >
                <div
                    :class="[
                        'flex items-center justify-between p-2 px-4 text-xs font-bold',
                        item.is_correct ? 'border-b border-green-200 bg-green-50 text-green-700' : 'border-b border-red-200 bg-red-50 text-red-700',
                    ]"
                >
                    <span>SOAL NO. {{ (index as any) + 1 }}</span>
                    <span>{{ item.is_correct ? '✔ BENAR' : '✘ SALAH' }}</span>
                </div>

                <div class="p-4">
                    <div class="mb-4 text-sm leading-relaxed">
                        <p class="mb-1 text-[11px] font-medium text-slate-500 uppercase">Pertanyaan:</p>
                        <p class="font-semibold text-slate-800">{{ item.soal.pertanyaan }}</p>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 rounded border border-slate-200 bg-slate-50 p-3 md:grid-cols-2">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Jawaban Anda:</p>
                            <p :class="['text-sm font-bold', item.is_correct ? 'text-green-600' : 'text-red-600']">
                                {{ item.jawaban ? item.jawaban.teks_jawaban : item.jawaban_texts || 'Tidak Dijawab' }}
                            </p>
                        </div>

                        <div v-if="!item.is_correct">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Status:</p>
                            <p class="text-sm text-slate-500 italic">Perlu evaluasi kembali pada materi terkait.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 mb-10">
            <button
                @click="router.visit('/')"
                class="rounded bg-slate-700 px-6 py-2 text-xs font-bold text-white uppercase transition hover:bg-slate-800"
            >
                Kembali ke Daftar
            </button>
        </div>
    </div>
</template>
