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
import Program from './Partials/Program.vue';
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

const toast = useToast();

const props = defineProps(['data']);

const mainInfoForm = ref({
    title: props.data.event.title,
    age_restrictions: props.data.event.age_restrictions,
    short_description: props.data.event.short_description,
    long_description: props.data.event.long_description,
    event_date: props.data.event.event_date,
    event_time: props.data.event.event_time,
    ticket_price_from: props.data.event.ticket_price_from,
    ticket_price_to: props.data.event.ticket_price_to,
    place: props.data.event.place,
    page_alias: props.data.event.page_alias,
    external_link: props.data.event.external_link,
    enable_page: props.data.event.enable_page,
    sold_out: props.data.event.sold_out,
    featured: props.data.event.featured,
})

function translitTitle() {
    mainInfoForm.value.page_alias = translitRusEng(mainInfoForm.value.title + ' ' + mainInfoForm.value.event_date, { slug: true, lowerCase: true });
}

function checkboxToggle(field) {
    mainInfoForm.value[field] = !mainInfoForm.value[field];
}

async function saveChanges() {
    try {
        translitTitle();
        await axios.post('/events/save_changes/' + props.data.event.id, mainInfoForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

async function checkForModeration() {
    if (
        mainInfoForm.value.title &&
        mainInfoForm.value.age_restrictions &&
        mainInfoForm.value.short_description &&
        mainInfoForm.value.long_description &&
        mainInfoForm.value.event_date &&
        mainInfoForm.value.event_time &&
        mainInfoForm.value.ticket_price_from
    ) {
        sendToModeration();
    } else {
        toast.warning("Вы не заполнили все необходимые поля...");
    }
}

async function acceptModeration() {
    try {
        await axios.post('/events/accept_moderation/' + props.data.event.id)
        props.data.event.moderation_status = 3;
        toast.success("Страница подтверждена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

async function denyModeration() {
    try {
        await axios.post('/events/deny_moderation/' + props.data.event.id)
        props.data.event.moderation_status = 2;
        toast.success("Страница отклонена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

</script>

<template>

    <Head title="Редактирование события" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            <Link class="text-primary" href="/events">События</Link> /
            Редактирование события
        </template>

        <template #PageTitle>
            Редактирование события
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
                                <a href="#tabs-program" class="nav-link" data-bs-toggle="tab">Программа</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-participants" class="nav-link" data-bs-toggle="tab">Участники</a>
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
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Название события <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.title"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Возрастное ограничение <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" v-model="mainInfoForm.age_restrictions">
                                                <option>0+</option>
                                                <option>6+</option>
                                                <option>12+</option>
                                                <option>16+</option>
                                                <option>18+</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание для карточки события (не более
                                                100
                                                символов, без точки в конце) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainInfoForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Подробное описание события <span
                                                    class="text-danger">*</span></label>
                                            <ckeditor :editor="editor" v-model="mainInfoForm.long_description"
                                                :config="editorConfig">
                                            </ckeditor>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Дата проведения события <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.event_date"
                                                @change="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Время проведения события <span
                                                    class="text-danger">*</span></label>
                                            <input type="time" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.event_time">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">Стоимость билетов ОТ <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.ticket_price_from">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">Стоимость билетов ДО</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.ticket_price_to">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Место проведения события (полное название)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.place">
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
                                <Media :event="props.data.event"></Media>
                            </div>
                            <div class="tab-pane" id="tabs-program">
                                <Program :event="props.data.event"></Program>
                            </div>
                            <div class="tab-pane" id="tabs-participants">
                                <Participants :event="props.data.event"></Participants>
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
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    v-model="mainInfoForm.sold_out" @click="checkboxToggle('sold_out')"
                                                    :checked="mainInfoForm.sold_out">
                                                <span class="form-check-label">Указать, что все билеты на мероприятие
                                                    проданы</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    v-model="mainInfoForm.featured" @click="checkboxToggle('featured')"
                                                    :checked="mainInfoForm.featured">
                                                <span class="form-check-label">Поместить в избранные мероприятия</span>
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