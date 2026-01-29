<template>
    <PageTitle title="Manajemen Quiz" subtitle="Kelola Quiz untuk guru disini" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <Link :href="jadwalQuizGuru()" class="flex items-center p-4 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
            <div class="p-2 bg-gray-100 rounded mr-3">
                <i class="text-gray-600 font-bold italic">#</i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Kelola Jadwal</div>
                <div class="text-xs text-gray-500">Atur waktu kuis</div>
            </div>
        </Link>

        <Link :href="bankSoalGuru()" class="flex items-center p-4 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
            <div class="p-2 bg-gray-100 rounded mr-3">
                <i class="text-gray-600 font-bold italic">?</i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Kelola Bank Soal</div>
                <div class="text-xs text-gray-500">Daftar pertanyaan</div>
            </div>
        </Link>

        <Link href="/quiz/hasil" class="flex items-center p-4 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
            <div class="p-2 bg-gray-100 rounded mr-3">
                <i class="text-gray-600 font-bold italic">%</i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">Hasil & Evaluasi</div>
                <div class="text-xs text-gray-500">Lihat nilai siswa</div>
            </div>
        </Link>

        <Link href="/quiz/monitoring" class="flex items-center p-4 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
            <div class="p-2 bg-gray-100 rounded mr-3">
                <i class="text-gray-600 font-bold italic">!</i>
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
import { jadwalQuizGuru,bankSoalGuru } from '@/routes';
import { Link } from '@inertiajs/vue3';

</script>