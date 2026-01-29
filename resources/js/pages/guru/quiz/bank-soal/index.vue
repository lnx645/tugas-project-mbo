<template>
    <PageTitle title="Manajemen Quiz" subtitle="Kelola Bank Soal Anda di sini" />

    <div class="mt-8 mx-auto px-4 pb-20">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Kategori Soal</h2>
                <p class="text-sm text-gray-500 font-medium">Total {{ category.length }} kategori tersedia.</p>
            </div>
            <button @click="openModal()" 
                class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                TAMBAH KATEGORI
            </button>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">No</th>
                            <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Nama Kategori</th>
                            <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Jumlah Soal</th>
                            <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(item, index) in category" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-gray-400 w-16">
                                {{ (index as any) + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    {{ item.nama }}
                                </h4>
                                <span class="text-[10px] text-gray-400 font-medium">ID: #PKG-{{ item.id }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="item.bank_soals_count" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                    {{ item.bank_soals_count }} Soal
                                </span>
                                <span v-else class="text-xs font-medium italic text-gray-300">Kosong</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="showBankSoal({ id: item.id })" 
                                        class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-[11px] font-bold hover:bg-indigo-600 hover:text-white transition-all">
                                        KELOLA SOAL
                                    </Link>
                                    <button @click="openModal(item)" class="p-2 rounded-lg text-gray-400 hover:bg-amber-50 hover:text-amber-600 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="category.length === 0">
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                                Belum ada kategori soal yang dibuat.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <Transition name="fade">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">
                        {{ isEdit ? 'Update Kategori' : 'Kategori Baru' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Kategori / Paket</label>
                        <input v-model="form.nama" type="text" 
                            class="w-full bg-gray-50 border-2 border-gray-100 px-4 py-3 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all font-medium" 
                            placeholder="Contoh: Pemrograman Web Dasar" required />
                    </div>
                    <button type="submit" :disabled="form.processing"
                        class="w-full py-3.5 bg-indigo-600 text-white rounded-xl text-xs font-bold tracking-widest hover:bg-indigo-700 transition-all disabled:opacity-50">
                        {{ isEdit ? 'SIMPAN PERUBAHAN' : 'BUAT KATEGORI' }}
                    </button>
                </form>
            </div>
        </div>
    </Transition>
</template>


<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
<script setup lang="ts">
import { ref, computed } from 'vue';
import PageTitle from '@/layouts/page-title.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { addNewCategory,showBankSoal, editQuizCategory } from '@/routes'; // Asumsi route edit juga ada di sini
import { toast } from 'vue-sonner';

const showModal = ref(false);
const isEdit = ref(false);
const selectedId = ref<number | null>(null);

const form = useForm({
    nama: '',
});

const page = usePage();
const category = computed(() => {
    return page.props.category as any;
});

// Fungsi untuk buka modal (bisa untuk tambah atau edit)
const openModal = (item: any|null = null) => {
    if (item) {
        isEdit.value = true;
        selectedId.value = item.id;
        form.nama = item.nama;
    } else {
        isEdit.value = false;
        selectedId.value = null;
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (isEdit.value) {
        form.submit(editQuizCategory({
            id : (selectedId.value as any),
        }), {
            onSuccess: () =>{
                 closeModal()
                 toast.success("Kategori baru berhasil di tambahkan")
            },
        });
    } else {
        // Logic Tambah
        form.post(addNewCategory().url, {
            onSuccess: () =>{
                 closeModal()
                 toast.success("Kategori  berhasil di update")
            },
        });
    }
};
</script>