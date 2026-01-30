<script setup lang="ts">
import { periksaJawaban } from '@/routes';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
const props = defineProps<{
    history: any[];
}>();

const selectedKelas = ref('Semua');
const selectedMatpel = ref('Semua');

const daftarKelas = computed(() => {
    const kelasSet = new Set(props.history.map((item) => item.siswa.kelas?.nama || 'Tanpa Kelas'));
    return ['Semua', ...Array.from(kelasSet)];
});

const daftarMatpel = computed(() => {
    const matpelSet = new Set(props.history.map((item) => item.jadwal_quiz.judul || 'Umum'));
    return ['Semua', ...Array.from(matpelSet)];
});

const filteredHistory = computed(() => {
    return props.history.filter((item) => {
        const matchKelas = selectedKelas.value === 'Semua' || (item.siswa.kelas?.nama || 'Tanpa Kelas') === selectedKelas.value;
        const matchMatpel = selectedMatpel.value === 'Semua' || (item.jadwal_quiz.judul || 'Umum') === selectedMatpel.value;
        return matchKelas && matchMatpel;
    });
});

const formatDuration = (start: string, end: string) => {
    if (!start || !end) return '-';
    const duration = new Date(end).getTime() - new Date(start).getTime();
    const minutes = Math.floor(duration / 60000);
    return `${minutes} Menit`;
};

// Fungsi untuk navigasi ke halaman koreksi essay
const evaluasiJawaban = (id: number) => {
    router.visit(periksaJawaban({ id }).url);
};
</script>

<template>
    <div class="min-h-screen py-3 font-serif text-[#333]">
        <div class="flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-2 text-white">
            <div class="flex items-center gap-3">
                <div class="rounded-sm bg-white p-1">
                    <div class="flex h-8 w-8 items-center justify-center bg-[#003366] text-xs font-bold text-white italic">UBK</div>
                </div>
                <div>
                    <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Monitoring Evaluasi Hasil Belajar</h1>
                    <span class="font-mono text-[10px] text-[#ffcc00] uppercase">TAHUN PELAJARAN 2025/2026</span>
                </div>
            </div>
            <div class="text-right font-mono text-xs">Selesai: {{ filteredHistory.length }} Peserta</div>
        </div>

        <div class="p-4">
            <div class="mb-4 flex flex-wrap items-center gap-4 border border-gray-400 bg-[#f0f0f0] p-3 shadow-[2px_2px_0px_rgba(0,0,0,0.1)]">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase">Filter Kelas:</span>
                    <select
                        v-model="selectedKelas"
                        class="min-w-[120px] border border-gray-400 p-1 font-sans text-xs outline-none focus:bg-yellow-50"
                    >
                        <option v-for="kls in daftarKelas" :key="kls" :value="kls">{{ kls }}</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase">Mata Pelajaran:</span>
                    <select
                        v-model="selectedMatpel"
                        class="min-w-[180px] border border-gray-400 p-1 font-sans text-xs outline-none focus:bg-yellow-50"
                    >
                        <option v-for="mp in daftarMatpel" :key="mp" :value="mp">{{ mp }}</option>
                    </select>
                </div>
                <button
                    class="ml-auto border-b-2 border-black bg-[#006633] px-3 py-1 text-xs font-bold text-white uppercase transition-colors hover:bg-black"
                >
                    Refresh Data
                </button>
            </div>

            <div class="overflow-x-auto border border-gray-400 bg-white shadow-sm">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#003366] font-mono text-white uppercase">
                            <th class="w-10 border border-gray-400 p-2 text-center">No</th>
                            <th class="border border-gray-400 p-2 text-left font-normal">Identitas Peserta</th>
                            <th class="w-24 border border-gray-400 p-2 text-center font-normal">Kelas</th>
                            <th class="border border-gray-400 p-2 text-left font-normal">Judul Ujian</th>
                            <th class="w-16 border border-gray-400 p-2 text-center font-normal">Skor</th>
                            <th class="w-24 border border-gray-400 p-2 text-center font-normal">Durasi</th>
                            <th class="w-32 border border-gray-400 p-2 text-center font-normal">Opsi Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#111]">
                        <tr
                            v-for="(data, index) in filteredHistory"
                            :key="data.id"
                            :class="index % 2 === 0 ? 'bg-white' : 'bg-[#f4f4f4]'"
                            class="border-b border-gray-300 hover:bg-[#fff9c4]"
                        >
                            <td class="border border-gray-400 p-2 text-center font-bold">{{ index + 1 }}</td>
                            <td class="border border-gray-400 p-2">
                                <div class="leading-tight font-bold text-[#003366] uppercase">{{ data.siswa.nama }}</div>
                                <div class="font-mono text-[10px] text-gray-500 italic">{{ data.siswa.email }}</div>
                            </td>
                            <td class="border border-gray-400 p-2 text-center font-bold">{{ data.siswa.kelas?.nama || '-' }}</td>
                            <td class="border border-gray-400 p-2 font-medium text-gray-600 uppercase">
                                {{ data.jadwal_quiz.judul || 'Umum' }}
                            </td>
                            <td class="border border-gray-400 p-2 text-center">
                                <span class="text-sm font-black" :class="data.score_result >= 75 ? 'text-green-700' : 'text-red-700'">
                                    {{ Math.round(data.score_result) }}
                                </span>
                            </td>
                            <td class="border border-gray-400 p-2 text-center font-mono">
                                {{ formatDuration(data.start_date, data.end_date) }}
                            </td>
                            <td class="border border-gray-400 p-2 text-center">
                                <button
                                    @click="evaluasiJawaban(data.id)"
                                    class="border border-gray-500 bg-[#ffcc00] px-2 py-1 text-[10px] font-bold text-[#333] uppercase shadow-[1px_1px_0px_#000] hover:bg-black hover:text-white"
                                >
                                    Periksa Jawaban
                                </button>
                            </td>
                        </tr>

                        <tr v-if="filteredHistory.length === 0">
                            <td colspan="7" class="bg-gray-50 p-10 text-center font-bold text-gray-400 italic">-- DATA TIDAK DITEMUKAN --</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-between border border-gray-400 bg-[#fefefe] p-2 text-[10px] text-gray-600">
                <div>
                    <p><strong>Keterangan:</strong> Klik 'Periksa Jawaban' untuk melakukan koreksi pada soal Essay.</p>
                </div>
                <div class="text-right font-mono uppercase italic">Waktu Server: {{ new Date().toLocaleString('id-ID') }}</div>
            </div>
        </div>
    </div>
</template>

<style scoped>
select {
    appearance: auto !important;
    border-radius: 0px !important;
}
table {
    border-spacing: 0;
}
/* Efek tombol ditekan ala aplikasi jadul */
button:active {
    box-shadow: none !important;
    transform: translate(1px, 1px);
}
</style>
