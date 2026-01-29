<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

// Menerima props 'links' yang merupakan array dari Laravel Pagination
defineProps<{
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}>();
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap items-center justify-center -mb-1 gap-1">
            <template v-for="(link, key) in links" :key="key">
                
                <div 
                    v-if="link.url === null"
                    class="mr-1 mb-1 px-3 py-2 text-sm leading-4 text-gray-400 border border-transparent rounded-lg"
                    v-html="link.label"
                />

                <Link
                    v-else
                    class="mr-1 mb-1 px-3 py-2 text-sm leading-4 border rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                    :class="{ 
                        'bg-indigo-600 text-white border-indigo-600 font-medium': link.active, 
                        'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active 
                    }"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>