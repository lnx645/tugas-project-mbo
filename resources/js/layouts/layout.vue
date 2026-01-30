<script setup lang="ts">
import 'vue-select/dist/vue-select.css';
import 'vue3-json-viewer/dist/vue3-json-viewer.css';
import Base from './base.vue';
import Header from './header/header.vue';
</script>

<template>
    <Base>
        <!-- begin:header -->
        <Header />
        <!-- end:header -->
        <div class="container mx-auto mb-4 px-2 lg:px-0">
            <slot />
        </div>
    </Base>
</template>
