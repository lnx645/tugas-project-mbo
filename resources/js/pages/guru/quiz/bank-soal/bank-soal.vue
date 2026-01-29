<script setup lang="ts">
import { ref } from 'vue';
import PageTitle from '@/layouts/page-title.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { bankSoalGuru, deleteSoal, editBankSoal, tambahSoalBaru } from '@/routes';
import {  Pencil, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import BankSoalController from '@/actions/App/Http/Controllers/Guru/BankSoalController';


const daftarSoal = computed(()=>{
    return usePage().props.soals as any;
})
function newBankSoal(id : any){
    router.visit(tambahSoalBaru({id}).url)
}
function deleteItem(id : any){
    if( confirm('Apakah Anda Yakin?') ){
        router.delete(deleteSoal({soal_id:id}).url);
    }
}
</script>

<template>
    <PageTitle title="Isi Bank Soal" subtitle="Kategori: Pemrograman Web Dasar" />
    <div class="mt-8  mx-auto px-4 pb-20">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900 leading-tight">Daftar Pertanyaan</h3>
                <p class="text-sm text-gray-500 font-medium">Total {{ daftarSoal.length }} Soal tersedia dalam kategori ini.</p>
            </div>
            <div class="flex items-center gap-3">
                <Link :href="bankSoalGuru()" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600 transition-all">
                    KEMBALI
                </Link>
                
                <Link :href="tambahSoalBaru({id:$page.props?.category_id as any})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-extrabold tracking-wide transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    SOAL BARU
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div v-for="(soal, index) in daftarSoal" :key="soal.id" 
                class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-indigo-500 transition-all group shadow-sm">
                
                <div class="flex flex-col md:flex-row">
                    <div :class="['w-full md:w-2 text-indigo-500', 
                        soal.tipe === 'Essay' ? 'bg-purple-500' : 
                        soal.tipe === 'Pilihan Ganda' ? 'bg-blue-500' : 'bg-emerald-500']">
                    </div>

                    <div class="flex-1 p-5">
                        <div class="flex justify-between items-start gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black bg-gray-100 text-gray-500 px-2 py-0.5 rounded">NO. {{ (index as any) + 1 }}</span>
                                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ soal.tipe }}</span>
                                </div>
                                <h4 class="text-gray-800 font-semibold text-base leading-snug pt-1">
                                    {{ soal.pertanyaan }}
                                </h4>
                            </div>
                            
                            <div class="flex gap-1">
                                <Link :href="editBankSoal({
                                    soal_id : soal.id
                                })" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                                   <Pencil :size="17"/>
                                </Link >
                                <button @click="deleteItem(soal.id)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-600 transition-all">
                                    <Trash2 :size="17"/>
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-gray-50 pt-4">
                            <div class="flex gap-6">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Kunci Jawaban</span>
                                    <span v-if="soal.kunci_jawaban" class="text-xs font-bold text-gray-700">{{ soal.kunci_jawaban?.teks_jawaban ?? '' }}</span>
                                    <span v-else="soal.kunci_jawaban" class="text-xs font-bold text-gray-700">-</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Bobot Nilai</span>
                                    <span class="text-xs font-bold text-indigo-600">{{ soal.bobot }} Poin</span>
                                </div>
                            </div>
                            
                            <div class="hidden md:block">
                                <span class="text-[10px] italic text-gray-300">ID: #SQ-00{{ soal.id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>