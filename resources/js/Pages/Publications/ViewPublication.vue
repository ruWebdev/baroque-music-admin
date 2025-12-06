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
import { translitSlug } from '@/utils/translit';

import { useToast } from "vue-toastification";

import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'


// CKEditor upload adapter removed as we use Quill now

const quillToolbar = [
    ['bold', 'italic'],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    ['blockquote', 'link', 'image'],
    [{ 'header': [2, 3, false] }],
];



const toast = useToast();

const props = defineProps(['data']);

const publicationId = props.data.publication.id;

const mainInfoForm = ref({
    page_alias: props.data.publication.page_alias,
    artist_id: props.data.publication.artist_id,
    title: props.data.publication.title,
    short_description: props.data.publication.short_description,
    long_description: props.data.publication.long_description,
    enable_page: props.data.publication.enable_page,
})

function translitTitle() {
    mainInfoForm.value.page_alias = translitSlug(mainInfoForm.value.title);
}

function checkboxToggle(field) {
    mainInfoForm.value[field] = !mainInfoForm.value[field];
}

async function saveChanges() {
    try {
        translitTitle();
        await axios.post('/publications/save_changes/' + props.data.publication.id, mainInfoForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

async function acceptModeration() {
    try {
        await axios.post('/publications/accept_moderation/' + props.data.publication.id)
        props.data.publication.moderation_status = 3;
        toast.success("Страница подтверждена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

async function denyModeration() {
    try {
        await axios.post('/publications/deny_moderation/' + props.data.publication.id)
        props.data.publication.moderation_status = 2;
        toast.success("Страница отклонена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

async function deletePublication() {
    if (!confirm('Удалить эту публикацию? Это действие необратимо.')) return;
    try {
        await axios.post('/publications/delete/' + props.data.publication.id);
        window.location.href = '/publications';
    } catch (e) {
        toast.warning('Не удалось удалить публикацию');
    }
}

</script>

<template>

    <Head title="Редактирование статьи" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            <Link class="text-primary" href="/publications">Статьи</Link> /
            Редактирование статьи
        </template>

        <template #PageTitle>
            Редактирование статьи
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
            <button class="btn btn-outline-danger d-none d-sm-inline-block me-2" @click="deletePublication()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7l16 0" />
                    <path d="M10 11l0 6" />
                    <path d="M14 11l0 6" />
                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                    <path d="M9 7v-3h6v3" />
                </svg>
                Удалить
            </button>
            <button v-if="props.data.publication.moderation_status != 3"
                class="btn btn-danger d-none d-sm-inline-block me-2" @click="denyModeration()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-off">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.042 16.045a9 9 0 0 0 -12.087 -12.087m-2.318 1.677a9 9 0 1 0 12.725 12.73" />
                    <path d="M3 3l18 18" />
                </svg>
                Отклонить
            </button>
            <button v-if="props.data.publication.moderation_status != 3"
                class="btn btn-success d-none d-sm-inline-block" @click="acceptModeration()">
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
                                            <label class="form-label">Автор статьи (если нет в списке, необходимо
                                                добавить
                                                сначала в исполнителей). <span class="text-danger">*</span></label>
                                            <select class="form-select" v-model="mainInfoForm.artist_id">
                                                <option value="">Выберите исполнителя</option>
                                                <option :value="artist.id" v-for="artist in data.artists">{{
                                                    artist.last_name
                                                    }}, {{ artist.first_name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Заголовок <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.title"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание для карточки публикации (не более
                                                100
                                                символов, без точки в конце) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainInfoForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Текст публикации <span
                                                    class="text-danger">*</span></label>
                                            <QuillEditor theme="snow" :toolbar="quillToolbar"
                                                v-model:content="mainInfoForm.long_description" contentType="html" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-media">
                                <Media :publication="props.data.publication"></Media>
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
                                            <label class="form-label">Ссылка на публикацию на сайте BaroqueMusic.ru (это
                                                значение
                                                нельзя поменять)</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    https://baroquemusic.ru/publications/
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
