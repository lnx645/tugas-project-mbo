<script setup lang="ts">
//@ts-expect-error
//@ts-nocheck
import { bankSoalGuru, deleteSoal, editBankSoal, tambahSoalBaru } from '@/routes';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronLeft, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

const daftarSoal = computed(() => {
    return usePage().props.soals as any;
});

function deleteItem(id: any) {
    if (confirm('Apakah Anda Yakin ingin menghapus soal ini?')) {
        router.delete(deleteSoal({ soal_id: id }).url);
    }
}

// Helper untuk warna indikator tipe soal ala ANBK
const getTipeClass = (tipe: string) => {
    if (tipe === 'Essay' || tipe === 'isian_singkat') return 'bg-purple-700';
    if (tipe === 'pilihan_ganda') return 'bg-[#003366]';
    return 'bg-emerald-700';
};
</script>

<template>
    <div class="py-4 font-serif text-[#333]">
        <div class="flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-2 text-white shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-sm bg-white p-1">
                    <div class="flex h-8 w-8 items-center justify-center bg-[#003366] text-xs text-[10px] font-bold text-white italic">UBK</div>
                </div>
                <div>
                    <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Daftar Bank Soal</h1>
                    <span class="font-mono text-[10px] text-[#ffcc00] uppercase italic"
                        >Kategori: {{ $page.props.category_nama || 'Pemrograman Web Dasar' }}</span
                    >
                </div>
            </div>
            <div class="text-right font-mono text-xs italic">Total: {{ daftarSoal.length }} Soal</div>
        </div>

        <div class="p-6">
            <div
                class="mb-4 flex flex-col justify-between gap-4 border border-gray-400 bg-[#f0f0f0] p-4 shadow-[2px_2px_0px_rgba(0,0,0,0.1)] md:flex-row md:items-center"
            >
                <div class="flex items-center gap-4">
                    <Link
                        :href="bankSoalGuru().url"
                        class="flex items-center gap-1 border border-gray-400 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 uppercase shadow-[1px_1px_0px_#000] transition-all hover:bg-gray-100"
                    >
                        <ChevronLeft :size="14" /> Kembali
                    </Link>
                    <div class="hidden h-8 w-[1px] bg-gray-300 md:block"></div>
                    <h3 class="text-xs font-bold tracking-tighter text-[#003366] uppercase">List Pertanyaan Ujian</h3>
                </div>

                <Link
                    :href="tambahSoalBaru({ id: $page.props?.category_id as any }).url"
                    class="flex items-center gap-2 border-b-2 border-black bg-[#006633] px-5 py-2 text-xs font-bold tracking-wide text-white uppercase transition-all hover:bg-black"
                >
                    <Plus :size="16" /> Tambah Soal Baru
                </Link>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(soal, index) in daftarSoal"
                    :key="soal.id"
                    class="group relative overflow-hidden border border-gray-400 bg-white shadow-sm"
                >
                    <div :class="['absolute top-0 bottom-0 left-0 w-1.5', getTipeClass(soal.tipe)]"></div>

                    <div class="p-5 pl-7">
                        <div class="flex flex-col items-start justify-between gap-4 md:flex-row">
                            <div class="flex-1">
                                <div class="mb-2 flex items-center gap-3">
                                    <span class="bg-[#003366] px-2 py-0.5 font-mono text-[10px] font-bold text-white">SOAL #{{ index + 1 }}</span>
                                    <span class="border-b border-gray-300 text-[9px] font-black tracking-[0.2em] text-gray-500 uppercase">{{
                                        soal.tipe.replace('_', ' ')
                                    }}</span>
                                </div>

                                <div class="mb-4 font-sans text-sm leading-relaxed font-bold text-gray-800">
                                    <p v-html="soal.pertanyaan"></p>
                                </div>

                                <div class="grid grid-cols-1 gap-4 border-t border-dashed border-gray-200 pt-4 md:grid-cols-2">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">Kunci Jawaban / Referensi:</span>
                                        <span class="text-xs font-bold text-[#006633] italic">
                                            {{ soal.kunci_jawaban?.teks_jawaban || soal.jawaban_texts || '-' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">Bobot Poin:</span>
                                        <span class="font-mono text-xs font-bold text-[#003366]">{{ soal.bobot }} POINT</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 gap-2 md:flex-col">
                                <Link
                                    :href="editBankSoal({ soal_id: soal.id }).url"
                                    class="flex h-10 w-10 items-center justify-center border border-gray-400 bg-[#ffcc00] text-black shadow-[2px_2px_0px_#000] transition-all hover:bg-black hover:text-white active:translate-y-[1px] active:shadow-none"
                                >
                                    <Pencil :size="16" />
                                </Link>
                                <button
                                    @click="deleteItem(soal.id)"
                                    class="flex h-10 w-10 items-center justify-center border border-gray-400 bg-red-700 text-white shadow-[2px_2px_0px_#000] transition-all hover:bg-black active:translate-y-[1px] active:shadow-none"
                                >
                                    <Trash2 :size="16" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-200 bg-gray-50 px-5 py-1">
                        <span class="font-mono text-[9px] text-gray-400 italic">SYSTEM_UID: SOAL_REF_{{ soal.id }}</span>
                    </div>
                </div>

                <div v-if="daftarSoal.length === 0" class="border-2 border-dashed border-gray-400 bg-gray-100/50 p-20 text-center">
                    <p class="text-sm font-bold tracking-widest text-gray-400 uppercase italic">-- Belum ada pertanyaan dalam database ini --</p>
                </div>
            </div>

            <div class="mt-6 border border-gray-400 bg-[#fefefe] p-3 text-[10px] font-medium text-gray-500 italic">
                * Catatan: Soal yang sudah dibuat akan otomatis muncul pada paket pengerjaan siswa yang menggunakan kategori ini.
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Reset font untuk simulasi aplikasi jadul */
button,
input,
select,
th,
td {
    font-family: 'Arial', sans-serif;
}

/* Hilangkan rounded corners tailwind */
.rounded-2xl,
.rounded-xl,
.rounded-lg {
    border-radius: 0px !important;
}

/* Navigasi Link styling */
a {
    text-decoration: none;
}
</style>
