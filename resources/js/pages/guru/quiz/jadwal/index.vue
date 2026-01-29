<script setup lang="ts">
import PageTitle from '@/layouts/page-title.vue';
import { formTambahJadwal } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpenIcon, ChevronRightIcon, ClockIcon, PlusIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import ShowJadwalDetail from './show-jadwal-detail.vue';

const jadwals = computed(() => {
    return usePage().props.jadwals as any[];
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <div class="p-6">
        <div class="mb-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageTitle title="Jadwal Quiz" subtitle="Daftar jadwal aktif" />

            <Link
                :href="formTambahJadwal().url"
                class="inline-flex items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
            >
                <PlusIcon class="mr-2 h-4 w-4" />
                Tambah Jadwal
            </Link>
        </div>

        <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
            <div v-if="jadwals.length === 0" class="p-12 text-center text-gray-500">Belum ada data jadwal quiz.</div>

            <div v-else class="divide-y divide-gray-200">
                <div
                    v-for="jadwal in jadwals"
                    :key="jadwal.id"
                    class="flex flex-col p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex-1">
                        <div class="mb-1 flex items-center gap-2">
                            <span class="text-xs font-semibold tracking-wider text-blue-600 uppercase">
                                {{ jadwal.kelas.nama }}
                            </span>
                            <span class="text-gray-300">|</span>
                            <span class="text-xs text-gray-500">KKM: {{ jadwal.kkm }}</span>
                        </div>

                        <h3 class="mb-2 text-base leading-none font-semibold text-gray-900">
                            {{ jadwal.judul }}
                        </h3>

                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                            <div class="flex items-center">
                                <BookOpenIcon class="mr-1.5 h-3.5 w-3.5" />
                                {{ jadwal.bank_soal_group?.nama }} ({{ jadwal.bank_soal_group?.bank_soals_count }} Soal)
                            </div>
                            <div class="flex items-center">
                                <ClockIcon class="mr-1.5 h-3.5 w-3.5" />
                                {{ jadwal.durasi }} Menit
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4 sm:mt-0 sm:border-0 sm:pt-0">
                        <div class="text-left sm:mr-6 sm:text-right">
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Jadwal Mulai</p>
                            <p class="text-sm text-gray-700">{{ formatDate(jadwal.mulai) }}</p>
                        </div>

                        <div class="flex items-center rounded border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                            <div>
                                <ShowJadwalDetail :jadwal="jadwal">Detail</ShowJadwalDetail>
                            </div>
                            <ChevronRightIcon class="ml-1 h-3 w-3" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
