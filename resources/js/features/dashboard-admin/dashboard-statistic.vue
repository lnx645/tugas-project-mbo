<script setup lang="ts">
import FamiconsPeopleSharp from '@/icons/FamiconsPeopleSharp.vue';
import IcBaselineClass from '@/icons/IcBaselineClass.vue';
import PhChalkboardTeacher from '@/icons/PhChalkboardTeacher.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage().props;

const stat = computed(() => page.statistic as any);
const stats = [
    {
        label: 'Guru',
        status: 'Aktif',
        count: stat.value.guru,
        icon: PhChalkboardTeacher,
        iconColor: 'text-orange-500',
    },
    {
        label: 'Siswa',
        status: 'Aktif',
        count: stat.value.siswa,
        icon: FamiconsPeopleSharp,
        iconColor: 'text-green-600',
    },
    {
        label: 'Kelas',
        status: 'Aktif',
        count: stat.value.kelas,
        icon: IcBaselineClass,
        iconColor: 'text-blue-600',
    },
];
</script>

<template>
    <div class="mt-5 flex-1 px-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div
                v-for="(stat, index) in stats"
                :key="index"
                class="rounded-xl bg-gray-50/80 p-5 shadow-sm transition-shadow duration-300 hover:shadow-md"
            >
                <div class="mb-4 flex items-center gap-2">
                    <span class="text-base font-bold text-indigo-950">
                        {{ stat.label }}
                    </span>
                    <div class="h-3 w-[1px] bg-gray-300"></div>
                    <span class="text-xs font-medium text-gray-400">
                        {{ stat.status }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-center">
                        <component :is="stat.icon" :class="[stat.iconColor, 'h-[50px] w-[50px] stroke-[1.5]']" />
                    </div>

                    <span class="text-3xl font-bold tracking-tight text-indigo-950">
                        {{ stat.count }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
