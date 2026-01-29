<script setup lang="ts">
import { computed, ref, watch } from 'vue'; // Tambahkan watch
import { useForm, Head, usePage } from '@inertiajs/vue3';
import PageTitle from '@/layouts/page-title.vue';
import { simpanBankSoal } from '@/routes';
const category_id = computed(() => usePage().props.category_id as any)
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
watch(() => form.tipe, (newType) => {
    if (newType === 'benar_salah') {
        form.pilihan = [
            { label: 'A', teks: 'Benar' },
            { label: 'B', teks: 'Salah' },
        ];
        form.jawaban_benar = ''; // Reset kunci
    } else if (newType === 'pilihan_ganda') {
        // Kembalikan ke format default pilihan ganda jika pindah tipe
        form.pilihan = [
            { label: 'A', teks: '' },
            { label: 'B', teks: '' },
        ];
    }
});

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
    console.log(form.data());

    form.post(simpanBankSoal({
        id: 1
    }).url, { forceFormData: true });
};
</script>

<template>

    <Head title="Tambah Soal" />

    <div class="max-w-5xl mx-auto py-8 px-4">
        <PageTitle title="Tambah Soal Baru" subtitle="Kategori: Pemrograman Web Dasar" />

        <form @submit.prevent="submit" class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pertanyaan</label>
                        <textarea v-model="form.pertanyaan" rows="5"
                            class="w-full px-4 py-3 border rounded-xl border-slate-200 focus:ring-2 focus:ring-indigo-500 transition placeholder:text-slate-400"
                            placeholder="Contoh: Apakah HTML adalah bahasa pemrograman?"></textarea>
                    </div>

                    <div v-if="form.tipe === 'pilihan_ganda' || form.tipe === 'benar_salah'" class="space-y-4">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">
                                {{ form.tipe === 'benar_salah' ? 'Opsi Benar/Salah' : 'Opsi Jawaban' }}
                            </h3>
                            <span class="text-xs text-slate-500 italic">*Klik huruf untuk kunci</span>
                        </div>

                        <div v-for="(opt, index) in form.pilihan" :key="index" class="group flex items-start gap-3">
                            <button type="button" @click="form.jawaban_benar = opt.label" :class="[
                                'mt-1 w-10 h-10 shrink-0 rounded-lg flex items-center justify-center font-bold transition-all border-2',
                                form.jawaban_benar === opt.label
                                    ? 'bg-green-500 border-green-600 text-white shadow-md'
                                    : 'bg-slate-50 border-slate-200 text-slate-400 hover:border-indigo-400'
                            ]">
                                {{ opt.label }}
                            </button>

                            <div class="relative w-full">
                                <input type="text" v-model="opt.teks" :disabled="form.tipe === 'benar_salah'" :class="[
                                    'w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-0 outline-none transition',
                                    form.tipe === 'benar_salah' ? 'bg-slate-100 font-semibold text-slate-700 cursor-not-allowed' : ''
                                ]" :placeholder="'Ketik jawaban ' + opt.label" />
                                <button v-if="form.pilihan.length > 2 && form.tipe !== 'benar_salah'"
                                    @click="hapusOpsi(index)" type="button"
                                    class="absolute right-2 top-2.5 text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button v-if="form.pilihan.length < 5 && form.tipe === 'pilihan_ganda'" @click="tambahOpsi"
                            type="button"
                            class="w-full py-2 border-2 border-dashed border-slate-200 rounded-lg text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 transition flex items-center justify-center gap-2 text-sm">
                            + Tambah Pilihan
                        </button>
                    </div>

                    <div v-else
                        class="p-8 border-2 border-dashed border-slate-100 rounded-xl text-center text-slate-400">
                        Tipe soal {{ form.tipe.replace('_', ' ') }} tidak memerlukan opsi pilihan ganda.
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Soal</label>
                        <select v-model="form.tipe"
                            class="w-full p-3 bg-slate-50 border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="pilihan_ganda">Pilihan Ganda</option>
                            <option value="benar_salah">Benar / Salah</option>
                            <option value="isian_singkat">Isian Singkat</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Bobot Soal</label>
                        <input v-model="form.bobot" type="number" min="1"
                            class="w-full bg-white border-2 border-gray-100 px-4 py-2 rounded-xl focus:outline-none focus:border-indigo-500 font-bold text-gray-800"
                            placeholder="Contoh: 5" />
                        <p class="text-[10px] text-gray-500 font-medium">
                            *Poin yang didapat siswa jika menjawab benar pada soal ini.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100 flex flex-col items-center">
                        <span class="text-[10px] font-black text-indigo-400 uppercase">Kunci Terpilih</span>
                        <div class="text-4xl font-black text-indigo-700">
                            {{ form.jawaban_benar || '?' }}
                        </div>
                        <span v-if="form.jawaban_benar && form.tipe === 'benar_salah'"
                            class="text-xs font-bold text-indigo-500">
                            ({{form.pilihan.find(p => p.label === form.jawaban_benar)?.teks}})
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Lampiran Gambar</label>
                    <div v-if="!imagePreview"
                        class="relative group border-2 border-dashed border-slate-200 p-6 rounded-xl hover:border-indigo-400 transition cursor-pointer text-center">
                        <input type="file" @change="handleFileUpload"
                            class="absolute inset-0 opacity-0 cursor-pointer" />
                        <p class="text-xs text-slate-400">Klik untuk upload gambar</p>
                    </div>
                    <div v-else class="relative group">
                        <img :src="imagePreview" class="w-full h-auto rounded-xl border" />
                        <button @click="imagePreview = null; form.lampiran_gambar = null" type="button"
                            class="absolute -top-2 -right-2 bg-red-500 text-white p-1 rounded-full shadow-lg">✕</button>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl transition shadow-xl shadow-indigo-100">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Soal' }}
                </button>
            </div>
        </form>
    </div>
</template>