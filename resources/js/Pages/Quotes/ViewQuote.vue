<script>

// Импорт разметки для проекта
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    layout: MainLayout
};

</script>

<script setup>

import { defineEmits, ref, reactive, watch, onMounted } from 'vue';

import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

import CKEditor from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

import { useToast } from "vue-toastification";

const toast = useToast();

const editor = ClassicEditor
const ckeditor = CKEditor.component
const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
}

const props = defineProps(['data']);



const mainQuoteForm = ref({
    author: props.data.quote.author,
    title: props.data.quote.title,
    long_description: props.data.quote.long_description,
})

async function saveChanges() {
    try {
        await axios.post('/quotes/save_changes/' + props.data.quote.id, mainQuoteForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

</script>

<template>

    <Head title="Редактирование композитора" />

    <ContentLayout>

        <template #PageTitle>
            Редактирование композитора
        </template>

        <template #RightButtons>
            <button class="btn btn-success me-2" @click="saveChanges()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                    <path d="M14 4l0 4l-6 0l0 -4" />
                </svg>
                Сохранить изменения
            </button>
        </template>

        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Автор</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено" v-model="mainQuoteForm.author">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Имя</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено" v-model="mainQuoteForm.title">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Текст цитаты</label>
                                    <ckeditor :editor="editor" v-model="mainQuoteForm.long_description"
                                        :config="editorConfig">
                                    </ckeditor>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </ContentLayout>

</template>