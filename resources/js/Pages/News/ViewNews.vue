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
    page_alias: props.data.news.page_alias,
    title: props.data.news.title,
    short_description: props.data.news.short_description,
    long_description: props.data.news.long_description,
    enable_page: props.data.news.enable_page,
    external_link: props.data.news.external_link,
    video_type: props.data.news.video_type,
    video_code: props.data.news.video_code,
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
        await axios.post('/news/save_changes/' + props.data.news.id, mainInfoForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

async function acceptModeration() {
    try {
        await axios.post('/news/accept_moderation/' + props.data.news.id)
        props.data.news.moderation_status = 3;
        toast.success("Страница подтверждена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

async function denyModeration() {
    try {
        await axios.post('/news/deny_moderation/' + props.data.news.id)
        props.data.news.moderation_status = 2;
        toast.success("Страница отклонена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

</script>

<template>

    <Head title="Редактирование новости" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            <Link class="text-primary" href="/news">Новости</Link> /
            Редактирование новости
        </template>

        <template #PageTitle>
            Редактирование новости
        </template>

        <template #RightButtons>
            <button class="btn btn-primary me-2 d-none d-sm-inline-block" @click="saveChanges()">
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
            <button class="btn btn-danger d-none d-sm-inline-block me-2" @click="denyModeration()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-off">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.042 16.045a9 9 0 0 0 -12.087 -12.087m-2.318 1.677a9 9 0 1 0 12.725 12.73" />
                    <path d="M3 3l18 18" />
                </svg>
                Отклонить
            </button>
            <button class="btn btn-success d-none d-sm-inline-block" @click="acceptModeration()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-checkbox">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 11l3 3l8 -8" />
                    <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" />
                </svg>
                Подтвердить
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
                                <a href="#tabs-media" class="nav-link" data-bs-toggle="tab">Изображения и видео</a>
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
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Заголовок новости <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.title"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание для карточки новости (не более
                                                100
                                                символов, без точки в конце) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainInfoForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Текст новости <span
                                                    class="text-danger">*</span></label>
                                            <ckeditor :editor="editor" v-model="mainInfoForm.long_description"
                                                :config="editorConfig">
                                            </ckeditor>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Внешняя ссылка на событие (описание, онлайн
                                                продажа
                                                билетов, и т. п.)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.external_link">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-media">
                                <Media :news="props.data.news"></Media>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <hr />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Тип видео плеера</label>
                                        <select class="form-select" v-model="mainInfoForm.video_type">
                                            <option value="none">Выключить видео</option>
                                            <option value="youtube">YouTube</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Код</label>
                                        <input type="text" class="form-control" name="example-text-input"
                                            placeholder="Не заполнено" maxlength="100"
                                            v-model="mainInfoForm.video_code">
                                    </div>
                                </div>
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
                                                <span class="form-check-label">Показывать новость на сайте
                                                    BaroqueMusic.ru</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Ссылка на новость на сайте BaroqueMusic.ru (это
                                                значение
                                                нельзя поменять)</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    https://baroquemusic.ru/events/
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