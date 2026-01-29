<script setup lang="ts">
import Input from '@/components/input.vue';
import PageTitle from '@/layouts/page-title.vue';
import { simpanJadwal } from '@/routes';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';

const props = computed(() => usePage().props as any);

const form = useForm({
    judul: '',
    quiz_category_id: '',
    kelas_id: '',
    total_soal: 0,
    max_attempts: 1,
    kkm: 75,
    mulai: '',
    selesai: '',
    durasi: 90,
});

// Logic Otomatis Bank Soal
const totalSoal = computed(() => {
    const finded = props.value.bankSoals.find((data: any) => form.quiz_category_id == data.id);
    return finded ? finded.bank_soals_count : 0;
});

watch(
    () => form.quiz_category_id,
    () => {
        form.total_soal = totalSoal.value;
    },
);

const submit = () => {
    form.post(simpanJadwal().url, {
        onSuccess: () => {
            form.reset();
            toast.success('Jadwal Berhasil Ditambahkan');
        },
    });
};
</script>

<template>
    <PageTitle title="Tambah Jadwal" subtitle="Kelola setting waktu ujian untuk SMK Pasundan 2" />

    <div class="mx-auto mt-6 px-4 pb-12">
        <div class="overflow-hidden rounded-2xl border-gray-100 bg-white">
            <div class="border-b border-gray-100 px-8 py-4">
                <h3 class="text-lg font-bold text-slate-800">Konfigurasi Jadwal</h3>
            </div>

            <form @submit.prevent="submit" class="space-y-8 p-8">
                <div class="space-y-6">
                    <div class="mb-4 flex items-center gap-2 font-semibold text-blue-600">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm">01</span>
                        Informasi Utama
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="label-style">Judul Ujian</label>
                            <Input v-model="form.judul" type="text" placeholder="Misal: UTS Pemrograman Web Ganjil" />
                            <p v-if="form.errors.judul" class="error-text">{{ form.errors.judul }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="label-style">Bank Soal</label>
                            <select v-model="form.quiz_category_id" class="form-select-modern">
                                <option value="">-- Pilih Bank Soal --</option>
                                <option v-for="soal in props.bankSoals" :key="soal.id" :value="soal.id">{{ soal.nama }}</option>
                            </select>
                            <p v-if="form.errors.quiz_category_id" class="error-text">{{ form.errors.quiz_category_id }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="label-style">Kelas Tujuan</label>
                            <select v-model="form.kelas_id" class="form-select-modern">
                                <option value="">-- Pilih Kelas --</option>
                                <option v-for="kelas in props.listKelas" :key="kelas.id_kelas" :value="kelas.id_kelas">{{ kelas.nama_kelas }}</option>
                            </select>
                            <p v-if="form.errors.kelas_id" class="error-text">{{ form.errors.kelas_id }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200 pt-6">
                    <div class="mb-6 flex items-center gap-2 font-semibold text-blue-600">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm">02</span>
                        Aturan & Penjadwalan
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="label-style">Total Soal</label>
                            <Input v-model="form.total_soal" type="number" class="bg-slate-50 font-semibold" />
                            <p v-if="form.errors.total_soal" class="error-text">{{ form.errors.total_soal }}</p>
                        </div>
                        <div>
                            <label class="label-style">Standar KKM</label>
                            <Input v-model="form.kkm" type="number" />
                            <p v-if="form.errors.kkm" class="error-text">{{ form.errors.kkm }}</p>
                        </div>
                        <div>
                            <label class="label-style">Max Percobaan</label>
                            <Input v-model="form.max_attempts" type="number" />
                            <p v-if="form.errors.max_attempts" class="error-text">{{ form.errors.max_attempts }}</p>
                        </div>

                        <div>
                            <label class="label-style">Mulai Akses</label>
                            <Input v-model="form.mulai" type="datetime-local" />
                            <p v-if="form.errors.mulai" class="error-text">{{ form.errors.mulai }}</p>
                        </div>
                        <div>
                            <label class="label-style">Batas Selesai</label>
                            <Input v-model="form.selesai" type="datetime-local" />
                            <p v-if="form.errors.selesai" class="error-text">{{ form.errors.selesai }}</p>
                        </div>
                        <div>
                            <label class="label-style">Durasi (Menit)</label>
                            <Input v-model="form.durasi" type="number" placeholder="Menit" />
                            <p v-if="form.errors.durasi" class="error-text">{{ form.errors.durasi }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                    <p class="text-sm text-slate-500">Pastikan semua data sudah sesuai sebelum menyimpan.</p>
                    <button :disabled="form.processing" type="submit" class="btn-primary-modern">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Jadwal Ujian' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@reference "tailwindcss";

.label-style {
    @apply mb-1 block text-sm font-bold text-slate-700;
}

.form-select-modern {
    @apply mt-1 block w-full rounded-xl border border-gray-200 bg-white p-2.5 text-sm transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10;
}

.btn-primary-modern {
    @apply rounded-xl bg-blue-600 px-8 py-3 font-bold text-white transition-all hover:-translate-y-0.5 hover:bg-blue-700 active:scale-95 disabled:translate-y-0 disabled:bg-gray-400 disabled:shadow-none;
}

.error-text {
    @apply mt-1 text-xs font-medium text-red-500;
}

/* Kustomisasi input type number agar bersih */
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    @apply opacity-100;
}
</style>
