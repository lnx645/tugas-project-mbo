<script lang="ts" setup>
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import Paging from '@/components/paging.vue'; // Pastikan path paging sesuai
import { Link, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2, Shield, Mail } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
//@ts-ignore
import Avatar from 'vue3-avatar';
import { debounce } from 'lodash';
import { toast } from 'vue-sonner';
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';

const props = defineProps<{
    users: any;
    filters: { search?: string };
}>();

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'User Management', href: '/user-management' },
    { label: 'Admin & Staff' }
];

// State Search
const search = ref(props.filters?.search || '');

// Logic Search Debounce
watch(
    search,
    debounce((value: string) => {
        router.get(
            UserManagementController.users().url, 
            { search: value }, 
            { preserveState: true, preserveScroll: true, replace: true }
        );
    }, 300)
);

// Logic Hapus
const handleDelete = (id: number, name: string) => {
    if (confirm(`Apakah Anda yakin ingin menghapus user "${name}"?`)) {
        router.delete(UserManagementController.destroyUser({id:id}).url, {
            onSuccess: () => toast.success('User berhasil dihapus'),
            onError: () => toast.error('Gagal menghapus user'),
        });
    }
};
</script>

<template>
    <div class="relative min-h-screen w-full bg-gray-50/50 pb-12 font-sans">
        <div class="mb-8 flex flex-col justify-between gap-4 px-1 sm:flex-row sm:items-end">
            <Breadcrumb :items="breadcrumbs" />
            <div class="flex flex-col gap-2 sm:flex-row">
                <Link
                    :href="UserManagementController.createUser().url" 
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700"
                >
                    <Plus class="h-4 w-4" />
                    <span>Tambah Admin/Staff</span>
                </Link>
            </div>
        </div>

        <div class="rounded-xl mx-3 border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <div class="relative max-w-md">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        class="block w-full rounded-lg border-0 bg-gray-50 py-2.5 pl-10 text-sm ring-1 ring-gray-200 ring-inset focus:ring-2 focus:ring-blue-600"
                        placeholder="Cari Nama atau Email..."
                    />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50/50 text-gray-500">
                        <tr>
                            <th class="px-6 py-4 font-medium">User Profile</th>
                            <th class="px-6 py-4 font-medium">Email</th>
                            <th class="px-6 py-4 font-medium">Role Info</th>
                            <th class="px-6 py-4 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <tr v-for="user in users.data" :key="user.id" class="group transition-colors hover:bg-blue-50/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative h-10 w-10 flex-shrink-0 overflow-hidden rounded-full shadow-sm ring-2 ring-white">
                                        <Avatar :name="user.name" class="h-full w-full object-cover" />
                                    </div>
                                    <div class="font-semibold text-gray-900">{{ user.name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center gap-2">
                                    <Mail class="h-3.5 w-3.5 text-gray-400" />
                                    {{ user.email }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    <Shield class="h-3 w-3" />
                                    Admin / Staff
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1 opacity-60 transition-opacity group-hover:opacity-100">
                                    <button 
                                        @click="router.visit(UserManagementController.editUser({id:user.id}).url)" 
                                        class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600"
                                        title="Edit User"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </button>
                                    
                                    <button 
                                        @click="handleDelete(user.id, user.name)"
                                        class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600"
                                        title="Hapus User"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada data user ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                <Paging :links="users.links" />
            </div>
        </div>
    </div>
</template>