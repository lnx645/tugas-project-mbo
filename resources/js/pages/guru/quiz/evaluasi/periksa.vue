<script setup lang="ts">
//@ts-expect-error
//@ts-nocheck
import { simpanHasilPeriksa } from '@/routes';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    jawaban: any;
}>();

const currentIndex = ref(0);

// Ambil soal aktif berdasarkan navigasi
const soalAktif = computed(() => props.jawaban.jawaban_tersimpan[currentIndex.value]);

const setIndex = (index: number) => {
    currentIndex.value = index;
};

const getTipeLabel = (tipe: string) => {
    return tipe.replace('_', ' ').toUpperCase();
};

// Fungsi Inertia untuk update status jawaban ke database
const toggleCorrectStatus = (item: any) => {
    // Kita kirim data ke backend menggunakan router.patch atau router.put
    router.patch(
        simpanHasilPeriksa({
            id: item.id,
        }).url,
        {
            is_correct: item.is_correct,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Status berhasil diperbarui');
            },
        },
    );
};

const selesaiKoreksi = () => {};
</script>

<template>
    <div class="p-4 font-serif text-[#333]">
        <div class="mb-4 flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-3 text-white shadow-sm">
            <div class="flex items-center gap-4">
                <div class="bg-white px-2 py-1 text-xl font-black text-[#003366]">!</div>
                <div>
                    <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Lembar Koreksi Jawaban Siswa</h1>
                    <p class="mt-1 font-mono text-[10px] text-[#ffcc00]">
                        ID HISTORY: #{{ jawaban.id }} | SKOR SAAT INI: {{ Math.round(jawaban.score_result) }}
                    </p>
                </div>
            </div>
            <button
                @click="selesaiKoreksi"
                class="border border-white bg-red-700 px-4 py-1 text-xs font-bold text-white uppercase shadow-[2px_2px_0px_#000] hover:bg-black active:translate-y-[1px] active:shadow-none"
            >
                Selesai Koreksi
            </button>
        </div>

        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-3 lg:col-span-2">
                <div class="border border-gray-400 bg-[#f0f0f0] p-3 shadow-sm">
                    <h2 class="mb-3 border-b border-gray-400 pb-1 text-center text-xs font-bold uppercase">Daftar Soal</h2>
                    <div class="grid grid-cols-4 gap-2">
                        <button
                            v-for="(item, index) in jawaban.jawaban_tersimpan"
                            :key="item.id"
                            @click="setIndex(index)"
                            :class="[
                                currentIndex === index
                                    ? 'border-black bg-[#ffcc00] text-black'
                                    : item.is_correct
                                      ? 'border-gray-400 bg-white text-gray-700'
                                      : 'border-red-400 bg-red-500 text-white',
                                item.is_correct
                                    ? 'after:absolute after:top-0 after:right-0.5 after:text-[8px] after:text-green-600 after:content-[\'\u2713\']'
                                    : '',
                            ]"
                            class="relative h-10 border text-sm font-bold shadow-[1px_1px_0px_rgba(0,0,0,0.1)] transition-all"
                        >
                            {{ index + 1 }}
                        </button>
                    </div>
                </div>

                <div class="mt-4 border border-gray-400 bg-white p-2 text-[10px] font-bold text-gray-500 uppercase">
                    Siswa: <span class="text-[#003366]">{{ jawaban.siswa_id }}</span
                    ><br />
                    Status: <span class="text-green-600">Terikirim</span>
                </div>
            </div>

            <div class="col-span-12 md:col-span-9 lg:col-span-10">
                <div class="relative min-h-[450px] border border-gray-400 bg-white p-8 shadow-sm">
                    <div class="mb-6 flex items-center justify-between border-b border-dashed border-gray-300 pb-2">
                        <span class="bg-gray-200 px-3 py-1 text-[10px] font-bold tracking-widest text-gray-600 uppercase">
                            SOAL NOMOR {{ currentIndex + 1 }} - {{ getTipeLabel(soalAktif.soal.tipe) }}
                        </span>
                        <div class="flex items-center gap-2 font-mono text-xs">BOBOT: {{ soalAktif.soal.bobot }}</div>
                    </div>

                    <div class="mb-8 font-sans text-[17px] leading-relaxed text-slate-800">
                        <p v-html="soalAktif.soal.pertanyaan"></p>
                    </div>

                    <div class="mb-10 rounded border-2 border-gray-200 bg-[#f9f9f9] p-5 shadow-inner">
                        <h3 class="mb-3 text-[10px] font-bold tracking-tighter text-gray-400 uppercase italic">Input Jawaban Siswa:</h3>
                        <div class="text-lg font-bold text-[#003366]">
                            <template v-if="soalAktif.jawaban">
                                {{ soalAktif.jawaban.teks_jawaban }}
                            </template>
                            <template v-else>
                                <div class="min-h-[50px] border border-gray-300 bg-white p-3 font-mono text-sm">
                                    {{ soalAktif.jawaban_texts || '-- TIDAK MENJAWAB --' }}
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="absolute right-0 bottom-0 left-0 flex items-center justify-between border-t-4 border-[#003366] bg-[#f0f0f0] p-4">
                        <div class="flex items-center gap-6">
                            <label
                                class="group flex cursor-pointer items-center gap-3 border border-gray-400 bg-white px-4 py-2 shadow-sm hover:bg-green-50"
                            >
                                <input
                                    type="checkbox"
                                    v-model="soalAktif.is_correct"
                                    @change="toggleCorrectStatus(soalAktif)"
                                    class="h-6 w-6 border-gray-400 accent-[#006633]"
                                />
                                <span
                                    class="text-sm font-bold uppercase select-none"
                                    :class="soalAktif.is_correct ? 'text-green-700' : 'text-red-700'"
                                >
                                    Tandai Sebagai Benar
                                </span>
                            </label>
                        </div>

                        <div class="flex gap-2">
                            <button
                                :disabled="currentIndex === 0"
                                @click="currentIndex--"
                                class="border border-gray-500 bg-gray-300 px-6 py-2 text-xs font-bold uppercase hover:bg-gray-400 active:bg-gray-500 disabled:opacity-30"
                            >
                                Sebelumnya
                            </button>
                            <button
                                :disabled="currentIndex === jawaban.jawaban_tersimpan.length - 1"
                                @click="currentIndex++"
                                class="border border-black bg-[#003366] px-6 py-2 text-xs font-bold text-white uppercase shadow-[2px_2px_0px_#ffcc00] active:translate-y-[1px] active:shadow-none"
                            >
                                Selanjutnya
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 border border-yellow-200 bg-yellow-50 p-3 text-[11px] leading-tight text-yellow-800 shadow-sm">
                    <strong>PENTING:</strong> Menekan kotak <strong>"Tandai Sebagai Benar"</strong> akan langsung memperbarui database siswa melalui
                    sistem Inertia. Gunakan fitur ini terutama untuk mengoreksi tipe soal <strong>ISIAN/ESSAY</strong>.
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
button,
input,
select {
    font-family: 'Arial', sans-serif;
}

input[type='checkbox'] {
    cursor: pointer;
}

/* Navigasi Grid hover effect */
.grid button:hover {
    border-color: #000;
}
</style>
