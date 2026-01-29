<script lang="ts" setup>
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { Link } from '@inertiajs/vue3';
import { Contact, Users, ShieldCheck } from 'lucide-vue-next'; // Tambah icon ShieldCheck

const breadcrumbs = [{ label: 'Dashboard' }, { label: 'User Management' }];

const menuCards = [
    {
        label: 'Guru',
        href: UserManagementController.guru().url,
        icon: Contact,
        bgColor: 'bg-orange-500',
        hoverColor: 'hover:bg-orange-600',
        shadowColor: 'shadow-orange-200',
    },
    {
        label: 'Siswa',
        href: UserManagementController.siswa().url,
        icon: Users,
        bgColor: 'bg-green-500',
        hoverColor: 'hover:bg-green-600',
        shadowColor: 'shadow-green-200',
    },
    // --- TAMBAHAN MENU ADMIN ---
    {
        label: 'Admin / Staff',
        href: UserManagementController.users().url, // Pastikan route ini sesuai web.php
        icon: ShieldCheck,
        bgColor: 'bg-blue-500',
        hoverColor: 'hover:bg-blue-600',
        shadowColor: 'shadow-blue-200',
    },
];
</script>

<template>
    <div class="mb-6">
        <Breadcrumb :items="breadcrumbs" />
    </div>

    <div class="w-full px-2 md:px-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
            <Link
                v-for="(menu, index) in menuCards"
                :key="index"
                :href="menu.href"
                class="group relative flex h-24 w-full items-center justify-center gap-4 overflow-hidden rounded-xl text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-xl md:h-32"
                :class="[menu.bgColor, menu.hoverColor, menu.shadowColor]"
            >
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white opacity-10 transition-transform duration-300 group-hover:scale-110 md:h-24 md:w-24"
                ></div>

                <component
                    :is="menu.icon"
                    class="z-10 h-8 w-8 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6 md:h-10 md:w-10"
                    stroke-width="2.5"
                />

                <span class="z-10 text-xl font-bold tracking-wide md:text-2xl">
                    {{ menu.label }}
                </span>
            </Link>
        </div>
    </div>
</template>