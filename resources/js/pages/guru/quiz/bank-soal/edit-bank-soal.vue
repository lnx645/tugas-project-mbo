<script setup lang="ts">
import { computed, ref, watch, onMounted } from 'vue';
import { useForm, Head, usePage } from '@inertiajs/vue3';
import PageTitle from '@/layouts/page-title.vue';
import { simpanEditBankSoal } from '@/routes';

const props = computed(() => {
    return usePage().props as any;
})

const soal = computed(() => {
    return usePage().props.soal as any;
})
const category_id = computed(() => usePage().props.category_id as any);

// 2. Inisialisasi Form langsung dengan data dari props.soal
const form = useForm({
    _method: 'PUT', // Penting: Agar Laravel mengenali ini sebagai Update saat kirim file
    quiz_category_id: props.value.soal?.quiz_category_id || category_id.value,
    pertanyaan: props.value.soal?.pertanyaan || '',
    tipe: props.value.soal?.tipe || 'pilihan_ganda',
    lampiran_gambar: null as File | null,
    bobot: props.value.soal?.bobot || 1,

    // Mapping jawaban dari database ke format form (A, B, C...)
    pilihan: props.value.soal?.jawaban?.length > 0
        ? props.value.soal.jawaban.map((j: any, index: number) => ({
            label: String.fromCharCode(65 + index),
            teks: j.teks_jawaban
        }))
        : [
            { label: 'A', teks: '' },
            { label: 'B', teks: '' },
        ],

    // Cari label mana yang kuncinya true
    jawaban_benar: props.value.soal?.jawaban?.find((j: any) => j.apakah_benar)
        ? String.fromCharCode(65 + props.value.soal.jawaban.findIndex((j: any) => j.apakah_benar))
        : '',
});

// Preview gambar lama jika ada di storage
const imagePreview = ref<string | null>(
    props.value.soal?.lampiran_gambar ? `/storage/${props.value.soal.lampiran_gambar}` : null
);

// LOGIK OTOMATIS UNTUK PERUBAHAN TIPE
watch(() => form.tipe, (newType) => {
    if (newType === 'benar_salah') {
        form.pilihan = [
            { label: 'A', teks: 'Benar' },
            { label: 'B', teks: 'Salah' },
        ];
        form.jawaban_benar = '';
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
        (form.pilihan as any).forEach((opt: any, i: number) => {
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
    // Arahkan ke route update soal
    form.put(simpanEditBankSoal({ soal_id: soal.value?.id }).url, props.value.soal?.id), {
        forceFormData: true,
        onSuccess: () => {
        }
    };
};
</script>

<template>

    <Head title="Edit Bank Soal" />

    <div class="max-w-5xl mx-auto py-8 px-4">
        <PageTitle title="Edit Soal" :subtitle="'Kategori ID: ' + form.quiz_category_id" />

        <form @submit.prevent="submit" class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pertanyaan</label>
                        <textarea v-model="form.pertanyaan" rows="5"
                            class="w-full px-4 py-3 border rounded-xl border-slate-200 focus:ring-2 focus:ring-indigo-500 transition outline-none"
                            placeholder="Masukkan pertanyaan..."></textarea>
                        <div v-if="form.errors.pertanyaan" class="text-red-500 text-xs mt-1">{{ form.errors.pertanyaan
                            }}</div>
                    </div>

                    <div v-if="['pilihan_ganda', 'benar_salah'].includes(form.tipe)" class="space-y-4">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">
                                {{ form.tipe === 'benar_salah' ? 'Opsi Benar/Salah' : 'Opsi Jawaban' }}
                            </h3>
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
                                <input type="text" v-model="opt.teks" :disabled="form.tipe === 'benar_salah'"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-0 transition disabled:bg-slate-50 disabled:font-bold"
                                    :placeholder="'Isi jawaban ' + opt.label" />
                                <button v-if="form.pilihan.length > 2 && form.tipe !== 'benar_salah'"
                                    @click="hapusOpsi(index as any)" type="button"
                                    class="absolute right-2 top-2.5 text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <button v-if="form.pilihan.length < 5 && form.tipe === 'pilihan_ganda'" @click="tambahOpsi"
                            type="button"
                            class="w-full py-2 border-2 border-dashed border-slate-200 rounded-lg text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 transition flex items-center justify-center gap-2 text-sm">
                            + Tambah Opsi
                        </button>
                    </div>

                    <div v-else
                        class="p-10 border-2 border-dashed border-slate-100 rounded-xl text-center text-slate-400">
                        Tipe {{ form.tipe }} tidak memerlukan input pilihan jawaban.
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Soal</label>
                        <select v-model="form.tipe"
                            class="w-full p-3 bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
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

                    <div
                        class="p-4 rounded-xl bg-indigo-50 border border-indigo-100 flex flex-col items-center shadow-inner">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-tighter">Kunci Jawaban
                            Aktif</span>
                        <div class="text-5xl font-black text-indigo-700">
                            {{ form.jawaban_benar || '?' }}
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Lampiran Gambar</label>
                    <div v-if="!imagePreview"
                        class="relative group border-2 border-dashed border-slate-200 p-6 rounded-xl hover:border-indigo-400 transition cursor-pointer text-center">
                        <input type="file" @change="handleFileUpload"
                            class="absolute inset-0 opacity-0 cursor-pointer" />
                        <p class="text-xs text-slate-400 font-medium">Klik untuk ganti gambar</p>
                    </div>
                    <div v-else class="relative group border p-1 rounded-xl shadow-sm bg-slate-50">
                        <img :src="imagePreview" class="w-full h-auto rounded-lg max-h-52 object-contain" />
                        <button @click="imagePreview = null; form.lampiran_gambar = null" type="button"
                            class="absolute -top-2 -right-2 bg-red-500 text-white p-1 rounded-full shadow-lg border-2 border-white hover:scale-110 transition">
                            ✕
                        </button>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl transition shadow-xl shadow-indigo-100 disabled:opacity-50">
                    {{ form.processing ? 'Sedang Memperbarui...' : 'Perbarui Soal' }}
                </button>
            </div>
        </form>
    </div>
</template>