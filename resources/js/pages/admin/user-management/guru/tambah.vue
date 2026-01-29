<script setup lang="ts">
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import Input from '@/components/input.vue';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

// 1. Terima data Matpel dari Controller
const props = defineProps<{
    matpels: Array<{ kode: string; nama: string }>;
}>();

const breadcrumbs = [{ label: 'Dashboard', href: '/dashboard' }, { label: 'Manajemen Guru', href: '/users/guru' }, { label: 'Tambah Data' }];

const form = useForm({
    name: '',
    nip: '',
    jenis_kelamin: '',
    status: 'Aktif',
    gelar_depan: '',
    gelar_belakang: '',
    matpel_kode: '', // 2. Tambahkan field untuk mapel
    foto: null as File | null,
});

const previewUrl = ref<string | null>(null);

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        
        if (file.size > 2 * 1024 * 1024) {
            toast.error('Ukuran foto terlalu besar! Maksimal 2MB.');
            target.value = ''; 
            return;
        }

        form.foto = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.submit(UserManagementController.simpanGuru(), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset();
            previewUrl.value = null;
            const flashMessage = (page.props.flash as any)?.success || 'Data Guru berhasil disimpan!';
            toast.success(flashMessage);
        },
        onError: (errors) => {
            toast.error('Gagal menyimpan data. Silakan periksa inputan.');
            const firstError = Object.values(errors)[0];
            if (firstError) {
                toast.warning(`Error: ${firstError}`);
            }
        },
    });
};

const previewNama = computed(() => {
    const depan = form.gelar_depan ? `${form.gelar_depan} ` : '';
    const nama = form.name || 'Nama Guru';
    const belakang = form.gelar_belakang ? `, ${form.gelar_belakang}` : '';
    return `${depan}${nama}${belakang}`;
});
</script>

