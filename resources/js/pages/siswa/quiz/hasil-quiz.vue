<script setup lang="ts">
//@ts-nocheck
//@ts-expect-error
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    history: Object as any, // Data JSON tunggal
});

// Hitung total benar untuk ringkasan header
const totalBenar = computed(() => {
    return props.history?.jawaban_tersimpan.filter((j: any) => j.is_correct).length;
});

const totalSoal = computed(() => props.history?.jawaban_tersimpan.length);
</script>

<template>
    <div class="p-4 font-serif text-[#333]">
        <div class="mx-auto mb-6 max-w-5xl border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-3 text-white shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="rounded-sm bg-white p-1 text-xl font-black text-[#003366]">R</div>
                    <div>
                        <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Laporan Hasil Ujian Digital</h1>
                        <p class="mt-1 font-mono text-[10px] text-[#ffcc00] uppercase">Sertifikat Hasil Ujian Nasional Berbasis Komputer</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-white px-3 py-1 text-lg font-black text-[#003366] shadow-[2px_2px_0px_#000]">
                        SKOR: {{ Math.round(history?.score_result) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-5xl">
            <div class="mb-6 border border-gray-400 bg-[#f0f0f0] p-4 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                <div class="grid grid-cols-2 gap-y-4 text-xs md:grid-cols-4">
                    <div class="border-l-2 border-gray-400 pl-3">
                        <p class="font-mono text-[10px] font-bold text-gray-500 uppercase">Judul Mata Uji</p>
                        <p class="font-bold text-[#003366] uppercase">{{ history?.jadwal_quiz.judul }}</p>
                    </div>
                    <div class="border-l-2 border-gray-400 pl-3">
                        <p class="font-mono text-[10px] font-bold text-gray-500 uppercase">Kualifikasi</p>
                        <p
                            :class="[
                                'font-bold uppercase',
                                history?.score_result >= (history?.jadwal_quiz.kkm || 75) ? 'text-green-700' : 'text-red-700',
                            ]"
                        >
                            {{ history?.score_result >= (history?.jadwal_quiz.kkm || 75) ? 'KOMPETEN' : 'BELUM KOMPETEN' }}
                        </p>
                    </div>
                    <div class="border-l-2 border-gray-400 pl-3">
                        <p class="font-mono text-[10px] font-bold text-gray-500 uppercase">Capaian Jawaban</p>
                        <p class="font-mono font-bold">{{ totalBenar }} Benar / {{ totalSoal }} Soal</p>
                    </div>
                    <div class="border-l-2 border-gray-400 pl-3">
                        <p class="font-mono text-[10px] font-bold text-gray-500 uppercase">Waktu Selesai</p>
                        <p class="font-bold uppercase">
                            {{ new Date(history?.end_date).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' }) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div v-for="(item, index) in history?.jawaban_tersimpan" :key="item.id" class="border border-gray-400 bg-white shadow-sm">
                    <div
                        :class="[
                            'flex items-center justify-between border-b p-2 px-4 font-mono text-[11px] font-bold uppercase',
                            item.is_correct ? 'border-gray-400 bg-[#e8f5e9] text-green-800' : 'border-gray-400 bg-[#ffebee] text-red-800',
                        ]"
                    >
                        <span>NOMOR URUT: {{ (index as any) + 1 }}</span>
                        <span class="border border-current px-2 py-0.5">{{ item.is_correct ? 'BENAR' : 'SALAH' }}</span>
                    </div>

                    <div class="p-5">
                        <div class="mb-4">
                            <p class="mb-1 font-mono text-[9px] font-bold tracking-tighter text-gray-400 uppercase">Butir Pertanyaan:</p>
                            <p class="font-sans text-sm leading-relaxed font-bold text-gray-800">{{ item.soal.pertanyaan }}</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 border-t border-dashed border-gray-300 pt-4 md:grid-cols-2">
                            <div class="border border-gray-200 bg-gray-50 p-2">
                                <p class="font-mono text-[9px] font-bold text-gray-400 uppercase italic">Input Jawaban Anda:</p>
                                <p :class="['font-sans text-sm font-black uppercase', item.is_correct ? 'text-green-700' : 'text-red-700']">
                                    {{ item.jawaban ? item.jawaban.teks_jawaban : item.jawaban_texts || '-- TIDAK MENJAWAB --' }}
                                </p>
                            </div>

                            <div v-if="!item.is_correct" class="border border-[#fbc02d] bg-[#fffde7] p-2">
                                <p class="font-mono text-[9px] font-bold text-[#fbc02d] uppercase italic">Analisis Kesalahan:</p>
                                <p class="font-sans text-[11px] font-medium text-gray-600">
                                    Jawaban tidak sesuai dengan kunci sistem. Pelajari kembali bab terkait.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 mb-12 flex items-center justify-between border-t-2 border-gray-400 pt-6">
                <button
                    @click="router.visit('/')"
                    class="border border-black bg-[#333] px-8 py-2 text-xs font-bold text-white uppercase shadow-[3px_3px_0px_#888] transition active:translate-y-[2px] active:shadow-none"
                >
                    Kembali ke Beranda
                </button>

                <button
                    @click="window.print()"
                    class="border border-black bg-[#006633] px-8 py-2 text-xs font-bold text-white uppercase shadow-[3px_3px_0px_#888] transition active:translate-y-[2px] active:shadow-none"
                >
                    Cetak Hasil
                </button>
            </div>

            <div class="mb-10 text-center font-mono text-[10px] text-gray-400 uppercase">
                *** Dokumen ini dihasilkan secara otomatis oleh sistem UBK pada {{ new Date().toLocaleString() }} ***
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Reset Font untuk look jadul */
button,
p,
div {
    font-family: 'Arial', 'Helvetica', sans-serif;
}

.font-mono {
    font-family: 'Courier New', Courier, monospace;
}

/* Hilangkan rounded corners secara paksa */
.rounded,
.rounded-sm {
    border-radius: 0px !important;
}

@media print {
    button {
        display: none;
    }
}
</style>
