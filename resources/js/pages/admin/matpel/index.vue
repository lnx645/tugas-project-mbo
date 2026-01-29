<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import Modal from '@/components/ui/modal.vue';
import Paging from '@/components/paging.vue';
// Import icon tambahan: FileSpreadsheet, Upload, X
import { Pencil, Trash2, Plus, Search, FileSpreadsheet, Upload, X } from 'lucide-vue-next';
import MatpelController from '@/actions/App/Http/Controllers/Admin/MatpelController';

const props = defineProps({
    matpels: Object as any,
    filters: Object as any
});

const search = ref(props.filters?.search || '');

// --- STATE MODAL ---
const showAddModal = ref(false);
const showEditModal = ref(false);
const showImportModal = ref(false); // State untuk Modal Import

// --- FORM DEFINITIONS ---

// 1. Form Tambah
const formAdd = useForm({
    kode: '',
    nama: '',
    kelompok: ''
});

// 2. Form Edit (Menyimpan original_kode untuk URL update)
const formEdit = useForm({
    original_kode: '' as string,
    kode: '',
    nama: '',
    kelompok: ''
});

// 3. Form Import Excel
const formImport = useForm({
    file: null as File | null,
});

// Search Logic
watch(search, debounce((val: string) => {
    router.get(
        MatpelController.index().url, 
        { search: val }, 
        { preserveState: true, replace: true }
    );
}, 300));

// --- LOGIC TAMBAH ---
const openAddModal = () => {
    formAdd.reset();
    formAdd.clearErrors();
    showAddModal.value = true;
};

const submitAdd = () => {
    formAdd.post(MatpelController.store().url, {
        onSuccess: () => {
            showAddModal.value = false;
            formAdd.reset();
        }
    });
};

// --- LOGIC IMPORT EXCEL (BARU) ---
const openImportModal = () => {
    formImport.reset();
    formImport.clearErrors();
    showImportModal.value = true;
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        formImport.file = target.files[0];
    }
};

const submitImport = () => {
    // Pastikan route ini sesuai dengan route di Laravel (web.php)
    formImport.post('/admin/matpel/import', {
        onSuccess: () => {
            showImportModal.value = false;
            formImport.reset();
            router.reload(); // Refresh halaman untuk melihat data baru
        },
        onError: (err) => {
            console.error('Import Gagal', err);
        }
    });
};

// --- LOGIC EDIT (Fixed: Pakai Kode) ---
const openEditModal = (data: any) => {
    if (!data) return;

    formEdit.clearErrors();
    formEdit.reset();

    // Simpan kode asli agar URL update tetap valid meskipun kode diganti user
    formEdit.original_kode = data.kode;

    // Isi data ke form
    formEdit.kode = data.kode;
    formEdit.nama = data.nama;
    formEdit.kelompok = data.kelompok;
    
    showEditModal.value = true;
};

const submitEdit = () => {
    // Gunakan original_kode untuk parameter URL
    if (!formEdit.original_kode) return;

    formEdit.put(MatpelController.update({ matpel: formEdit.original_kode }).url, {
        onSuccess: () => {
            showEditModal.value = false;
            formEdit.reset();
        }
    });
};

// --- LOGIC DELETE (Fixed: Pakai Kode) ---
const deleteData = (kode: string) => {
    if (confirm('Hapus mata pelajaran ini?')) {
        router.delete(MatpelController.destroy({ matpel: kode }).url);
    }
};
</script>

