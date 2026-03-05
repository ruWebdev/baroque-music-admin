<script>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

export default {
    layout: MainLayout,
};
</script>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { translitSlug } from '@/utils/translit';
import { useToast } from 'vue-toastification';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps(['data']);
const toast = useToast();

const state = reactive({
    photoModal: null,
    deleteModal: null,
});

const cropper = ref(null);
const img = ref(null);
const imgType = ref(null);
const ratio = ref(null);
const fileField = ref(null);

const quillToolbar = [
    ['bold', 'italic'],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    ['blockquote', 'link'],
    [{ 'header': [2, 3, false] }],
];

function openPhotoModal(type) {
    imgType.value = type;
    if (imgType.value === 'main_photo') {
        ratio.value = { aspectRatio: 1 / 1 };
    } else if (imgType.value === 'page_photo') {
        ratio.value = { aspectRatio: 12 / 16 };
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
}

function uploadImage() {
    const { canvas } = cropper.value.getResult();
    if (canvas) {
        const form = new FormData();
        canvas.toBlob(blob => {
            form.append('type', imgType.value);
            form.append('file', blob);
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            };
            axios.post('/upload/opera_photo/' + props.data.opera.id, form, config)
                .then(response => {
                    if (imgType.value === 'main_photo') {
                        if (typeof response.data === 'string') {
                            props.data.opera.main_photo = response.data;
                        } else if (response.data && response.data.main_photo) {
                            props.data.opera.main_photo = response.data.main_photo;
                        }
                    } else if (imgType.value === 'page_photo') {
                        if (response.data && typeof response.data === 'object') {
                            if (response.data.page_photo) {
                                props.data.opera.page_photo = response.data.page_photo;
                            }
                            if (response.data.main_photo) {
                                props.data.opera.main_photo = response.data.main_photo;
                            }
                        } else {
                            props.data.opera.page_photo = response.data;
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }, 'image/webp', 0.9);
        closePhotoModal();
    }
}

const mainOperaForm = ref({
    title: props.data.opera.title,
    title_en: props.data.opera.title_en,
    year_created: props.data.opera.year_created,
    composer_id: props.data.opera.composer_id,
    short_description: props.data.opera.short_description,
    long_description: props.data.opera.long_description ?? '',
    vk_video_link: props.data.opera.vk_video_link,
    imslp_link: props.data.opera.imslp_link,
    page_alias: props.data.opera.page_alias,
    enable_page: props.data.opera.enable_page ?? true,
});

function makePageAlias() {
    mainOperaForm.value.page_alias = translitSlug(mainOperaForm.value.title_en || mainOperaForm.value.title);
}

async function saveChanges() {
    try {
        const payload = JSON.parse(JSON.stringify(mainOperaForm.value));
        await axios.post(
            '/operas/save_changes/' + props.data.opera.id,
            payload,
            { headers: { 'Content-Type': 'application/json' } }
        );
        toast.success('Изменения успешно сохранены');
    } catch (e) {
        console.error(e);
    }
}

function openDeleteModal() {
    state.deleteModal.show();
}

function closeDeleteModal() {
    state.deleteModal.hide();
}

async function deleteOpera() {
    try {
        await axios.post('/operas/delete/' + props.data.opera.id);
        window.location.href = '/operas';
    } catch (e) {
        console.error(e);
    } finally {
        closeDeleteModal();
    }
}

onMounted(() => {
    state.photoModal = new bootstrap.Modal(document.getElementById('photoModal'), {});
    state.deleteModal = new bootstrap.Modal(document.getElementById('deleteOperaModal'), {});
});
</script>

<template>
    <Head title="Редактирование оперы" />

    <ContentLayout>
        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            <Link class="text-primary" href="/operas">Оперы</Link> /
            {{ mainOperaForm.title }}
        </template>

        <template #PageTitle>
            Редактирование оперы
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
                                <a href="#tabs-home-1" class="nav-link active" data-bs-toggle="tab">Основная информация</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-images" class="nav-link" data-bs-toggle="tab">Изображения</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-home-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Название (на русском)</label>
                                            <input type="text" class="form-control" placeholder="Не заполнено" v-model="mainOperaForm.title" @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Название (на английском)</label>
                                            <input type="text" class="form-control" placeholder="Не заполнено" v-model="mainOperaForm.title_en" @keyup="makePageAlias()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Год создания</label>
                                            <input type="text" class="form-control" placeholder="Не заполнено" v-model="mainOperaForm.year_created">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Композитор</label>
                                            <select class="form-select" v-model="mainOperaForm.composer_id">
                                                <option :value="null">Не выбран</option>
                                                <option v-for="composer in props.data.composers" :key="composer.id" :value="composer.id">
                                                    {{ composer.last_name }}, {{ composer.first_name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткое описание</label>
                                            <input type="text" class="form-control" placeholder="Не заполнено" maxlength="160" v-model="mainOperaForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Длинное описание</label>
                                            <QuillEditor
                                                theme="snow"
                                                :toolbar="quillToolbar"
                                                v-model:content="mainOperaForm.long_description"
                                                contentType="html"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ссылка на VK видео</label>
                                            <input type="text" class="form-control" placeholder="Не заполнено" v-model="mainOperaForm.vk_video_link">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ссылка на IMSLP</label>
                                            <input type="text" class="form-control" placeholder="Не заполнено" v-model="mainOperaForm.imslp_link">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Адрес страницы на сайте</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">https://baroquemusic.ru/operas/</span>
                                                <input type="text" class="form-control" placeholder="Не заполнено" autocomplete="off" v-model="mainOperaForm.page_alias" disabled="disabled">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Показывать на сайте</label>
                                            <select class="form-select" v-model="mainOperaForm.enable_page">
                                                <option :value="true">Показывать</option>
                                                <option :value="false">Не показывать</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-images">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Изображение в списке опер</h3>
                                                <div class="card-actions">
                                                    <button @click="openPhotoModal('main_photo')" class="btn btn-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                        Изменить
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0 text-center">
                                                <template v-if="props.data.opera.main_photo && props.data.opera.main_photo !== 'operas/no-opera-image.jpg'">
                                                    <img
                                                        style="width: 240px;"
                                                        class="p-2"
                                                        :src="'https://baroquemusic.ru/storage/' + props.data.opera.main_photo"
                                                        alt="Главное изображение оперы"
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
                                                <h3 class="card-title">Изображение на странице оперы</h3>
                                                <div class="card-actions">
                                                    <button @click="openPhotoModal('page_photo')" class="btn btn-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                        Изменить
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0 text-center">
                                                <template v-if="props.data.opera.page_photo && props.data.opera.page_photo !== 'operas/no-opera-image.jpg'">
                                                    <img
                                                        style="width: 240px;"
                                                        class="p-2"
                                                        :src="'https://baroquemusic.ru/storage/' + props.data.opera.page_photo"
                                                        alt="Изображение на странице оперы"
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
                        <button type="button" class="btn btn-primary" @click="uploadImage()">Добавить изображение</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-blur fade" id="deleteOperaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтвердите удаление</h5>
                        <button type="button" class="btn-close" @click="closeDeleteModal()"></button>
                    </div>
                    <div class="modal-body">
                        Вы действительно хотите удалить оперу
                        <strong>{{ mainOperaForm.title }}</strong>?
                        Данные будут удалены безвозвратно, включая изображения (если есть).
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeDeleteModal()">Отмена</button>
                        <button type="button" class="btn btn-danger" @click="deleteOpera()">Удалить</button>
                    </div>
                </div>
            </div>
        </div>
    </ContentLayout>
</template>
