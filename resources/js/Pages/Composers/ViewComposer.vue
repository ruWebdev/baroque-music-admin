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

import { defineEmits, ref, reactive, watch, onMounted, onBeforeUnmount } from 'vue';

import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { translitSlug } from '@/utils/translit'

import { useToast } from "vue-toastification";

import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css';

import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const quillToolbar = [
    ['bold', 'italic'],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    ['blockquote', 'link'],
    [{ 'header': [2, 3, false] }],
];

function openDeleteModal() {
    state.deleteModal.show();
}

function closeDeleteModal() {
    state.deleteModal.hide();
}

async function deleteComposer() {
    try {
        await axios.post('/composers/delete/' + props.data.composer.id);
        // редирект обратно к списку композиторов
        window.location.href = '/composers';
    } catch (e) {
        console.error(e);
    } finally {
        closeDeleteModal();
    }
}

const toast = useToast();

const props = defineProps(['data']);

const state = reactive({
    photoModal: null,
    deleteModal: null,
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
            axios.post('/upload/composer_photo/' + props.data.composer.id, form, config)
                .then(response => {
                    if (imgType.value == 'main_photo') {
                        // API may return a string or an object { main_photo }
                        if (typeof response.data === 'string') {
                            props.data.composer.main_photo = response.data;
                        } else if (response.data && response.data.main_photo) {
                            props.data.composer.main_photo = response.data.main_photo;
                        }
                    } else if (imgType.value == 'page_photo') {
                        // When uploading page_photo the API may return both images
                        if (response.data && typeof response.data === 'object') {
                            if (response.data.page_photo) {
                                props.data.composer.page_photo = response.data.page_photo;
                            }
                            if (response.data.main_photo) {
                                props.data.composer.main_photo = response.data.main_photo;
                            }
                        } else {
                            props.data.composer.page_photo = response.data;
                        }
                    } else if (imgType.value == 'additional_photo') {
                        props.data.composer.composer_photos.push(response.data);
                    }

                })
                .catch(err => {
                    console.error(err);
                });
            // Perhaps you should add the setting appropriate file format here
        }, 'image/webp');
        closePhotoModal()
    }
    // closeNewPhotoModal();
}

const mainComposerForm = ref({
    last_name: props.data.composer.last_name,
    last_name_en: props.data.composer.last_name_en,
    last_name_rod: props.data.composer.last_name_rod,
    first_name: props.data.composer.first_name,
    first_name_en: props.data.composer.first_name_en,
    first_name_rod: props.data.composer.first_name_rod,
    first_name_short: props.data.composer.first_name_short,
    first_name_short_en: props.data.composer.first_name_short_en,
    birth_date: props.data.composer.birth_date,
    death_date: props.data.composer.death_date,
    short_description: props.data.composer.short_description,
    long_description: props.data.composer.long_description ?? '',
    imslp_link: props.data.composer.imslp_link,
    video_type: props.data.composer.video_type,
    video_code: props.data.composer.video_code,
    page_alias: props.data.composer.page_alias,
})

function makePageAlias() {
    const fullName = mainComposerForm.value.last_name_en + ' ' + mainComposerForm.value.first_name_en;
    mainComposerForm.value.page_alias = translitSlug(fullName);
}

async function saveChanges() {
    try {
        const payload = JSON.parse(JSON.stringify(mainComposerForm.value));
        console.debug('Submitting composer payload', payload);
        await axios.post(
            '/composers/save_changes/' + props.data.composer.id,
            payload,
            { headers: { 'Content-Type': 'application/json' } }
        )
        toast.success("Изменения успешно сохранены");
    } catch (e) {

    }
}

onMounted(async () => {
    state.photoModal = new bootstrap.Modal(document.getElementById('photoModal'), {});
    state.deleteModal = new bootstrap.Modal(document.getElementById('deleteComposerModal'), {});
});

</script>

<template>

    <Head title="Редактирование композитора" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            <Link class="text-primary" href="/composers">Композиторы</Link> /
            {{ mainComposerForm.first_name }} {{ mainComposerForm.last_name }}
        </template>

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
            <button class="btn btn-danger" @click="openDeleteModal()">
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
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Фамилия (на русском)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.last_name"
                                                @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Имя (на русском)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.first_name"
                                                @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Имя (на русском, сокращенно)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.first_name_short">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Фамилия (на английском)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.last_name_en"
                                                @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Имя (на английском)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.first_name_en"
                                                @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Имя (на английском, сокращенно)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено"
                                                v-model="mainComposerForm.first_name_short_en">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Фамилия (на русском, родительный падеж -
                                                кого/чего)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.last_name_rod"
                                                @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Имя (на русском, родительный падеж -
                                                кого/чего)</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.first_name_rod">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Дата рождения</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.birth_date">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Дата смерти</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainComposerForm.death_date">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainComposerForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Подробная биография</label>
                                            <QuillEditor
                                                theme="snow"
                                                :toolbar="quillToolbar"
                                                v-model:content="mainComposerForm.long_description"
                                                contentType="html"
                                            />

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Ссылка на архив IMSLP</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainComposerForm.imslp_link">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Видео: Тип</label>
                                            <select class="form-select" v-model="mainComposerForm.video_type">
                                                <option value="youtube" selected>Youtube</option>
                                                <option value="vkvideo" selected>VK Video</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Видео: Код ролика</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" maxlength="100"
                                                v-model="mainComposerForm.video_code">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Адрес страницы на сайте</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    https://baroquemusic.ru/composers/
                                                </span>
                                                <input type="text" class="form-control" placeholder="Не заполнено"
                                                    autocomplete="off" v-model="mainComposerForm.page_alias"
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
                                                <h3 class="card-title">Изображение в каталоге композиторов</h3>
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

                                                <template v-if="props.data.composer.main_photo && props.data.composer.main_photo !== 'composers/no-composer-photo.jpg'">
                                                    <img
                                                        style="width: 240px;"
                                                        class="p-2"
                                                        :src="'https://baroquemusic.ru/storage/' + props.data.composer.main_photo"
                                                        alt="Главное изображение композитора"
                                                    >
                                                </template>
                                                <template v-else>
                                                    <div class="p-4 text-muted" style="min-height: 100px; display:flex; align-items:center; justify-content:center;">
                                                        изображение не загружено
                                                    </div>
                                                </template>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Изображение на странице композитора</h3>
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

                                                <template v-if="props.data.composer.page_photo && props.data.composer.page_photo !== 'composers/no-composer-photo.jpg'">
                                                    <img
                                                        style="width: 240px;"
                                                        class="p-2"
                                                        :src="'https://baroquemusic.ru/storage/' + props.data.composer.page_photo"
                                                        alt="Изображение на странице композитора"
                                                    >
                                                </template>
                                                <template v-else>
                                                    <div class="p-4 text-muted" style="min-height: 100px; display:flex; align-items:center; justify-content:center;">
                                                        изображение не загружено
                                                    </div>
                                                </template>

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

        <!-- Delete confirmation modal -->
        <div class="modal modal-blur fade" id="deleteComposerModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтвердите удаление</h5>
                        <button type="button" class="btn-close" @click="closeDeleteModal()"></button>
                    </div>
                    <div class="modal-body">
                        Вы действительно хотите удалить композитора
                        <strong>{{ mainComposerForm.first_name }} {{ mainComposerForm.last_name }}</strong>?
                        Данные будут удалены безвозвратно, включая изображения (если есть).
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeDeleteModal()">Отмена</button>
                        <button type="button" class="btn btn-danger" @click="deleteComposer()">Удалить</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>

</template>