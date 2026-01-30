<template>
    <PageTitle title="Manajemen Quiz" subtitle="Kelola Quiz untuk guru disini" />

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <Link :href="jadwalQuizGuru()" class="flex items-center rounded border border-gray-300 bg-white p-4 transition-colors hover:bg-gray-50">
            <div class="mr-3 rounded bg-gray-100 p-2">
                <i class="font-bold text-gray-600 italic">#</i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Kelola Jadwal</div>
                <div class="text-xs text-gray-500">Atur waktu kuis</div>
            </div>
        </Link>

        <Link :href="bankSoalGuru()" class="flex items-center rounded border border-gray-300 bg-white p-4 transition-colors hover:bg-gray-50">
            <div class="mr-3 rounded bg-gray-100 p-2">
                <i class="font-bold text-gray-600 italic">?</i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Kelola Bank Soal</div>
                <div class="text-xs text-gray-500">Daftar pertanyaan</div>
            </div>
        </Link>

        <Link :href="evaluasiController()" class="flex items-center rounded border border-gray-300 bg-white p-4 transition-colors hover:bg-gray-50">
            <div class="mr-3 rounded bg-gray-100 p-2">
                <i class="font-bold text-gray-600 italic">%</i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Hasil & Evaluasi</div>
                <div class="text-xs text-gray-500">Lihat nilai siswa</div>
            </div>
        </Link>

        <Link href="/quiz/monitoring" class="flex items-center rounded border border-gray-300 bg-white p-4 transition-colors hover:bg-gray-50">
            <div class="mr-3 rounded bg-gray-100 p-2">
                <i class="font-bold text-gray-600 italic">!</i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Monitoring</div>
                <div class="text-xs text-gray-500">Pantau kuis aktif</div>
            </div>
        </Link>
    </div>
</template>

<script setup lang="ts">
import PageTitle from '@/layouts/page-title.vue';
import { bankSoalGuru, evaluasiController, jadwalQuizGuru } from '@/routes';
import { Link } from '@inertiajs/vue3';
</script>
