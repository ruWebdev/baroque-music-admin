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

import ModerationStatusAlert from '@/Components/ModerationStatusAlert.vue';

import Media from './Partials/Media.vue';
import Participants from './Partials/Participants.vue';

import translitRusEng from 'translit-rus-eng'

import { useToast } from "vue-toastification";

import CKEditor from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

const editor = ClassicEditor
const ckeditor = CKEditor.component
const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
}


const props = defineProps(['data']);

const toast = useToast();

const state = reactive({
    newPhotoModal: null
});

const imageUrl = ref(null);

const img = ref(null);

const mainInfoForm = ref({
    title: props.data.band.title,
    short_description: props.data.band.short_description,
    long_description: props.data.band.long_description,
    country: props.data.band.country,
    city: props.data.band.city,
    page_alias: props.data.band.page_alias,
    external_link: props.data.band.external_link,
    enable_page: props.data.band.enable_page,
    moderation_status: props.data.band.moderation_status,
    video_type: props.data.band.video_type,
    video_code: props.data.band.video_code,
})

function openNewPhotoModal() {
    img.value = null;
    state.newPhotoModal.show();
}

function closeNewPhotoModal() {
    state.newPhotoModal.hide();
}


function translitTitle() {
    mainInfoForm.value.page_alias = translitRusEng(mainInfoForm.value.title, { slug: true, lowerCase: true });
}

function onFileChange(e) {
    const file = e.target.files[0];
    img.value = URL.createObjectURL(file);
}

function change({ coordinates, canvas }) {
    console.log(coordinates, canvas)
}

function checkboxToggle(field) {
    mainInfoForm.value[field] = !mainInfoForm.value[field];
}


async function saveChanges() {
    try {
        translitTitle();
        await axios.post('/bands/save_changes/' + props.data.band.id, mainInfoForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

async function checkForModeration() {
    if (mainInfoForm.value.title && mainInfoForm.value.short_description && mainInfoForm.value.long_description) {
        sendToModeration();
    } else {
        toast.warning("Вы не заполнили все необходимые поля...");
    }
}

async function acceptModeration() {
    try {
        await axios.post('/bands/accept_moderation/' + props.data.band.id)
        props.data.band.moderation_status = 3;
        toast.success("Страница подтверждена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

async function denyModeration() {
    try {
        await axios.post('/bands/deny_moderation/' + props.data.band.id)
        props.data.band.moderation_status = 2;
        toast.success("Страница отклонена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

onMounted(async () => {
    translitTitle()
});

</script>

<template>

    <Head title="Редактирование коллектива" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/dashboard">Главная страница</Link> /
            <Link class="text-primary" href="/bands">Коллективы</Link> /
            {{ props.data.band.title }}
        </template>

        <template #PageTitle>
            {{ props.data.band.title }}: редактирование коллектива
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
            <button v-if="mainInfoForm.moderation_status != 3" class="btn btn-danger d-none d-sm-inline-block me-2"
                @click="denyModeration()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-off">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.042 16.045a9 9 0 0 0 -12.087 -12.087m-2.318 1.677a9 9 0 1 0 12.725 12.73" />
                    <path d="M3 3l18 18" />
                </svg>
                Отклонить
            </button>
            <button v-if="mainInfoForm.moderation_status != 3" class="btn btn-success d-none d-sm-inline-block"
                @click="acceptModeration()">
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
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-info" class="nav-link active" data-bs-toggle="tab">Основная
                                    информация</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-media" class="nav-link" data-bs-toggle="tab">Изображения и видео</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-participants" class="nav-link" data-bs-toggle="tab">Участники</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-settings" class="nav-link" data-bs-toggle="tab">Настройки</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-delete" class="nav-link text-danger" data-bs-toggle="tab">Удаление
                                    коллектива</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-info">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Название коллектива <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.title"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание для карточки коллектива (не более
                                                100
                                                символов, без точки в конце) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainInfoForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Подробное описание коллектива <span
                                                    class="text-danger">*</span></label>
                                            <ckeditor :editor="editor" v-model="mainInfoForm.long_description"
                                                :config="editorConfig">
                                            </ckeditor>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Страна</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.country">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Город</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.city">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Видео: Тип</label>
                                            <select class="form-select" v-model="mainInfoForm.video_type">
                                                <option value="youtube" selected>Youtube</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Видео: Код ролика</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainInfoForm.video_code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-media">
                                <Media :band="props.data.band"></Media>
                            </div>
                            <div class="tab-pane" id="tabs-participants">
                                <Participants :band="props.data.band"></Participants>
                            </div>
                            <div class="tab-pane" id="tabs-settings">

                                <div class="row">
                                    <div class="col-md-12" v-if="props.data.band.moderation_status == 3">
                                        <div class="mb-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    v-model="mainInfoForm.enable_page"
                                                    @click="checkboxToggle('enable_page')"
                                                    :checked="mainInfoForm.enable_page">
                                                <span class="form-check-label">Показывать страницу коллектива на сайте
                                                    BaroqueMusic.ru</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Ссылка на событие на сайте BaroqueMusic.ru</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    https://baroquemusic.ru/bands/
                                                </span>
                                                <input type="text" class="form-control" placeholder="Не заполнено"
                                                    autocomplete="off" v-model="mainInfoForm.page_alias">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Внешняя ссылка (сайт, сообщество в соц. сетях,
                                                телеграм
                                                канал)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.external_link">
                                            <p><small>Обратите внимание, модерация страницы не будет пройдена, если
                                                    ссылка ведет
                                                    на запрещенные в РФ ресурсы</small></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane text-center" id="tabs-delete">

                                <p class="mt-4 mb-4">Удаление коллектива в настоящее время не предусмотрено.<br />Если
                                    вы не
                                    хотите
                                    показывать
                                    страницу
                                    коллектива на сайте, вы можете выбрать соответствующий пункт в настройках.</p>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </ContentLayout>

</template>