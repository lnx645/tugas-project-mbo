<template>
    <div class="min-h-screen py-3 font-serif text-[#333]">
        <div class="flex items-center justify-between border-b-4 border-[#ffcc00] bg-[#003366] px-4 py-2 text-white">
            <div class="flex items-center gap-3">
                <div class="rounded-sm bg-white p-1">
                    <div class="flex h-8 w-8 items-center justify-center bg-[#003366] text-xs font-bold text-white italic">UBK</div>
                </div>
                <div>
                    <h1 class="text-sm leading-none font-bold tracking-wider uppercase">Panel Manajemen Quiz</h1>
                    <span class="font-mono text-[10px] text-[#ffcc00] uppercase">Admin System v2.0</span>
                </div>
            </div>
            <div class="text-right font-mono text-xs italic">Total: {{ category.length }} Kategori</div>
        </div>

        <div class="p-6">
            <div
                class="mb-4 flex flex-col justify-between gap-4 border border-gray-400 bg-[#f0f0f0] p-4 shadow-[2px_2px_0px_rgba(0,0,0,0.1)] md:flex-row md:items-center"
            >
                <div>
                    <h2 class="text-sm font-bold tracking-tighter text-[#003366] uppercase">Daftar Kategori / Paket Soal</h2>
                    <p class="font-sans text-[11px] text-gray-600 italic">Silakan kelola paket soal yang tersedia di bawah ini.</p>
                </div>
                <button
                    @click="openModal()"
                    class="inline-flex items-center justify-center border-b-2 border-black bg-[#006633] px-4 py-2 text-xs font-bold tracking-wide text-white uppercase transition-all hover:bg-black"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    TAMBAH KATEGORI BARU
                </button>
            </div>

            <div class="overflow-hidden border border-gray-400 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-400 bg-[#003366] font-mono text-white uppercase">
                                <th class="w-12 border-r border-gray-400 px-4 py-3 text-center">No</th>
                                <th class="border-r border-gray-400 px-4 py-3">Informasi Kategori</th>
                                <th class="w-32 border-r border-gray-400 px-4 py-3 text-center">Kapasitas Soal</th>
                                <th class="w-56 px-4 py-3 text-center">Kontrol Panel</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-300">
                            <tr v-for="(item, index) in category" :key="item.id" class="transition-colors hover:bg-[#fff9c4]">
                                <td class="border-r border-gray-400 px-4 py-3 text-center font-bold text-gray-500">
                                    {{ index + 1 }}
                                </td>
                                <td class="border-r border-gray-400 px-4 py-3">
                                    <h4 class="text-sm leading-tight font-bold text-[#003366] uppercase">
                                        {{ item.nama }}
                                    </h4>
                                    <span class="font-mono text-[10px] text-gray-500 italic">KODE: #PKG-{{ item.id }}</span>
                                </td>
                                <td class="border-r border-gray-400 px-4 py-3 text-center">
                                    <span
                                        v-if="item.bank_soals_count"
                                        class="border border-green-600 bg-green-50 px-2 py-0.5 text-[10px] font-bold text-green-700"
                                    >
                                        {{ item.bank_soals_count }} ITEM
                                    </span>
                                    <span v-else class="text-[10px] font-bold text-red-600 italic">EMPTY</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link
                                            :href="showBankSoal({ id: item.id })"
                                            class="border border-gray-500 bg-[#003366] px-2 py-1 text-[10px] font-bold text-white shadow-[1px_1px_0px_#000] hover:bg-black"
                                        >
                                            KELOLA SOAL
                                        </Link>
                                        <button
                                            @click="openModal(item)"
                                            class="border border-gray-500 bg-[#ffcc00] px-2 py-1 text-[10px] font-bold text-black shadow-[1px_1px_0px_#000] hover:bg-black hover:text-white"
                                        >
                                            EDIT
                                        </button>
                                        <button
                                            class="border border-gray-500 bg-red-700 px-2 py-1 text-[10px] font-bold text-white shadow-[1px_1px_0px_#000] hover:bg-black"
                                        >
                                            HAPUS
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="category.length === 0">
                                <td colspan="4" class="bg-gray-50 px-6 py-12 text-center font-bold text-gray-400 italic">
                                    -- BELUM ADA DATA KATEGORI --
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 border border-gray-400 bg-[#fefefe] p-2 text-[10px] text-gray-600">
                <p><strong>Info:</strong> Gunakan menu "Kelola Soal" untuk menambah pertanyaan ke dalam paket ujian.</p>
            </div>
        </div>
    </div>

    <Transition name="fade">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>
            <div
                class="relative w-full max-w-md transform overflow-hidden border-2 border-[#003366] bg-white shadow-[8px_8px_0px_rgba(0,0,0,0.2)] transition-all"
            >
                <div class="flex items-center justify-between bg-[#003366] p-3 text-white">
                    <h3 class="text-xs font-bold tracking-widest uppercase">
                        {{ isEdit ? 'Form Update Kategori' : 'Form Entry Kategori Baru' }}
                    </h3>
                    <button @click="closeModal" class="hover:text-[#ffcc00]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-4 p-6">
                    <div>
                        <label class="mb-2 block text-[10px] font-bold text-[#003366] uppercase">Nama Kategori / Judul Paket</label>
                        <input
                            v-model="form.nama"
                            type="text"
                            class="w-full border border-gray-400 px-3 py-2 text-sm font-bold outline-none placeholder:font-normal placeholder:italic focus:bg-yellow-50"
                            placeholder="Ketik nama kategori di sini..."
                            required
                        />
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 border-b-2 border-black bg-[#006633] py-2 text-xs font-bold text-white uppercase transition-all hover:bg-black disabled:opacity-50"
                        >
                            {{ isEdit ? 'SIMPAN PERUBAHAN' : 'TAMBAHKAN SEKARANG' }}
                        </button>
                        <button
                            type="button"
                            @click="closeModal"
                            class="border-b-2 border-gray-400 bg-gray-200 px-4 py-2 text-xs font-bold text-gray-700 uppercase"
                        >
                            BATAL
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Hilangkan font modern untuk kesan jadul */
h1,
h2,
h3,
h4,
button,
th,
td {
    font-family: 'Arial', sans-serif;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

input,
select {
    border-radius: 0px !important;
}

button {
    border-radius: 0px !important;
}
</style>

<script setup lang="ts">
//@ts-expect-error
//@ts-nocheck
// (Logic Script Anda tetap sama, tidak ada yang berubah)
import { addNewCategory, editQuizCategory, showBankSoal } from '@/routes';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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

const openModal = (item: any | null = null) => {
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
        form.submit(
            editQuizCategory({
                id: selectedId.value as any,
            }),
            {
                onSuccess: () => {
                    closeModal();
                    toast.success('Kategori berhasil di update');
                },
            },
        );
    } else {
        form.post(addNewCategory().url, {
            onSuccess: () => {
                closeModal();
                toast.success('Kategori baru berhasil ditambahkan');
            },
        });
    }
};
</script>
