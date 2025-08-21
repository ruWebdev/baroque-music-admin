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

import Media from './Partials/Media.vue';

import translitRusEng from 'translit-rus-eng'

import { useToast } from "vue-toastification";

import CKEditor from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

const editor = ClassicEditor
const ckeditor = CKEditor.component
const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
}

const toast = useToast();

const props = defineProps(['data']);

const mainInfoForm = ref({
    page_alias: props.data.dictionary.page_alias,
    title: props.data.dictionary.title,
    origin_language: props.data.dictionary.origin_language,
    transcription: props.data.dictionary.transcription,
    short_description: props.data.dictionary.short_description,
    long_description: props.data.dictionary.long_description,
    external_link: props.data.dictionary.external_link,
    enable_page: props.data.dictionary.enable_page,
})

function translitTitle() {
    mainInfoForm.value.page_alias = translitRusEng(mainInfoForm.value.title, { slug: true, lowerCase: true });
}

function checkboxToggle(field) {
    mainInfoForm.value[field] = !mainInfoForm.value[field];
}

async function saveChanges() {
    try {
        translitTitle();
        await axios.post('/dictionary/save_changes/' + props.data.dictionary.id, mainInfoForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

async function checkForModeration() {
    if (
        mainInfoForm.value.title &&
        mainInfoForm.value.short_description &&
        mainInfoForm.value.long_description
    ) {
        sendToModeration();
    } else {
        toast.warning("Вы не заполнили все необходимые поля...");
    }
}

async function sendToModeration() {
    try {
        await axios.post('/event/request_moderation/' + props.data.dictionary.id)
        props.data.dictionary.moderation_status = 1;
        toast.success("Страница отправлена на модерацию");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

</script>

<template>

    <Head title="Редактирование музыкального термина" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            <Link class="text-primary" href="/dictionary">Словарь музыкальных терминов</Link> /
            Редактирование
        </template>

        <template #PageTitle>
            Редактирование музыкального термина
        </template>

        <template #RightButtons>
            <button :disabled="props.data.dictionary.moderation_status == 1" class="btn btn-success me-2"
                @click="saveChanges()">
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
            <div class="col-md-12 col-lg-12 mt-0">

                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-home" class="nav-link active" data-bs-toggle="tab">Основная
                                    информация</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-settings" class="nav-link" data-bs-toggle="tab">Управление</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-home">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Название музыкального термина <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.title"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Обозначение языка БЕЗ СКОБОК, например "Ит" <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.origin_language"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Транскрипция БЕЗ СКОБОК <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.transcription"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание, если нет длинного описания для
                                                карточки
                                                в каталоге (не более
                                                150
                                                символов, без точки в конце) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="150"
                                                v-model="mainInfoForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Подробное описание, если текст длинный <span
                                                    class="text-danger">*</span></label>
                                            <ckeditor :editor="editor" v-model="mainInfoForm.long_description"
                                                :config="editorConfig">
                                            </ckeditor>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Внешняя ссылка (если есть)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.external_link">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-media">
                                <Media :dictionary="props.data.dictionary"></Media>
                            </div>
                            <div class="tab-pane" id="tabs-settings">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    v-model="mainInfoForm.enable_page"
                                                    @click="checkboxToggle('enable_page')"
                                                    :checked="mainInfoForm.enable_page">
                                                <span class="form-check-label">Показывать страницу события на сайте
                                                    BaroqueMusic.ru</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Ссылка на событие на сайте BaroqueMusic.ru (это
                                                значение
                                                нельзя поменять)</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    https://baroquemusic.ru/encyclopedia/dictionary/
                                                </span>
                                                <input type="text" class="form-control" placeholder="Не заполнено"
                                                    autocomplete="off" v-model="mainInfoForm.page_alias"
                                                    disabled="disabled">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </ContentLayout>

</template>