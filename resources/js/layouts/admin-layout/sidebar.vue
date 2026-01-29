<script setup lang="ts">
import { create } from '@/actions/App/Http/Controllers/Admin/AkademikController';
import CetakLaporanController from '@/actions/App/Http/Controllers/Admin/CetakLaporanController';
import HomeController from '@/actions/App/Http/Controllers/Admin/HomeController';
import ManagementKelasController from '@/actions/App/Http/Controllers/Admin/ManagementKelasController';
// Asumsi Anda sudah generate MatpelController via ziggy/inertia helper
import MatpelController from '@/actions/App/Http/Controllers/Admin/MatpelController'; 
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import { usePage } from '@inertiajs/vue3';
import { BookOpen, FileText, LayoutDashboard, MonitorPlay, Users, Library } from 'lucide-vue-next'; // Tambah Icon Library
import { computed } from 'vue';
import SidebarMenu from './sidebar-menu.vue';

const page = usePage();
const currentPath = computed(() => page.url);

const menus = [
    { label: 'Dashboard', href: HomeController.index().url, icon: LayoutDashboard },
    { label: 'Management User', href: UserManagementController.index().url, icon: Users },
    { label: 'Mata Pelajaran', href: MatpelController.index().url, icon: BookOpen }, 
    { label: 'Manajemen Kelas', href: ManagementKelasController.index().url, icon: MonitorPlay },
    { label: 'Manajemen Akademik', href: create().url, icon: Library },
    { label: 'Cetak Laporan', href: CetakLaporanController().url, icon: FileText },
];
</script>

<template>
    <aside
        class="sticky top-[50px] flex max-h-[calc(100vh-50px)] min-h-[calc(100vh-50px)] flex-col border-r border-neutral-200 bg-[#F8F9FA] transition-all"
    >
        <div class="flex items-center px-6 pt-4 font-bold text-neutral-800">SMK PASUNDAN 2</div>
        <div class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            <SidebarMenu v-for="(menu, index) in menus" :key="index" :href="menu.href" :active="currentPath.startsWith(menu.href)">
                <template #icon>
                    <component :is="menu.icon" :size="20" />
                </template>
                {{ menu.label }}
            </SidebarMenu>
        </div>
        <div class="border-t border-neutral-200 p-4 text-xs text-neutral-400">© 2025 SMK PASUNDAN 2 BDG</div>
    </aside>
</template>