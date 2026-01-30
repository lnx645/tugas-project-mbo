<script setup lang="ts">
//@ts-expect-error
//@ts-nocheck
import { simpanEditBankSoal } from '@/routes';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ChevronLeft, Image as ImageIcon, Save } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = computed(() => usePage().props as any);
const soal = computed(() => usePage().props.soal as any);
const category_id = computed(() => usePage().props.category_id as any);

const form = useForm({
    _method: 'PUT',
    quiz_category_id: props.value.soal?.quiz_category_id || category_id.value,
    pertanyaan: props.value.soal?.pertanyaan || '',
    tipe: props.value.soal?.tipe || 'pilihan_ganda',
    lampiran_gambar: null as File | null,
    bobot: props.value.soal?.bobot || 1,
    pilihan:
        props.value.soal?.jawaban?.length > 0
            ? props.value.soal.jawaban.map((j: any, index: number) => ({
                  label: String.fromCharCode(65 + index),
                  teks: j.teks_jawaban,
              }))
            : [
                  { label: 'A', teks: '' },
                  { label: 'B', teks: '' },
              ],
    jawaban_benar: props.value.soal?.jawaban?.find((j: any) => j.apakah_benar)
        ? String.fromCharCode(65 + props.value.soal.jawaban.findIndex((j: any) => j.apakah_benar))
        : '',
});

const imagePreview = ref<string | null>(props.value.soal?.lampiran_gambar ? `/storage/${props.value.soal.lampiran_gambar}` : null);

