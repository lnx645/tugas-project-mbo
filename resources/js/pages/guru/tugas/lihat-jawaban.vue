<template>
    <div class="mx-auto mt-4 max-w-full space-y-4">
        <div
            class="flex flex-col items-start justify-between gap-4 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-sm font-bold text-white shadow-md"
                >
                    {{ getInitials(jawaban.user_name) }}
                </div>
                <div>
                    <h1 class="text-sm leading-tight font-bold text-neutral-800 md:text-base">
                        {{ jawaban.user_name }}
                        <span class="font-normal text-neutral-400">({{ jawaban.nis }})</span>
                    </h1>
                    <div class="mt-0.5 flex items-center gap-2 text-xs text-neutral-500">
                        <span class="rounded border border-neutral-200 bg-neutral-100 px-2 py-0.5 font-medium text-neutral-600">
                            {{ jawaban.kelas_nama }}
                        </span>
                        <span>&bull;</span>
                        <span>{{ jawaban.nama_matpel }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end gap-1">
                <button
                    @click="router.visit('/guru/tugas/' + page.tugas_id + '/periksa')"
                    class="flex items-center gap-1 text-xs font-medium text-neutral-500 transition-colors hover:text-neutral-900"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="14"
                        height="14"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Kembali
                </button>
                <div class="text-xs text-neutral-400">Dikirim: {{ formatTanggal(jawaban.created_at) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="flex min-h-[300px] flex-col rounded-xl border border-neutral-200 bg-white shadow-sm md:col-span-2">
                <div class="flex items-center justify-between rounded-t-xl border-b border-neutral-100 bg-neutral-50/50 px-5 py-3">
                    <h3 class="text-xs font-bold tracking-wider text-neutral-500 uppercase">Jawaban Siswa</h3>
                    <div class="flex gap-2">
                        <button class="rounded p-1 text-neutral-400 transition hover:bg-neutral-200 hover:text-neutral-600" title="Salin Teks">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-grow p-5">
                    <div v-if="jawaban.jawaban" class="prose prose-sm max-w-none text-[14px] leading-relaxed text-neutral-700 prose-neutral">
                        {{ jawaban.jawaban }}
                    </div>
                    <div v-else class="flex h-full flex-col items-center justify-center py-10 text-sm text-neutral-400 italic">
                        <span>Tidak ada jawaban tertulis</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-100 bg-neutral-50 px-4 py-2 text-xs font-bold tracking-wider text-neutral-500 uppercase">
                        Lampiran File
                    </div>

                    <div class="p-4">
                        <div v-if="jawaban.file_url">
                            <a :href="jawaban.file_url" target="_blank" class="group relative block">
                                <div
                                    class="flex items-center gap-3 rounded-lg border border-neutral-200 bg-white p-3 transition-all hover:border-blue-400 hover:shadow-sm"
                                >
                                    <div class="rounded-md bg-blue-50 p-2 text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                            />
                                        </svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="truncate text-sm font-medium text-neutral-700 transition-colors group-hover:text-blue-600">
                                            Download File
                                        </p>
                                        <p class="text-[10px] text-neutral-400 uppercase">Klik untuk buka</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div v-else class="py-6 text-center">
                            <p class="text-xs text-neutral-400 italic">Tidak ada file dilampirkan</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-center">
                    <div v-if="jawaban.nilai" class="mb-3">
                        <span class="mb-1 block text-xs font-medium text-indigo-500">Nilai Saat Ini</span>
                        <span class="text-3xl font-bold text-indigo-700">{{ jawaban.nilai.angka || '-' }}</span>
                        <span class="text-sm font-medium text-indigo-400">/100</span>
                    </div>
                    <p v-else class="mb-2 text-xs font-medium text-indigo-600">Perlu memberi nilai?</p>

                    <button
                        @click="showModalNilai = true"
                        class="w-full rounded-lg border border-indigo-200 bg-white py-2 text-xs font-semibold text-indigo-600 shadow-sm transition-all hover:border-transparent hover:bg-indigo-600 hover:text-white"
                    >
                        {{ jawaban.nilai ? 'Ubah Penilaian' : 'Buka Form Penilaian' }}
                    </button>
                </div>
                <ModalTambahNilai :show="showModalNilai" @close="showModalNilai = false" max-width="lg">
                    <div class="flex items-center justify-between border-b border-neutral-100 bg-neutral-50 px-6 py-4">
                        <h3 class="text-lg font-bold text-neutral-800">Input Penilaian</h3>
                        <button @click="showModalNilai = false" class="text-neutral-400 hover:text-neutral-600">✕</button>
                    </div>

                    <form @submit.prevent="submitNilai">
                        <div class="px-6 pt-6">
                            <p class="mb-1 text-sm text-gray-500">Silakan masukkan nilai untuk siswa ini.</p>
                            <input v-model="form.nilai" type="number" class="w-full rounded border p-2" placeholder="0 - 100" />
                        </div>
                        <div class="p-6">
                            <label class="mb-1 block text-sm font-medium text-neutral-700">Komentar / Feedback</label>
                            <textarea
                                v-model="form.komentar"
                                rows="3"
                                class="w-full rounded-lg border border-neutral-300 p-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                placeholder="Berikan catatan..."
                            ></textarea>
                        </div>
                        <div class="flex justify-end gap-3 rounded-b-xl bg-gray-50 px-6 py-4">
                            <button
                                type="button"
                                @click="showModalNilai = false"
                                class="rounded-lg border bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-70"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Nilai' }}
                            </button>
                        </div>
                    </form>
                </ModalTambahNilai>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import NilaiController from '@/actions/App/Http/Controllers/Guru/NilaiController';
import ModalTambahNilai from '@/components/modal-tambah-nilai.vue';
import { formatTanggal } from '@/lib/utils';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage() as any;
const jawaban = computed(() => page.props.jawaban);
const showModalNilai = ref(false);

const form = useForm({
    jawaban_id: jawaban.value.jawabanID,
    tugas_id: jawaban.value.tugas_id,
    nilai: jawaban.value.nilai ? jawaban.value.nilai.angka : 0,
    siswa_id: jawaban.value.user_id,
    komentar: jawaban.value.nilai?.komentar || '', // Ambil komentar lama
});
function submitNilai() {
    form.submit(NilaiController.simpan(), {
        onSuccess() {
            showModalNilai.value = false;
        },
        onError() {
            showModalNilai.value = false;
        },
    });
}

const getInitials = (name: string) => {
    if (!name) return 'S';
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};
</script>
