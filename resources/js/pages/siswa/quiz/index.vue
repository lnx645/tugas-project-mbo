<script setup lang="ts">
import { hasilDetail } from '@/routes';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    histories: any;
}>();

// Format Waktu ANBK: 29 Jan 2026 16:51
const formatANBK = (dateStr: string) => {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} ${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}`;
};
</script>

<template>
    <div class="p-4 font-serif text-[#333]">
        <div class="mx-auto mb-6 max-w-6xl border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-3 text-white shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="rounded-sm bg-white p-1 text-lg leading-none font-black text-[#003366]">H</div>
                    <div>
                        <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Riwayat Aktivitas Ujian</h1>
                        <p class="mt-1 font-mono text-[10px] tracking-tighter text-[#ffcc00] uppercase italic">Log Data Pengerjaan Siswa</p>
                    </div>
                </div>
                <div class="text-right font-mono text-[11px] uppercase">
                    Terdata: <span class="bg-white px-2 font-bold text-[#003366]">{{ histories.length }} Record</span>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-6xl">
            <div
                class="hidden border-x border-t border-gray-400 bg-[#003366] p-2 px-4 text-[10px] font-bold tracking-widest text-white uppercase md:flex"
            >
                <div class="flex-1">Detail Mata Uji & Kategori</div>
                <div class="w-40 text-center">Status</div>
                <div class="w-24 text-center">Nilai</div>
                <div class="w-32 text-right">Aksi Sistem</div>
            </div>

            <div class="overflow-hidden border border-gray-400 bg-white shadow-sm">
                <div
                    v-for="(history, index) in histories"
                    :key="history.id"
                    :class="[
                        'group flex flex-col gap-4 border-b border-gray-300 p-4 transition md:flex-row md:items-center',
                        (index as any) % 2 === 0 ? 'bg-white' : 'bg-[#f4f7f9]',
                    ]"
                >
                    <div class="flex flex-1 items-start gap-4">
                        <div class="mt-1 font-mono text-xs font-bold text-gray-400">{{ (index as any) + 1 }}.</div>
                        <div>
                            <div
                                class="mb-1 inline-block border-b border-gray-200 text-[10px] font-bold tracking-tighter text-[#003366] uppercase italic"
                            >
                                {{ history.jadwal_quiz.judul }}
                            </div>
                            <h3 class="text-sm leading-tight font-black text-gray-800 uppercase transition-colors group-hover:text-blue-700">
                                {{ history.jadwal_quiz.judul }}
                            </h3>
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 font-mono text-[10px] font-bold text-gray-500 uppercase">
                                <span class="flex items-center gap-1 text-slate-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                    Soal: {{ history.jadwal_quiz.total_soal }} Item
                                </span>
                                <span class="flex items-center gap-1 text-slate-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                    Durasi: {{ history.jadwal_quiz.durasi }}'
                                </span>
                                <span class="flex items-center gap-1 border-l border-gray-300 pl-3 text-slate-400">
                                    Tgl Selesai: {{ formatANBK(history.end_date) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex w-full flex-row justify-between border-t border-dashed border-gray-200 pt-3 md:w-40 md:flex-col md:justify-center md:border-none md:pt-0 md:text-center"
                    >
                        <span class="text-[9px] font-bold text-gray-400 uppercase md:hidden">Status Pengerjaan</span>
                        <span
                            :class="[
                                'inline-block border px-2 py-0.5 text-xs font-black',
                                history.end_date ? 'border-green-600 bg-green-50 text-green-700' : 'border-orange-600 bg-orange-50 text-orange-700',
                            ]"
                        >
                            {{ history.end_date ? 'SELESAI' : 'BELUM SELESAI' }}
                        </span>
                    </div>

                    <div
                        class="flex w-full flex-row justify-between md:w-24 md:flex-col md:justify-center md:border-l md:border-gray-200 md:text-center"
                    >
                        <span class="text-[9px] font-bold text-gray-400 uppercase md:hidden">Skor Hasil</span>
                        <div class="flex flex-col">
                            <span
                                :class="[
                                    'text-lg leading-none font-black',
                                    history.score_result >= (history.jadwal_quiz.kkm || 75) ? 'text-blue-700' : 'text-red-600',
                                ]"
                            >
                                {{ history.score_result != null ? Math.round(history.score_result) : '-' }}
                            </span>
                            <span class="mt-1 text-[8px] font-bold text-gray-400 uppercase">KKM: {{ history.jadwal_quiz.kkm }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end md:w-32">
                        <Link
                            :href="history.end_date ? hasilDetail({ history_id: history.id }) : `/quiz/kerjakan/${history.id}`"
                            :class="[
                                'border border-black px-4 py-1.5 text-[10px] font-bold uppercase shadow-[2px_2px_0px_#888] transition active:translate-y-[1px] active:shadow-none',
                                history.end_date
                                    ? 'bg-[#ffcc00] text-black hover:bg-black hover:text-white'
                                    : 'bg-[#003366] text-white hover:bg-black',
                            ]"
                        >
                            {{ history.end_date ? 'Lihat Hasil' : 'Lanjutkan' }}
                        </Link>
                    </div>
                </div>

                <div v-if="histories.length === 0" class="border-b border-gray-300 bg-gray-50 p-20 text-center">
                    <p class="font-mono text-xs font-bold tracking-widest text-gray-400 uppercase italic">
                        -- Tidak ada rekaman riwayat aktivitas ditemukan --
                    </p>
                </div>
            </div>

            <div class="mt-4 border border-gray-400 bg-[#fefefe] p-3 text-[10px] font-medium text-gray-500 italic shadow-sm">
                * Data riwayat pengerjaan bersifat permanen dan tidak dapat diubah oleh siswa. Silakan hubungi proktor/admin sekolah jika terdapat
                ketidaksinkronan data nilai.
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Reset Font untuk look jadul */
button,
h1,
h2,
h3,
div,
a {
    font-family: 'Arial', sans-serif;
}

.font-mono {
    font-family: 'Courier New', Courier, monospace;
}

/* Hilangkan rounded secara paksa */
.rounded,
.rounded-sm,
.rounded-xl {
    border-radius: 0px !important;
}

/* Memastikan border collapse pada grid sistem jadul */
.border-b {
    border-bottom-width: 1px;
}
</style>
