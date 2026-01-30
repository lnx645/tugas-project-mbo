<script setup lang="ts">
//@ts-expect-error
//@ts-nocheck
import { simpanBankSoal } from '@/routes';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ChevronLeft, Image as ImageIcon, Plus, Save, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const category_id = computed(() => usePage().props.category_id as any);

const form = useForm({
    quiz_category_id: category_id.value as any,
    pertanyaan: '',
    bobot: 1,
    tipe: 'pilihan_ganda',
    lampiran_gambar: null as File | null,
    pilihan: [
        { label: 'A', teks: '' },
        { label: 'B', teks: '' },
    ],
    jawaban_benar: '',
});

const imagePreview = ref<string | null>(null);

// LOGIK OTOMATIS UNTUK BENAR / SALAH
watch(
    () => form.tipe,
    (newType) => {
        if (newType === 'benar_salah') {
            form.pilihan = [
                { label: 'A', teks: 'Benar' },
                { label: 'B', teks: 'Salah' },
            ];
            form.jawaban_benar = '';
        } else if (newType === 'pilihan_ganda') {
            form.pilihan = [
                { label: 'A', teks: '' },
                { label: 'B', teks: '' },
            ];
        }
    },
);

const tambahOpsi = () => {
    const nextLabel = String.fromCharCode(65 + form.pilihan.length);
    if (form.pilihan.length < 5) {
        form.pilihan.push({ label: nextLabel, teks: '' });
    }
};

const hapusOpsi = (index: number) => {
    if (form.pilihan.length > 2) {
        form.pilihan.splice(index, 1);
        form.pilihan.forEach((opt, i) => {
            opt.label = String.fromCharCode(65 + i);
        });
        form.jawaban_benar = '';
    }
};