<template>
    <div class="min-h-screen w-full bg-gray-50/50 pb-20 font-sans">
        <div class="mb-4 flex flex-col gap-4 px-1 sm:flex-row sm:items-center sm:justify-between">
            <Breadcrumb :items="breadcrumbs" />

            <div class="flex gap-3">
                <Link
                    :href="UserManagementController.guru().url"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-gray-200"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition-all hover:bg-indigo-700 hover:shadow-md focus:ring-2 focus:ring-indigo-500 disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    <Save class="h-4 w-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                </button>
            </div>
        </div>

        <div class="grid p-3 grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-base font-semibold text-gray-900">Foto Profil</h3>
                    <div class="flex flex-col items-center justify-center gap-4">
                        <div 
                            class="relative flex h-40 w-40 items-center justify-center overflow-hidden rounded-full border-2 border-dashed bg-gray-50 text-gray-400"
                            :class="form.errors.foto ? 'border-red-500 bg-red-50' : 'border-gray-300'"
                        >
                            <img v-if="previewUrl" :src="previewUrl" class="h-full w-full object-cover" alt="Preview Foto" />
                            <User v-else class="h-16 w-16 opacity-50" />

                            <input
                                type="file"
                                @input="handleFileUpload"
                                class="absolute inset-0 cursor-pointer opacity-0"
                                accept="image/*"
                            />
                        </div>

                        <div class="text-center">
                            <p class="text-sm font-medium text-indigo-600">Klik untuk upload foto</p>
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maks 2MB.</p>
                            <p v-if="form.errors.foto" class="mt-2 text-xs font-medium text-red-600 animate-pulse">
                                {{ form.errors.foto }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-indigo-600 to-indigo-800 p-6 text-white shadow-md">
                    <p class="mb-4 text-xs font-medium tracking-wider text-indigo-200 uppercase">Preview Tampilan</p>
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-lg font-bold backdrop-blur-sm">
                            {{ form.name ? form.name.charAt(0).toUpperCase() : 'G' }}
                        </div>
                        <div>
                            <h4 class="w-48 truncate text-sm font-bold">{{ previewNama }}</h4>
                            <p class="text-xs text-indigo-200">{{ form.nip || 'NIP Belum diisi' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Informasi Pribadi</h3>
                        <p class="text-sm text-gray-500">Lengkapi data diri guru dengan benar.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 p-6 md:grid-cols-2">
                        <div class="md:col-span-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Gelar Depan</label>
                            <Input v-model="form.gelar_depan" type="text" placeholder="Contoh: Dr., Ir." />
                            <p v-if="form.errors.gelar_depan" class="mt-1 text-xs text-red-500">{{ form.errors.gelar_depan }}</p>
                        </div>

                        <div class="md:col-span-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Gelar Belakang</label>
                            <Input v-model="form.gelar_belakang" type="text" placeholder="Contoh: S.Pd, M.Kom" />
                            <p v-if="form.errors.gelar_belakang" class="mt-1 text-xs text-red-500">{{ form.errors.gelar_belakang }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700"> Nama Lengkap <span class="text-red-500">*</span> </label>
                            <Input 
                                v-model="form.name" 
                                type="text" 
                                placeholder="Masukkan nama lengkap tanpa gelar" 
                                :class="{'border-red-500 ring-red-200': form.errors.name}"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <div class="md:col-span-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700"> NIP / NUPTK <span class="text-red-500">*</span> </label>
                            <Input 
                                v-model="form.nip" 
                                type="text" 
                                placeholder="Nomor Induk Pegawai" 
                                :class="{'border-red-500 ring-red-200': form.errors.nip}"
                            />
                            <p v-if="form.errors.nip" class="mt-1 text-xs text-red-500">{{ form.errors.nip }}</p>
                        </div>

                        <div class="md:col-span-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700"> Jenis Kelamin <span class="text-red-500">*</span> </label>
                            <select
                                v-model="form.jenis_kelamin"
                                class="w-full rounded-lg border bg-neutral-50 px-3 py-2 text-sm transition-all outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                :class="form.errors.jenis_kelamin ? 'border-red-500 ring-red-200' : 'border-neutral-200'"
                            >
                                <option value="" disabled>Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <p v-if="form.errors.jenis_kelamin" class="mt-1 text-xs text-red-500">{{ form.errors.jenis_kelamin }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700"> 
                                Keahlian Utama / Mata Pelajaran 
                                <span class="text-gray-400 text-xs font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <select
                                    v-model="form.matpel_kode"
                                    class="w-full appearance-none rounded-lg border bg-neutral-50 px-3 py-2 text-sm transition-all outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                    :class="form.errors.matpel_kode ? 'border-red-500 ring-red-200' : 'border-neutral-200'"
                                >
                                    <option value="">-- Tidak Memiliki Mapel Khusus --</option>
                                    <option v-for="mapel in props.matpels" :key="mapel.kode" :value="mapel.kode">
                                        {{ mapel.kode }} - {{ mapel.nama }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                            <p v-if="form.errors.matpel_kode" class="mt-1 text-xs text-red-500">{{ form.errors.matpel_kode }}</p>
                        </div>
                        <div class="col-span-full my-2 border-t border-gray-100"></div>

                        <div class="md:col-span-2">
                            <label class="mb-3 block text-sm font-medium text-gray-700">Status Kepegawaian</label>
                            <div class="flex gap-4">
                                <label
                                    class="relative flex w-full cursor-pointer rounded-lg border bg-white p-4 shadow-sm transition-all focus:outline-none md:w-1/2"
                                    :class="form.status === 'Aktif' ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-gray-300 hover:border-gray-400'"
                                >
                                    <input type="radio" name="status" value="Aktif" v-model="form.status" class="sr-only" />
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Aktif Mengajar</span>
                                            <span class="mt-1 flex items-center text-xs text-gray-500">Guru aktif dan memiliki jadwal.</span>
                                        </span>
                                    </span>
                                    <span class="h-5 w-5 text-indigo-600" v-if="form.status === 'Aktif'">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><circle cx="12" cy="12" r="12" fill="currentColor" fill-opacity="0.2"></circle><path d="M7 13l3 3 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    </span>
                                </label>

                                <label
                                    class="relative flex w-full cursor-pointer rounded-lg border bg-white p-4 shadow-sm transition-all focus:outline-none md:w-1/2"
                                    :class="form.status === 'Nonaktif' ? 'border-red-500 bg-red-50 ring-1 ring-red-500' : 'border-gray-300 hover:border-gray-400'"
                                >
                                    <input type="radio" name="status" value="Nonaktif" v-model="form.status" class="sr-only" />
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Non-Aktif / Cuti</span>
                                            <span class="mt-1 flex items-center text-xs text-gray-500">Tidak memiliki akses ke sistem.</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>