<script>

// Импорт разметки для проекта
import MainLayout from '@/Layouts/MainLayout.vue';

import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css';

export default {
    layout: MainLayout,
};

</script>

<script setup>

import { defineEmits, ref, reactive, watch, onMounted } from 'vue';

import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import translitRusEng from 'translit-rus-eng'

import { useToast } from "vue-toastification";

import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css';

import CKEditor from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

const editor = ClassicEditor
const ckeditor = CKEditor.component
const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
}

const toast = useToast();

const props = defineProps(['data']);

const state = reactive({
    photoModal: null
});

const cropper = ref(null);
const img = ref(null);
const imgType = ref(null);
const ratio = ref(null);

const fileField = ref(null);

function openPhotoModal(type) {
    imgType.value = type;
    if (imgType.value == 'main_photo') {
        ratio.value = {
            aspectRatio: 1 / 1
        };
    } else if (imgType.value == 'page_photo') {
        ratio.value = {
            aspectRatio: 12 / 16
        };
    } else if (imgType.value == 'additional_photo') {
        ratio.value = {
            aspectRatio: 1 / 1
        };
    }
    fileField.value.value = null;
    img.value = null;
    state.photoModal.show();
}

function closePhotoModal() {
    state.photoModal.hide();
}

function onFileChange(e) {
    const file = e.target.files[0];
    img.value = URL.createObjectURL(file);
    // cropper.value.refresh();
}

function uploadImage() {
    const { canvas } = cropper.value.getResult();
    if (canvas) {
        const form = new FormData();



        canvas.toBlob(blob => {
            form.append('type', imgType.value);
            form.append('file', blob);
            // You can use axios, superagent and other libraries instead here
            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }
            axios.post('/upload/instrument_photo/' + props.data.instrument.id, form, config)
                .then(response => {
                    if (imgType.value == 'main_photo') {
                        props.data.instrument.main_photo = response.data;
                    } else if (imgType.value == 'page_photo') {
                        props.data.instrument.page_photo = response.data;
                    } else if (imgType.value == 'additional_photo') {
                        props.data.instrument.composer_photos.push(response.data);
                    }

                })
                .catch(err => {
                    console.error(err);
                });
            // Perhaps you should add the setting appropriate file format here
        }, 'image/jpeg');
        closePhotoModal()
    }
    // closeNewPhotoModal();
}

const mainInstrumentForm = ref({
    title: props.data.instrument.title,
    title_rod: props.data.instrument.title_rod,
    short_description: props.data.instrument.short_description,
    long_description: props.data.instrument.long_description,
    page_alias: props.data.instrument.page_alias,
})

function makePageAlias() {
    const fullName = mainInstrumentForm.value.title;
    mainInstrumentForm.value.page_alias = translitRusEng(fullName, { slug: true, lowerCase: true });
}

async function saveChanges() {
    try {
        await axios.post('/instruments/save_changes/' + props.data.instrument.id, mainInstrumentForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

onMounted(async () => {
    state.photoModal = new bootstrap.Modal(document.getElementById('photoModal'), {});
});

</script>

<template>

    <Head title="Редактирование музыкального инструмента" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            <Link class="text-primary" href="/instruments">Музыкальные инструменты</Link> /
            {{ mainInstrumentForm.title }}
        </template>

        <template #PageTitle>
            Редактирование музыкального инструмента
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
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-home-1" class="nav-link active" data-bs-toggle="tab">Основная
                                    информация</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-images" class="nav-link" data-bs-toggle="tab">Изображения и видео</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-home-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Название</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInstrumentForm.title"
                                                @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Название (родительный падеж - кого/чего)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInstrumentForm.title_rod"
                                                @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainInstrumentForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Подробное описание</label>
                                            <ckeditor :editor="editor" v-model="mainInstrumentForm.long_description"
                                                :config="editorConfig">
                                            </ckeditor>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Адрес страницы на сайте</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    https://baroquemusic.ru/instruments/
                                                </span>
                                                <input type="text" class="form-control" placeholder="Не заполнено"
                                                    autocomplete="off" v-model="mainInstrumentForm.page_alias"
                                                    disabled="disabled">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-images">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Изображение в каталоге музыкальных инструментов
                                                </h3>
                                                <div class="card-actions">
                                                    <button @click="openPhotoModal('main_photo')"
                                                        class="btn btn-primary">
                                                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                        Изменить
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0 text-center">

                                                <img style="width: 240px;" class="p-2"
                                                    :src="'https://baroquemusic.ru/storage/' + props.data.instrument.main_photo">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Изображение на странице инструмента</h3>
                                                <div class="card-actions">
                                                    <button @click="openPhotoModal('page_photo')"
                                                        class="btn btn-primary">
                                                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                        Изменить
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0 text-center">

                                                <img style="width: 240px;" class="p-2"
                                                    :src="'https://baroquemusic.ru/storage/' + props.data.instrument.page_photo">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Другие изображения</h3>
                                                <div class="card-actions">
                                                    <button @click="openPhotoModal('additional_photo')"
                                                        class="btn btn-primary">
                                                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                        Добавить
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-100"
                                                    preserveAspectRatio="none" width="400" height="200"
                                                    viewBox="0 0 400 200" fill="transparent"
                                                    stroke="var(--tblr-border-color, #b8cef1)">
                                                    <line x1="0" y1="0" x2="400" y2="200"></line>
                                                    <line x1="0" y1="200" x2="400" y2="0"></line>
                                                </svg>
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

        <div ref="modal" class="modal modal-blur fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление нового изображения</h5>
                        <button type="button" class="btn-close" @click="closePhotoModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="file" @change="onFileChange" class="form-control mb-3" ref="fileField" />
                            </div>
                            <div class="col-md-12 text-center">
                                <Cropper v-if="img" ref="cropper" class="cropper" :src="img" :stencil-props="ratio" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closePhotoModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="uploadImage()">Добавить
                            изображение</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>

</template>