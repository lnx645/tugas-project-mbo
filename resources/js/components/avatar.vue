<template>
    <div
        v-if="!avatar_uri && fallback"
        class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-neutral-800 text-xs font-semibold text-neutral-100 ring-2 ring-neutral-400"
    >
        <div>
            {{ fallback.charAt(0) }}
        </div>
    </div>
    <div class="flex overflow-hidden ring-2 cursor-pointer ring-neutral-400 rounded-full h-8 w-8 " v-else>
        <img :src="`storage/${avatar_uri}`" alt="" srcset="" />
    </div>
</template>
<script setup lang="ts">
defineProps<{
    avatar_uri?: string;
    fallback?: string;
}>();
</script>
