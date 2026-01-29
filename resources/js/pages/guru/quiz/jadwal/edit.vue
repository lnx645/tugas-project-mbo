<script setup lang="ts">
import Input from '@/components/input.vue';
import PageTitle from '@/layouts/page-title.vue';
import { simpanEditJadwal } from '@/routes';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';

const props = computed(() => usePage().props as any);
const jadwal = computed(() => props.value.jadwal);

const form = useForm({
    judul: jadwal.value.judul,
    quiz_category_id: jadwal.value.quiz_category_id,
    kelas_id: jadwal.value.kelas_id,
    total_soal: jadwal.value.total_soal,
    max_attempts: jadwal.value.max_attempts,
    kkm: jadwal.value.kkm,
    mulai: jadwal.value.mulai ? new Date(jadwal.value.mulai).toISOString().slice(0, 16) : '',
    selesai: jadwal.value.selesai ? new Date(jadwal.value.selesai).toISOString().slice(0, 16) : '',
    durasi: jadwal.value.durasi,
});

const totalSoalTersedia = computed(() => {
    const found = props.value.bankSoals.find((s: any) => s.id == form.quiz_category_id);
    return found ? found.bank_soals_count : 0;
});

watch(
    () => form.quiz_category_id,
    (newVal) => {
        if (newVal != jadwal.value.quiz_category_id) {
            form.total_soal = totalSoalTersedia.value;
        }
    },
);

const submit = () => {
    (form.put(
        simpanEditJadwal({
            id: jadwal.value.id,
        }).url,
    ),
        {
            onSuccess: () => toast.success('Jadwal berhasil diupdate!'),
            onError: () => toast.error('Cek kembali inputan lu, jik!'),
        });
};
</script>

<template>
    <PageTitle title="Edit Jadwal" subtitle="Sesuaikan kembali waktu ujian" />

    <div class="mx-auto mt-6 px-4 pb-12">
        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="rounded-t-2xl border-b border-gray-100 bg-slate-50/50 px-8 py-4">
                <h3 class="text-lg font-bold text-slate-800">Ubah Konfigurasi: {{ jadwal.judul }}</h3>
            </div>

            <form @submit.prevent="submit" class="space-y-8 p-8">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="label-style">Judul Ujian</label>
                        <Input v-model="form.judul" type="text" placeholder="Contoh: Remedial UAS" />
                    </div>

                    <div class="space-y-1">
                        <label class="label-style">Bank Soal</label>
                        <select v-model="form.quiz_category_id" class="form-select-modern">
                            <option v-for="soal in props.bankSoals" :key="soal.id" :value="soal.id">
                                {{ soal.nama }} ({{ soal.bank_soals_count }} Soal)
                            </option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="label-style">Kelas</label>
                        <select v-model="form.kelas_id" class="form-select-modern">
                            <option v-for="k in props.listKelas" :key="k.id_kelas" :value="k.id_kelas">
                                {{ k.nama_kelas }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 border-t border-dashed border-gray-200 pt-6 md:grid-cols-3">
                    <div>
                        <label class="label-style">Total Soal (Max: {{ totalSoalTersedia }})</label>
                        <Input v-model="form.total_soal" type="number" />
                    </div>
                    <div>
                        <label class="label-style">KKM</label>
                        <Input v-model="form.kkm" type="number" />
                    </div>
                    <div>
                        <label class="label-style">Durasi (Menit)</label>
                        <Input v-model="form.durasi" type="number" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="label-style">Waktu Mulai</label>
                        <Input v-model="form.mulai" type="datetime-local" />
                    </div>
                    <div>
                        <label class="label-style">Batas Selesai</label>
                        <Input v-model="form.selesai" type="datetime-local" />
                    </div>
                </div>

                <div class="flex items-center justify-between border-t pt-6">
                    <button type="button" @click="$window.history.back()" class="text-sm font-bold text-gray-400">Batal</button>
                    <button :disabled="form.processing" type="submit" class="btn-primary-modern shadow-lg shadow-blue-200">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@reference "tailwindcss";
.label-style {
    @apply mb-1 block text-[12px] font-bold tracking-tight text-slate-500 uppercase;
}
.form-select-modern {
    @apply block w-full rounded-xl border border-gray-200 bg-white p-2.5 text-sm transition-all outline-none focus:ring-4 focus:ring-blue-500/10;
}
.btn-primary-modern {
    @apply rounded-xl bg-blue-600 px-10 py-3 font-bold text-white transition-all hover:bg-blue-700 active:scale-95 disabled:bg-gray-300;
}
</style>