<template>
    <div class="p-6">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Data Mata Pelajaran</h1>
            
            <div class="flex gap-2">
                <button @click="openImportModal()" class="bg-emerald-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-emerald-700 transition shadow-sm text-sm font-medium">
                    <FileSpreadsheet :size="18" /> Import Excel
                </button>

                <button @click="openAddModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-indigo-700 transition shadow-sm text-sm font-medium">
                    <Plus :size="18" /> Tambah Matpel
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="mb-4 relative max-w-sm">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" :size="16" />
                <input 
                    v-model="search" 
                    type="text" 
                    placeholder="Cari kode atau nama matpel..." 
                    class="pl-10 w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                >
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="p-3 font-semibold">Kode</th>
                            <th class="p-3 font-semibold">Nama Matpel</th>
                            <th class="p-3 font-semibold">Kelompok</th>
                            <th class="p-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in matpels.data" :key="item.kode" class="hover:bg-gray-50 transition-colors">
                            <td class="p-3 font-mono font-bold text-indigo-600">{{ item.kode }}</td>
                            <td class="p-3 font-medium text-gray-900">{{ item.nama }}</td>
                            <td class="p-3 text-gray-600">
                                <span class="bg-gray-100 px-2 py-1 rounded text-xs border border-gray-200">
                                    {{ item.kelompok || '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="openEditModal(item)" class="p-1.5 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Edit">
                                        <Pencil :size="18" />
                                    </button>
                                    <button @click="deleteData(item.kode)" class="p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus">
                                        <Trash2 :size="18" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="matpels.data.length === 0">
                            <td colspan="4" class="p-8 text-center text-gray-500 italic">
                                Belum ada data mata pelajaran.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 border-t pt-4">
                <Paging :links="matpels.links" />
            </div>
        </div>

        <Modal :isOpen="showImportModal" @close="showImportModal = false">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <FileSpreadsheet class="text-emerald-600" /> Import Excel
                    </h2>
                </div>
                
                <form @submit.prevent="submitImport">
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition relative">
                            <input 
                                type="file" 
                                @change="handleFileChange"
                                accept=".xlsx, .xls"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            >
                            <div class="flex flex-col items-center gap-2">
                                <Upload class="text-gray-400" :size="32" />
                                <span class="text-sm font-medium text-gray-600" v-if="!formImport.file">
                                    Klik atau tarik file Excel ke sini
                                </span>
                                <span class="text-sm font-medium text-emerald-600 break-all" v-else>
                                    {{ formImport.file.name }}
                                </span>
                                <span class="text-xs text-gray-400">Format: .xlsx, .xls</span>
                            </div>
                        </div>

                        <div v-if="formImport.errors.file" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm flex gap-2 items-center">
                            <X :size="16" /> {{ formImport.errors.file }}
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showImportModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="formImport.processing" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 disabled:opacity-50 transition shadow-sm">
                            {{ formImport.processing ? 'Mengupload...' : 'Import Sekarang' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :isOpen="showAddModal" @close="showAddModal = false">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Tambah Mata Pelajaran</h2>
                </div>
                
                <form @submit.prevent="submitAdd">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Matpel</label>
                            <input v-model="formAdd.kode" type="text" class="w-full border border-gray-300 rounded-lg p-2.5 uppercase focus:ring-indigo-500 focus:border-indigo-500" placeholder="MTK" required>
                            <p v-if="formAdd.errors.kode" class="text-red-500 text-xs mt-1">{{ formAdd.errors.kode }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Matpel</label>
                            <input v-model="formAdd.nama" type="text" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Matematika" required>
                            <p v-if="formAdd.errors.nama" class="text-red-500 text-xs mt-1">{{ formAdd.errors.nama }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelompok</label>
                            <select v-model="formAdd.kelompok" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Kelompok --</option>
                                <option value="Muatan Nasional">Muatan Nasional</option>
                                <option value="Muatan Kewilayahan">Muatan Kewilayahan</option>
                                <option value="Muatan Kejuruan">Muatan Kejuruan</option>
                            </select>
                            <p v-if="formAdd.errors.kelompok" class="text-red-500 text-xs mt-1">{{ formAdd.errors.kelompok }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="formAdd.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm">
                            {{ formAdd.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :isOpen="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Edit Mata Pelajaran</h2>
                </div>
                
                <form @submit.prevent="submitEdit">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Matpel</label>
                            <input v-model="formEdit.kode" type="text" class="w-full border border-gray-300 rounded-lg p-2.5 uppercase focus:ring-indigo-500 focus:border-indigo-500" required>
                            <p v-if="formEdit.errors.kode" class="text-red-500 text-xs mt-1">{{ formEdit.errors.kode }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Matpel</label>
                            <input v-model="formEdit.nama" type="text" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <p v-if="formEdit.errors.nama" class="text-red-500 text-xs mt-1">{{ formEdit.errors.nama }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelompok</label>
                            <select v-model="formEdit.kelompok" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Kelompok --</option>
                                <option value="Muatan Nasional">Muatan Nasional</option>
                                <option value="Muatan Kewilayahan">Muatan Kewilayahan</option>
                                <option value="Muatan Kejuruan">Muatan Kejuruan</option>
                            </select>
                            <p v-if="formEdit.errors.kelompok" class="text-red-500 text-xs mt-1">{{ formEdit.errors.kelompok }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="formEdit.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm">
                            {{ formEdit.processing ? 'Menyimpan...' : 'Update' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>