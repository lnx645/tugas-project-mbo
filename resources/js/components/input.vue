<script setup lang="ts">
import { cn } from '@/lib/utils';
import { defineEmits, defineProps } from 'vue';

interface Props {
    class?: string | object;
    modelValue?: any;
    id?: string;
    type?: string;
    placeholder?: string;
    error?: boolean;
}

const { class: className = '', modelValue = '', id = '', type = 'text', placeholder = '', error = false } = defineProps<Props>();

const emit = defineEmits(['update:modelValue']);

const updateValue = (e: Event) => {
    emit('update:modelValue', (e.target as HTMLInputElement).value);
};
</script>

<template>
    <input
        :id="id"
        :type="type"
        :placeholder="placeholder"
        :value="modelValue"
        @input="updateValue"
        :class="cn('input', className, error && 'bg-red-50 ring-2 ring-red-500')"
    />
</template>
<style scoped>
@reference "tailwindcss";

.label-style {
    @apply mb-1 block text-sm font-bold text-slate-700;
}

.input {
    @apply mt-1 block w-full rounded-xl border border-gray-200 bg-white p-2.5 text-sm transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10;
}

.btn-primary-modern {
    @apply rounded-xl bg-blue-600 px-8 py-3 font-bold text-white shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 hover:bg-blue-700 active:scale-95 disabled:translate-y-0 disabled:bg-gray-400 disabled:shadow-none;
}

.error-text {
    @apply mt-1 text-xs font-medium text-red-500;
}

/* Kustomisasi input type number agar bersih */
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    @apply opacity-100;
}
</style>
