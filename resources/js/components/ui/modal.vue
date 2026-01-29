<script setup lang="ts">
import { onUnmounted } from 'vue';
import { Teleport, Transition, watch } from 'vue';
const props = defineProps<{isOpen:boolean}>()
const emit = defineEmits(['close'])

watch(()=>props.isOpen,(val)=>{
    if (val) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
})
onUnmounted(() => {
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="props.isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                @click.self="emit('close')">

                <div
                    class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-gray-200 transform transition-all">

                    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Detail Penugasan</h3>
                            <p class="mt-1 text-xs text-gray-500">Daftar mata pelajaran yang diampu.</p>
                        </div>
                        <button @click="emit('close')"
                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-200 hover:text-gray-500 transition-colors">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="max-h-[460px] overflow-y-auto">
                        <slot/>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button @click="emit('close')"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>