<script setup lang="ts">
import { BookOpenIcon, CalendarIcon, ClipboardCheckIcon, ClockIcon, UserIcon, XIcon } from 'lucide-vue-next';
import { ref } from 'vue';

// State untuk modal
const isModalOpen = ref(false);
const selectedJadwal = ref<any>(null);

// Fungsi buka modal
const openDetail = (jadwal: any) => {
    selectedJadwal.value = jadwal;
    isModalOpen.value = true;
};

// Fungsi format tanggal (sama dengan sebelumnya)
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
    });
};
const props = defineProps<{
    jadwal: any;
}>();
</script>

<template>
    <button @click="openDetail(props.jadwal)">
        <slot />
    </button>

    <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="isModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between rounded-t-lg border border-b border-neutral-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Jadwal Quiz</h3>
                    <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <XIcon class="h-5 w-5" />
                    </button>
                </div>

                <div class="px-6 py-6">
                    <div class="mb-6">
                        <label class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Judul Quiz</label>
                        <h2 class="text-xl font-bold text-gray-900">{{ selectedJadwal?.judul }}</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 flex items-center text-xs font-medium text-gray-500">
                                    <UserIcon class="mr-1.5 h-3.5 w-3.5" /> Kelas
                                </label>
                                <p class="text-sm font-semibold text-gray-800">{{ selectedJadwal?.kelas.nama }}</p>
                            </div>
                            <div>
                                <label class="mb-1 flex items-center text-xs font-medium text-gray-500">
                                    <BookOpenIcon class="mr-1.5 h-3.5 w-3.5" /> Bank Soal
                                </label>
                                <p class="text-sm font-semibold text-gray-800">{{ selectedJadwal?.bank_soal_group?.nama }}</p>
                            </div>
                            <div>
                                <label class="mb-1 flex items-center text-xs font-medium text-gray-500">
                                    <ClipboardCheckIcon class="mr-1.5 h-3.5 w-3.5" /> Total Soal
                                </label>
                                <p class="text-sm font-semibold text-gray-800">{{ selectedJadwal?.bank_soal_group?.bank_soals_count }} Butir</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 flex items-center text-xs font-medium text-gray-500">
                                    <ClockIcon class="mr-1.5 h-3.5 w-3.5" /> Durasi
                                </label>
                                <p class="text-sm font-semibold text-gray-800">{{ selectedJadwal?.durasi }} Menit</p>
                            </div>
                            <div>
                                <label class="mb-1 flex items-center text-xs font-medium text-gray-500">
                                    <CalendarIcon class="mr-1.5 h-3.5 w-3.5" /> Batas Percobaan
                                </label>
                                <p class="text-sm font-semibold text-gray-800">{{ selectedJadwal?.max_attempts }} Kali</p>
                            </div>
                            <div>
                                <label class="mb-1 flex items-center text-xs font-medium text-gray-500">
                                    <span class="mr-1.5 font-bold text-blue-600">★</span> KKM
                                </label>
                                <p class="text-sm font-semibold text-gray-800">{{ selectedJadwal?.kkm }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border border-gray-100" />

                    <div class="space-y-3 rounded-md border border-gray-100 bg-gray-50 p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-500">Waktu Mulai:</span>
                            <span class="text-xs font-bold text-gray-700">{{ formatDate(selectedJadwal?.mulai) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-500">Waktu Selesai:</span>
                            <span class="text-xs font-bold text-gray-700">{{ formatDate(selectedJadwal?.selesai) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 rounded-b-lg border-t border-t-neutral-100 bg-gray-50 px-6 py-4">
                    <button
                        @click="isModalOpen = false"
                        class="rounded border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Tutup
                    </button>
                    <button class="rounded bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit Jadwal</button>
                </div>
            </div>
        </div>
    </div>
</template>
