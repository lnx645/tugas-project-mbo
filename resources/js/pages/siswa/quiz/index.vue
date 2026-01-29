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
    <div class="mx-auto max-w-6xl p-4 font-sans text-slate-700">
        <div class="overflow-hidden border border-slate-300 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-300 bg-slate-50 p-3">
                <h2 class="text-sm font-bold tracking-wide text-slate-600 uppercase">Daftar Riwayat Ujian</h2>
                <span class="rounded bg-blue-600 px-2 py-0.5 text-[11px] text-white">Total: {{ histories.length }}</span>
            </div>

            <div
                v-for="(history, index) in histories"
                :key="history.id"
                :class="[
                    'flex flex-col gap-4 border-b border-slate-200 p-3 transition md:flex-row md:items-center',
                    (index as any) % 2 === 0 ? 'bg-white' : 'bg-slate-50/50',
                ]"
            >
                <div class="flex flex-1 items-start gap-3">
                    <div class="mt-1 text-xs font-bold text-slate-400">{{ (index as any) + 1 }}.</div>
                    <div>
                        <div class="text-[10px] font-bold text-blue-500 uppercase">{{ history.jadwal_quiz.judul }}</div>
                        <h3 class="text-sm leading-tight font-bold text-slate-800">{{ history.jadwal_quiz.judul }}</h3>
                        <div class="mt-1 flex gap-3 text-[11px] text-slate-500 italic">
                            <span>Jml. Soal: {{ history.jadwal_quiz.total_soal }}</span>
                            <span>Waktu: {{ history.jadwal_quiz.durasi }} Menit</span>
                            <span>KKM: {{ history.jadwal_quiz.kkm }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex min-w-[150px] flex-row justify-between text-left md:flex-col md:justify-center md:text-right">
                    <span class="text-[10px] leading-none font-bold text-slate-400 uppercase">Status</span>
                    <span :class="['mt-0.5 text-xs font-bold', history.end_date ? 'text-green-600' : 'text-orange-500']">
                        {{ history.end_date ? 'SELESAI' : 'BELUM SELESAI' }}
                    </span>
                </div>

                <div
                    class="flex min-w-[80px] flex-row justify-between text-left md:flex-col md:justify-center md:border-l md:border-slate-200 md:pl-4 md:text-right"
                >
                    <span class="text-[10px] leading-none font-bold text-slate-400 uppercase">Skor</span>
                    <span :class="['mt-0.5 text-sm font-black', history.score_result >= history.jadwal_quiz.kkm ? 'text-blue-600' : 'text-red-500']">
                        {{ history.score_result != null ? Math.round(history.score_result) : '-' }}
                    </span>
                </div>

                <div class="flex justify-end">
                    <Link
                        :href="history.end_date ? hasilDetail({ history_id: history.id }) : `/quiz/kerjakan/${history.id}`"
                        :class="[
                            'rounded border px-4 py-1.5 text-[11px] font-bold uppercase transition',
                            history.end_date
                                ? 'border-slate-300 bg-white text-slate-600 hover:bg-slate-100'
                                : 'border-blue-700 bg-blue-600 text-white shadow-sm hover:bg-blue-700',
                        ]"
                    >
                        {{ history.end_date ? 'Lihat Hasil' : 'Lanjutkan' }}
                    </Link>
                </div>
            </div>

            <div v-if="histories.length === 0" class="p-10 text-center text-xs text-slate-400 italic">Tidak ada data riwayat ujian ditemukan.</div>
        </div>
    </div>
</template>