const handleFileUpload = (e: any) => {
    const file = e.target.files[0];
    if (file) {
        form.lampiran_gambar = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(
        simpanBankSoal({
            id: category_id.value,
        }).url,
        { forceFormData: true },
    );
};
</script>

<template>
    <Head title="Tambah Soal - Administrasi UBK" />

    <div class="min-h-screen bg-[#e1e4e8] font-serif text-[#333]">
        <div class="flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-2 text-white shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-sm bg-white p-1">
                    <div class="flex h-8 w-8 items-center justify-center bg-[#003366] text-[10px] font-bold text-white italic">UBK</div>
                </div>
                <div>
                    <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Entry Bank Soal Baru</h1>
                    <span class="font-mono text-[10px] tracking-tighter text-[#ffcc00] uppercase italic"
                        >Kategori: {{ $page.props.category_nama || 'Pemrograman Web Dasar' }}</span
                    >
                </div>
            </div>
            <button
                @click="(window as any).history.back()"
                class="flex items-center gap-1 text-xs font-bold tracking-tighter uppercase hover:text-[#ffcc00]"
            >
                <ChevronLeft :size="14" /> Kembali ke Daftar
            </button>
        </div>

        <div class="mx-auto max-w-7xl p-6">
            <form @submit.prevent="submit" class="grid grid-cols-12 gap-6">
                <div class="col-span-12 space-y-4 lg:col-span-8">
                    <div class="border border-gray-400 bg-white p-6 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                        <div class="mb-6">
                            <label class="mb-2 block text-xs font-bold text-[#003366] uppercase">Input Teks Pertanyaan</label>
                            <textarea
                                v-model="form.pertanyaan"
                                rows="6"
                                class="w-full rounded-none border border-gray-400 px-4 py-3 font-sans text-sm leading-relaxed outline-none focus:bg-yellow-50"
                                placeholder="Tuliskan pertanyaan ujian di sini..."
                            ></textarea>
                            <div
                                v-if="form.errors.pertanyaan"
                                class="mt-1 border-l-2 border-red-600 pl-2 text-[10px] font-bold text-red-600 uppercase italic"
                            >
                                [Peringatan: {{ form.errors.pertanyaan }}]
                            </div>
                        </div>

                        <div v-if="['pilihan_ganda', 'benar_salah'].includes(form.tipe)" class="space-y-4 border-t border-gray-200 pt-6">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="flex items-center gap-2 text-xs font-bold tracking-widest text-gray-500 uppercase">
                                    <span class="h-2 w-2 bg-gray-500"></span> Opsi Jawaban {{ form.tipe === 'benar_salah' ? '(B/S)' : '' }}
                                </h3>
                                <span class="text-[10px] font-bold text-red-600 uppercase italic">Klik Huruf Untuk Menentukan Kunci</span>
                            </div>

                            <div v-for="(opt, index) in form.pilihan" :key="index" class="group flex items-start gap-4">
                                <button
                                    type="button"
                                    @click="form.jawaban_benar = opt.label"
                                    :class="[
                                        'flex h-10 w-10 items-center justify-center border text-sm font-bold shadow-[1px_1px_0px_rgba(0,0,0,1)] transition-all',
                                        form.jawaban_benar === opt.label
                                            ? 'border-black bg-[#006633] text-white'
                                            : 'border-gray-400 bg-[#f0f0f0] text-gray-400 hover:bg-[#ffcc00] hover:text-black',
                                    ]"
                                >
                                    {{ opt.label }}
                                </button>

                                <div class="relative w-full">
                                    <input
                                        type="text"
                                        v-model="opt.teks"
                                        :disabled="form.tipe === 'benar_salah'"
                                        class="w-full rounded-none border border-gray-400 px-4 py-2 text-sm font-bold text-[#003366] italic outline-none focus:bg-yellow-50 disabled:bg-gray-100 disabled:text-gray-500"
                                        :placeholder="'Ketik Jawaban ' + opt.label"
                                    />

                                    <button
                                        v-if="form.pilihan.length > 2 && form.tipe !== 'benar_salah'"
                                        @click="hapusOpsi(index)"
                                        type="button"
                                        class="absolute top-2 right-2 text-red-700 opacity-0 transition group-hover:opacity-100"
                                    >
                                        <Trash2 :size="14" />
                                    </button>
                                </div>
                            </div>

                            <button
                                v-if="form.pilihan.length < 5 && form.tipe === 'pilihan_ganda'"
                                @click="tambahOpsi"
                                type="button"
                                class="flex w-full items-center justify-center gap-2 border border-dashed border-gray-400 bg-gray-50 py-2 text-[10px] font-bold tracking-tighter text-gray-500 uppercase transition hover:bg-[#e1e4e8]"
                            >
                                <Plus :size="14" /> Tambah Pilihan Jawaban
                            </button>
                        </div>

                        <div
                            v-else
                            class="border border-gray-300 bg-gray-50 p-10 text-center text-xs font-bold tracking-widest text-gray-400 uppercase italic"
                        >
                            Tipe {{ form.tipe.replace('_', ' ') }} Tidak Membutuhkan Opsi Pilihan
                        </div>
                    </div>
                </div>

                <div class="col-span-12 space-y-4 lg:col-span-4">
                    <div class="border border-gray-400 bg-white p-5 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                        <div class="mb-4">
                            <label class="mb-1 block text-[10px] font-bold text-gray-500 uppercase">Pilih Tipe Soal</label>
                            <select
                                v-model="form.tipe"
                                class="w-full cursor-pointer border border-gray-400 bg-[#f0f0f0] p-2 text-xs font-bold uppercase outline-none focus:bg-yellow-50"
                            >
                                <option value="pilihan_ganda">PILIHAN GANDA</option>
                                <option value="benar_salah">BENAR / SALAH</option>
                                <option value="isian_singkat">ISIAN SINGKAT</option>
                                <option value="essay">ESSAY / URAIAN</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="mb-1 block text-[10px] font-bold text-gray-500 uppercase">Bobot Soal (Point)</label>
                            <input
                                v-model="form.bobot"
                                type="number"
                                min="1"
                                class="w-full border border-gray-400 bg-[#f0f0f0] px-3 py-2 text-sm font-black text-[#006633] outline-none"
                            />
                        </div>

                        <div class="relative mt-6 overflow-hidden border-2 border-[#003366] bg-[#f0f0f0] p-4 text-center shadow-inner">
                            <span class="absolute top-1 left-1 font-sans text-[9px] font-bold text-[#003366] uppercase italic">Kunci Aktif:</span>
                            <div class="py-2 font-mono text-5xl leading-none font-black text-[#003366]">
                                {{ form.jawaban_benar || '?' }}
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-400 bg-white p-5 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                        <label class="mb-2 block flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase">
                            <ImageIcon :size="12" /> Media Lampiran (Opsional)
                        </label>

                        <div
                            v-if="!imagePreview"
                            class="group relative cursor-pointer border border-dashed border-gray-400 bg-gray-50 p-6 text-center transition hover:bg-gray-100"
                        >
                            <input type="file" @change="handleFileUpload" class="absolute inset-0 cursor-pointer opacity-0" />
                            <p class="text-[10px] font-bold tracking-tighter text-gray-400 uppercase italic group-hover:text-gray-600">
                                Klik Untuk Import Gambar
                            </p>
                        </div>

                        <div v-else class="group relative border border-gray-400 bg-gray-100 p-1 shadow-inner">
                            <img :src="imagePreview" class="mx-auto h-auto max-h-40 w-full object-contain" />
                            <button
                                @click="
                                    imagePreview = null;
                                    form.lampiran_gambar = null;
                                "
                                type="button"
                                class="absolute top-1 right-1 border border-black bg-red-700 px-2 py-0.5 text-[10px] font-bold text-white shadow-[1px_1px_0px_#000]"
                            >
                                RESET
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex w-full items-center justify-center gap-2 border-b-4 border-black bg-[#006633] py-4 text-xs font-bold tracking-[0.2em] text-white uppercase shadow-lg transition hover:bg-black active:translate-y-[2px] active:border-b-0"
                    >
                        <Save :size="16" /> {{ form.processing ? 'Proses Simpan...' : 'Simpan Ke Database' }}
                    </button>
                </div>
            </form>
        </div>

        <div
            class="fixed right-0 bottom-0 left-0 flex justify-between border-t border-[#ffcc00] bg-[#003366] px-4 py-1 font-mono text-[9px] text-white uppercase opacity-80"
        >
            <span>System Log: Ready for Entry</span>
            <span>Server Time: {{ new Date().toLocaleTimeString() }}</span>
        </div>
    </div>
</template>

<style scoped>
/* Reset font untuk simulasi aplikasi jadul */
button,
input,
select,
textarea {
    font-family: 'Arial', sans-serif;
    border-radius: 0px !important;
}

select {
    appearance: auto !important;
}

/* Transisi halus tapi tetap boxy */
button:active {
    box-shadow: none !important;
}
</style>
