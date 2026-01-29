<script lang="ts" setup>
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import Input from '@/components/input.vue';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Lock, Mail, User, AlertTriangle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    user: any;
}>();

const breadcrumbs = [
    { label: 'User Management', href: '/user-management' },
    { label: 'Admin & Staff', href: UserManagementController.users().url },
    { label: 'Edit User' }
];

// Inisialisasi form dengan data user yang ada
const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '', // Password kosong defaultnya
});

const submit = () => {
    // Menggunakan PUT method sesuai route di web.php
    form.put(UserManagementController.updateUser({
        id : props.user.id,
    }).url, {
        onSuccess: () => {
            toast.success('Data Admin/Staff berhasil diperbarui!');
        },
        onError: () => toast.error('Gagal memperbarui. Cek inputan.'),
    });
};
</script>

<template>
    <div class="min-h-screen w-full bg-gray-50/50 pb-20 font-sans">
        <div class="mb-6 flex flex-col gap-4 px-1 sm:flex-row sm:items-center sm:justify-between">
            <Breadcrumb :items="breadcrumbs" />
            <Link
                :href="UserManagementController.users().url"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
            >
                <ArrowLeft class="h-4 w-4" />
                Kembali
            </Link>
        </div>

        <div class="max-w-full p-4 mx-auto">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Edit Data Admin / Staff</h3>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <User class="h-4 w-4 text-gray-400" />
                            </div>
                            <Input 
                                v-model="form.name" 
                                type="text" 
                                class="pl-10"
                                :class="{'border-red-500 ring-red-200': form.errors.name}"
                            />
                        </div>
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Email Login</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Mail class="h-4 w-4 text-gray-400" />
                            </div>
                            <Input 
                                v-model="form.email" 
                                type="email" 
                                class="pl-10"
                                :class="{'border-red-500 ring-red-200': form.errors.email}"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <div class="border-t border-gray-100 my-4 pt-4"></div>

                    <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-100 mb-4">
                        <div class="flex gap-3">
                            <AlertTriangle class="h-5 w-5 text-yellow-600 flex-shrink-0" />
                            <div class="text-sm text-yellow-800">
                                <span class="font-semibold">Ganti Password?</span>
                                <p class="mt-1">Biarkan kolom di bawah kosong jika Anda tidak ingin mengubah password user ini.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Lock class="h-4 w-4 text-gray-400" />
                            </div>
                            <Input 
                                v-model="form.password" 
                                type="password" 
                                placeholder="Kosongkan jika tidak diubah" 
                                class="pl-10"
                                :class="{'border-red-500 ring-red-200': form.errors.password}"
                            />
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button
                            @click="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <Save class="h-4 w-4" />
                            {{ form.processing ? 'Menyimpan...' : 'Perbarui Data' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>