watch(
    () => form.tipe,
    (newType) => {
        if (newType === 'benar_salah') {
            form.pilihan = [
                { label: 'A', teks: 'Benar' },
                { label: 'B', teks: 'Salah' },
            ];
            form.jawaban_benar = '';
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
        form.pilihan.forEach((opt: any, i: number) => {
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
    form.post(simpanEditBankSoal({ soal_id: soal.value?.id }).url, {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Soal - Administrasi UBK" />

    <div class="py-4 font-serif text-[#333]">
        <div class="flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-2 text-white shadow-md">
            <div class="flex items-center gap-3">
                <div class="rounded-sm bg-white p-1">
                    <div class="flex h-8 w-8 items-center justify-center bg-[#003366] text-[10px] font-bold text-white italic">UBK</div>
                </div>
                <div>
                    <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Editor Bank Soal</h1>
                    <span class="font-mono text-[10px] text-[#ffcc00] uppercase italic">Mode: Perbarui Data Pertanyaan</span>
                </div>
            </div>
            <button @click="window.history.back()" class="flex items-center gap-1 text-xs font-bold tracking-tighter uppercase hover:text-[#ffcc00]">
                <ChevronLeft :size="14" /> Batal & Kembali
            </button>
        </div>

        <div class="mx-auto max-w-7xl p-6">
            <form @submit.prevent="submit" class="grid grid-cols-12 gap-6">
                <div class="col-span-12 space-y-4 lg:col-span-8">
                    <div class="border border-gray-400 bg-white p-6 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                        <div class="mb-6">
                            <label class="mb-2 block text-xs font-bold text-[#003366] uppercase">Teks Pertanyaan / Butir Soal</label>
                            <textarea
                                v-model="form.pertanyaan"
                                rows="6"
                                class="w-full rounded-none border border-gray-400 px-4 py-3 font-sans text-sm leading-relaxed outline-none focus:bg-yellow-50"
                                placeholder="Tuliskan soal disini..."
                            ></textarea>
                            <div
                                v-if="form.errors.pertanyaan"
                                class="mt-1 border-l-2 border-red-600 pl-2 text-[10px] font-bold text-red-600 uppercase italic"
                            >
                                [Peringatan: {{ form.errors.pertanyaan }}]
                            </div>
                        </div>

                        <div v-if="['pilihan_ganda', 'benar_salah'].includes(form.tipe)" class="space-y-4 border-t border-gray-200 pt-6">
                            <h3 class="mb-4 flex items-center gap-2 text-xs font-bold tracking-widest text-gray-500 uppercase">
                                <span class="h-2 w-2 bg-gray-500"></span> Pengaturan Opsi Jawaban
                            </h3>

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
                                        :placeholder="'Ketik Opsi ' + opt.label"
                                    />

                                    <button
                                        v-if="form.pilihan.length > 2 && form.tipe !== 'benar_salah'"
                                        @click="hapusOpsi(index as any)"
                                        type="button"
                                        class="absolute top-2 right-2 text-[10px] font-bold text-red-700 uppercase underline opacity-0 transition group-hover:opacity-100"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            <button
                                v-if="form.pilihan.length < 5 && form.tipe === 'pilihan_ganda'"
                                @click="tambahOpsi"
                                type="button"
                                class="w-full border border-dashed border-gray-400 bg-gray-50 py-2 text-[10px] font-bold tracking-tighter text-gray-500 uppercase transition hover:bg-[#e1e4e8]"
                            >
                                + Tambah Pilihan Baru (Max. 5)
                            </button>
                        </div>

                        <div
                            v-else
                            class="border border-gray-300 bg-gray-50 p-8 text-center text-xs font-bold tracking-widest text-gray-400 uppercase italic"
                        >
                            Mode {{ form.tipe.replace('_', ' ') }}: Input Opsi Dinonaktifkan
                        </div>
                    </div>
                </div>

                <div class="col-span-12 space-y-4 lg:col-span-4">
                    <div class="border border-gray-400 bg-white p-5 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                        <div class="mb-4">
                            <label class="mb-1 block text-[10px] font-bold text-gray-500 uppercase">Kategori Sistem</label>
                            <div class="border border-gray-300 bg-gray-100 p-2 font-mono text-xs text-[#003366]">
                                ID_CAT: #{{ form.quiz_category_id }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="mb-1 block text-[10px] font-bold text-gray-500 uppercase">Tipe Evaluasi</label>
                            <select
                                v-model="form.tipe"
                                class="w-full cursor-pointer border border-gray-400 bg-[#f0f0f0] p-2 text-xs font-bold outline-none focus:bg-yellow-50"
                            >
                                <option value="pilihan_ganda">PILIHAN GANDA</option>
                                <option value="benar_salah">BENAR / SALAH</option>
                                <option value="isian_singkat">ISIAN SINGKAT</option>
                                <option value="essay">ESSAY / URAIAN</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="mb-1 block text-[10px] font-bold text-gray-500 uppercase">Bobot Nilai (Point)</label>
                            <input
                                v-model="form.bobot"
                                type="number"
                                min="1"
                                class="w-full border border-gray-400 bg-[#f0f0f0] px-3 py-2 text-sm font-black text-[#006633] outline-none"
                            />
                        </div>

                        <div class="relative mt-6 overflow-hidden border-2 border-[#003366] bg-[#f0f0f0] p-4 text-center">
                            <span class="absolute top-1 left-1 text-[9px] font-bold text-[#003366] uppercase italic">Kunci:</span>
                            <div class="font-mono text-4xl font-black text-[#003366]">
                                {{ form.jawaban_benar || '??' }}
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-400 bg-white p-5 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                        <label class="mb-2 block flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase">
                            <ImageIcon :size="12" /> Media Lampiran
                        </label>

                        <div
                            v-if="!imagePreview"
                            class="relative cursor-pointer border border-dashed border-gray-400 bg-gray-50 p-6 text-center transition hover:bg-gray-100"
                        >
                            <input type="file" @change="handleFileUpload" class="absolute inset-0 cursor-pointer opacity-0" />
                            <p class="text-[10px] font-bold tracking-tighter text-gray-400 uppercase italic">Klik Untuk Upload Gambar</p>
                        </div>

                        <div v-else class="group relative border border-gray-400 bg-gray-100 p-1">
                            <img :src="imagePreview" class="h-auto max-h-40 w-full object-contain" />
                            <button
                                @click="
                                    imagePreview = null;
                                    form.lampiran_gambar = null;
                                "
                                type="button"
                                class="absolute top-1 right-1 border border-black bg-red-700 px-2 py-0.5 text-[10px] font-bold text-white shadow-[1px_1px_0px_#000]"
                            >
                                HAPUS
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex w-full items-center justify-center gap-2 border-b-4 border-black bg-[#006633] py-3 text-xs font-bold tracking-widest text-white uppercase shadow-lg transition hover:bg-black"
                    >
                        <Save :size="16" /> {{ form.processing ? 'Menyimpan...' : 'Update Database' }}
                    </button>
                </div>
            </form>
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

/* Kunci navigasi boxy */
select {
    appearance: auto !important;
}
</style>
