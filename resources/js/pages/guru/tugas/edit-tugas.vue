<template>
    <div class="mx-auto max-w-7xl pb-20">
        <div class="mb-4 flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <PageTitle title="Edit Tugas" :subtitle="`Perbarui detail tugas: ${form.judul}`" />

            <div class="flex items-center gap-3">
                <Button @click="goBack" variant="secondary" class="border border-neutral-300 bg-white text-neutral-600 shadow-sm hover:bg-neutral-50">
                    Batal
                </Button>
                <Button @click="simpan" :disabled="form.processing" class="shadow-md shadow-blue-500/20">
                    <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        Simpan Perubahan
                    </span>
                    <span v-else>Simpan Perubahan</span>
                </Button>
            </div>
        </div>

        <form @submit.prevent="simpan" class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-neutral-100 bg-neutral-50 px-5 py-3">
                        <h3 class="text-sm font-semibold text-neutral-700">Target & Mata Pelajaran</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Mata Pelajaran</label>
                            <VueSelect
                                placeholder="Pilih Mapel..."
                                :options="$page.props.matpels"
                                label="nama"
                                :reduce="(item: any) => item.kode_matpel"
                                v-model="form.matpel"
                                class="v-select-custom"
                                :class="{ 'rounded-lg border-red-500 ring-1 ring-red-500': form.errors.matpel }"
                            />
                            <p v-if="form.errors.matpel" class="mt-1 text-xs font-medium text-red-500">{{ form.errors.matpel }}</p>
                        </div>

                        <hr class="border-dashed border-neutral-200" />

                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Tugaskan Kepada</label>
                            <div class="mb-3 grid grid-cols-2 rounded-lg bg-neutral-100 p-1">
                                <button
                                    type="button"
                                    @click="form.receiver_type = 'class_id'"
                                    class="rounded-md py-1.5 text-sm font-medium transition-all duration-200"
                                    :class="
                                        form.receiver_type === 'class_id'
                                            ? 'bg-white text-blue-600 shadow-sm'
                                            : 'text-neutral-500 hover:text-neutral-700'
                                    "
                                >
                                    Per Kelas
                                </button>
                                <button
                                    type="button"
                                    @click="form.receiver_type = 'siswa_id'"
                                    class="rounded-md py-1.5 text-sm font-medium transition-all duration-200"
                                    :class="
                                        form.receiver_type === 'siswa_id'
                                            ? 'bg-white text-blue-600 shadow-sm'
                                            : 'text-neutral-500 hover:text-neutral-700'
                                    "
                                >
                                    Per Siswa
                                </button>
                            </div>

                            <div v-if="form.receiver_type == 'class_id'">
                                <VueSelect
                                    placeholder="Pilih Kelas..."
                                    :options="options_receiver"
                                    label="nama_kelas"
                                    :reduce="(item: Kelas) => item.id_kelas"
                                    :multiple="true"
                                    v-model="form.receiver_type_id"
                                    class="v-select-custom"
                                />
                            </div>
                            <div v-else>
                                <VueSelect
                                    @search="onSearchSiswa"
                                    placeholder="Ketik nama siswa..."
                                    :options="options_receiver"
                                    :reduce="(item: any) => item?.user?.id"
                                    :multiple="true"
                                    v-model="form.receiver_type_id"
                                    class="v-select-custom"
                                >
                                    <template #no-options>Ketik untuk mencari...</template>
                                    <template #option="option">
                                        <div class="flex flex-col py-1">
                                            <span class="text-sm font-bold text-neutral-800">{{ option.user?.name }}</span>
                                            <span class="text-xs text-neutral-500">{{ option.kelas?.nama }} • {{ option?.nis }}</span>
                                        </div>
                                    </template>
                                    <template #selected-option="option">
                                        {{ option.user?.name }}
                                    </template>
                                </VueSelect>
                            </div>
                            <p v-if="form.errors.receiver_type_id" class="mt-1 text-xs font-medium text-red-500">
                                {{ form.errors.receiver_type_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-100 bg-neutral-50 px-5 py-3">
                        <h3 class="text-sm font-semibold text-neutral-700">Pengumpulan</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Batas Waktu (Deadline)</label>
                            <VueDatePicker v-model="form.deadline" auto-apply :enable-time-picker="true" input-class-name="dp-custom-input" />
                            <p v-if="form.errors.deadline" class="mt-1 text-xs font-medium text-red-500">{{ form.errors.deadline }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Format Jawaban</label>
                            <VueSelect
                                class="v-select-custom"
                                v-model="form.mode_pengumpulan"
                                placeholder="Pilih Format..."
                                :options="typePengumpulan"
                            />
                            <p v-if="form.errors.mode_pengumpulan" class="mt-1 text-xs font-medium text-red-500">
                                {{ form.errors.mode_pengumpulan }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4 lg:col-span-2">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="mb-6">
                        <input
                            type="text"
                            v-model="form.judul"
                            placeholder="Tulis Judul Tugas di sini..."
                            class="w-full border-none p-0 text-2xl font-bold text-neutral-800 placeholder-neutral-300 transition-all focus:ring-0 focus:outline-none"
                        />
                        <div class="mt-2 h-1 w-20 rounded-full bg-blue-500"></div>
                        <p v-if="form.errors.judul" class="mt-2 text-sm font-medium text-red-500">{{ form.errors.judul }}</p>
                    </div>

                    <div class="relative">
                        <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Instruksi / Deskripsi</label>
                        <div class="ck-content-wrapper prose max-w-none" :class="{ 'rounded-lg ring-1 ring-red-500': form.errors.deskripsi }">
                            <Ckeditor :editor="ClassicEditor" v-model="form.deskripsi" :config="editorConfig" />
                        </div>
                        <p v-if="form.errors.deskripsi" class="mt-1 text-xs font-medium text-red-500">{{ form.errors.deskripsi }}</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { updateTugas } from '@/actions/App/Http/Controllers/Guru/TugasController';
import Button from '@/components/button.vue';
import PageTitle from '@/layouts/page-title.vue';
import { getKelasByMatpel, getSiswa } from '@/routes/guru';
import { Kelas } from '@/types';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from 'axios';
import {
    Autoformat,
    AutoImage,
    Autosave,
    BlockQuote,
    Bold,
    CKBox,
    CKBoxImageEdit,
    ClassicEditor,
    CloudServices,
    Code,
    CodeBlock,
    Emoji,
    Essentials,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    Heading,
    Highlight,
    HorizontalLine,
    ImageBlock,
    ImageCaption,
    ImageInline,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Indent,
    IndentBlock,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    MediaEmbed,
    Mention,
    Paragraph,
    PasteFromOffice,
    PictureEditing,
    Strikethrough,
    Subscript,
    Superscript,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';
import { debounce } from 'lodash';
import { computed, onMounted, ref, watch } from 'vue';
import VueSelect from 'vue-select';
import { toast } from 'vue-sonner';

// PROPS & STATE
const props = usePage().props as any;
const typePengumpulan = ['file', 'text', 'foto'];
const options_receiver = ref([]); // Opsi untuk dropdown (Daftar kelas atau Daftar siswa)

const form = useForm({
    matpel: props.tugas.matpel_kode,
    judul: props.tugas.title,
    deskripsi: props.tugas.content,
    mode_pengumpulan: props.tugas.mode_pengumpulan,
    deadline: props.tugas.deadline,
    receiver_type: props.tugas.receiver_type,
    // receiver_type_id harus di-init dengan data yang ada
    receiver_type_id: props.tugas.receiver_type_id ?? [],
});

// WATCHERS
// 1. Jika Mapel berubah, ambil daftar kelas baru
watch(
    () => form.matpel,
    async (newVal, oldVal) => {
        if (!newVal) return;
        // Mencegah reset saat inisialisasi awal (jika mapel sama)
        if (oldVal !== undefined) {
            const res = await axios.post(getKelasByMatpel().url, { matpel_kode: newVal });
            options_receiver.value = res.data ?? [];
            if (form.receiver_type === 'class_id') {
                form.receiver_type_id = []; // Reset pilihan kelas jika mapel ganti
            }
        }
    },
);

// 2. Jika Tipe Penerima berubah (Kelas <-> Siswa), reset pilihan & list
watch(
    () => form.receiver_type,
    (newVal, oldVal) => {
        if (oldVal !== undefined) {
            // Jangan reset saat mounting awal
            options_receiver.value = [];
            form.receiver_type_id = [];

            // Jika pindah ke Class ID, load kelas mapel tsb
            if (newVal === 'class_id' && form.matpel) {
                axios.post(getKelasByMatpel().url, { matpel_kode: form.matpel }).then((res) => (options_receiver.value = res.data ?? []));
            }
        }
    },
);

// 3. Error Handling
watch(
    () => [form.hasErrors, form.processing],
    () => {
        if (form.hasErrors && !(form.errors as any).success && !form.processing) {
            toast.error('Gagal! Mohon periksa kembali inputan Anda.');
        } else if ((form.errors as any).success) {
            toast.success((form.errors as any).success);
        }
    },
);

// CKEDITOR CONFIG
const editorConfig = computed(() => ({
    licenseKey: 'GPL',
    height: 500,
    placeholder: 'Tulis instruksi tugas...',
    plugins: [
        Autoformat,
        AutoImage,
        Autosave,
        BlockQuote,
        Bold,
        CKBox,
        CKBoxImageEdit,
        CloudServices,
        Emoji,
        Essentials,
        Subscript,
        Heading,
        ImageBlock,
        ImageCaption,
        ImageInline,
        ImageInsert,
        ImageInsertViaUrl,
        ImageResize,
        ImageStyle,
        ImageTextAlternative,
        ImageToolbar,
        ImageUpload,
        Indent,
        IndentBlock,
        Italic,
        Link,
        LinkImage,
        List,
        ListProperties,
        MediaEmbed,
        Mention,
        Paragraph,
        PasteFromOffice,
        PictureEditing,
        Table,
        TableCaption,
        TableCellProperties,
        TableColumnResize,
        TableProperties,
        TableToolbar,
        TextTransformation,
        TodoList,
        Underline,
        FontBackgroundColor,
        FontColor,
        FontFamily,
        FontSize,
        Highlight,
        HorizontalLine,
        CodeBlock,
        Strikethrough,
        Code,
        Superscript,
    ],
    toolbar: [
        'heading',
        '|',
        'bold',
        'italic',
        'underline',
        'strikethrough',
        'code',
        '|',
        'bulletedList',
        'numberedList',
        'todoList',
        '|',
        'fontSize',
        'fontColor',
        'fontBackgroundColor',
        '|',
        'link',
        'insertImage',
        'insertTable',
        'blockQuote',
        'codeBlock',
        '|',
        'undo',
        'redo',
    ],
}));

// SEARCH SISWA (Server-side searching)
const onSearchSiswa = debounce(async (search: string, loading: (status: boolean) => void) => {
    if (!search) {
        loading(false);
        return;
    }
    loading(true);
    try {
        const { data } = await axios.get(getSiswa().url, { params: { keywords: search } });
        options_receiver.value = data;
    } catch (error) {
        console.error('Gagal mencari siswa:', error);
    } finally {
        loading(false);
    }
}, 500);

// ACTIONS
function goBack() {
    window.history.back(); // Atau gunakan router.visit ke halaman index
}

async function simpan() {
    // Menggunakan form.post atau form.put sesuai kebutuhan Inertia
    // Asumsi: updateTugas() mengembalikan URL string
    form.put(updateTugas({ id: props.tugas.tugasID }).url, {
        onSuccess: () => {
            // Redirect atau logic lain ditangani oleh respon server/inertia
        },
    });
}

// ON MOUNTED: PRE-POPULATE DATA
onMounted(async () => {
    // 1. Jika tipe penerima Kelas, ambil daftar semua kelas untuk mapel ini
    // agar dropdown VueSelect menampilkan nama kelas yang benar (bukan cuma ID)
    if (form.receiver_type === 'class_id' && form.matpel) {
        const res = await axios.post(getKelasByMatpel().url, { matpel_kode: form.matpel });
        options_receiver.value = res.data;
    }

    // 2. Jika tipe penerima Siswa, ambil data siswa detail berdasarkan ID yang tersimpan
    // agar dropdown VueSelect menampilkan nama siswa yang benar
    if (form.receiver_type === 'siswa_id' && form.receiver_type_id.length > 0) {
        try {
            // Asumsi endpoint getSiswa bisa menerima array ID untuk fetch by IDs
            // Jika props.tugas.receiver_type_id sudah berupa object lengkap (id, user.name), step ini tidak perlu.
            // Kode di bawah untuk jaga-jaga jika isinya cuma [1, 2, 3]
            const { data } = await axios.get(getSiswa().url, {
                params: { ids: form.receiver_type_id },
            });
            options_receiver.value = data;
        } catch (e) {
            console.error('Gagal load data siswa selected', e);
        }
    }
});
</script>

<style>
@reference "tailwindcss";
/* CUSTOM STYLING (Sama dengan halaman Tambah Tugas) */

/* Vue Select */
.v-select-custom .vs__dropdown-toggle {
    @apply min-h-[42px] rounded-lg border border-neutral-300 bg-white px-1 py-1 shadow-sm transition-all;
}
.v-select-custom .vs__dropdown-toggle:focus-within {
    @apply border-blue-500 ring-2 ring-blue-500/20;
}
.v-select-custom .vs__search::placeholder {
    @apply text-sm text-neutral-400;
}
.v-select-custom .vs__dropdown-menu {
    @apply z-50 mt-1 rounded-lg border border-neutral-200 text-sm shadow-xl;
}
.v-select-custom .vs__selected {
    @apply rounded border border-blue-100 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700;
}

/* Datepicker */
.dp-custom-input {
    @apply h-[42px] rounded-lg border-neutral-300 font-sans text-sm text-neutral-700 shadow-sm;
}
.dp-custom-input:focus {
    @apply border-blue-500 ring-2 ring-blue-500/20;
}

/* CKEditor */
.ck-content-wrapper .ck.ck-editor__main > .ck-editor__editable {
    @apply min-h-[400px] rounded-b-lg border-neutral-300;
}
.ck-content-wrapper .ck.ck-toolbar {
    @apply rounded-t-lg border-neutral-300 bg-neutral-50;
}
.ck-content-wrapper .ck.ck-editor__main > .ck-editor__editable:focus {
    @apply border-blue-500 ring-1 ring-blue-500;
}
</style>
