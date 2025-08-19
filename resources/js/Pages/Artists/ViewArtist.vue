<script>

// Импорт разметки для проекта
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    layout: MainLayout
};

</script>

<script setup>

import { ref, reactive, onMounted } from 'vue';

import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css';

import { useToast } from "vue-toastification";

import translitRusEng from 'translit-rus-eng'

import Multiselect from '@vueform/multiselect'

import CKEditor from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

const editor = ClassicEditor
const ckeditor = CKEditor.component
const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
}

const state = reactive({
    photoModal: null
});

const page = usePage();
const toast = useToast();
const props = defineProps(['data']);
const instruments = ref(null);

const cropper = ref(null);
const img = ref(null);
const imgType = ref(null);
const ratio = ref(null);

const fileField = ref(null);


async function handleTagCreate(option, select$) {

    console.log(option);

    const newInstrument = await axios.post(
        "/musical_instruments/create_from_select", {
        title: option.value
    });

    return { value: newInstrument.data.id, title: newInstrument.data.title };


}

async function asyncFind(query) {

    let result;

    try {

        result = await axios.post('/musical_instruments/get_all',
            {
                query: query
            });
        // instruments.value = result.data;
    } catch (e) {

    }

    return result.data.map((item) => {
        return { value: item.id, title: item.title }
    })

}

const mainInfoForm = ref({
    page_alias: props.data.artist.page_alias,
    last_name: props.data.artist.last_name,
    first_name: props.data.artist.first_name,
    middle_name: props.data.artist.middle_name,
    birth_date: props.data.artist.birth_date,
    birth_place: props.data.artist.birth_place,
    musical_instruments: JSON.parse(props.data.artist.musical_instruments),
    short_description: props.data.artist.short_description,
    long_description: props.data.artist.long_description,
    show_birth_place: props.data.artist.show_birth_place,
    show_birth_date: props.data.artist.show_birth_date,
    moderation_status: props.data.artist.moderation_status,
    enable_page: props.data.artist.enable_page,
    video_type: props.data.artist.video_type,
    video_code: props.data.artist.video_code,
})

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
            axios.post('/upload/artist_photo/' + props.data.artist.id, form, config)
                .then(response => {
                    if (imgType.value == 'main_photo') {
                        props.data.artist.main_photo = response.data;
                    } else if (imgType.value == 'page_photo') {
                        props.data.artist.page_photo = response.data;
                    } else if (imgType.value == 'additional_photo') {
                        props.data.artist.artist_photos.push(response.data);
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

function translitTitle() {
    let fullName = mainInfoForm.value.first_name;
    if (mainInfoForm.value.middle_name) {
        fullName = fullName + ' ' + mainInfoForm.value.middle_name
    }
    fullName = fullName + ' ' + mainInfoForm.value.last_name;
    mainInfoForm.value.page_alias = translitRusEng(fullName, { slug: true, lowerCase: true });
}

function checkboxToggle(field) {
    mainInfoForm.value[field] = !mainInfoForm.value[field];
}

async function saveChanges() {
    try {
        translitTitle();
        await axios.post('/artists/save_changes/' + props.data.artist.id, mainInfoForm.value)
        toast.success("Изменения успешно сохранены");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

async function acceptModeration() {
    try {
        await axios.post('/artist/accept_moderation/' + props.data.artist.id)
        props.data.artist.moderation_status = 3;
        toast.success("Страница подтверждена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

async function denyModeration() {
    try {
        await axios.post('/artist/deny_moderation/' + props.data.artist.id)
        props.data.artist.moderation_status = 2;
        toast.success("Страница отклонена");
    } catch (e) {
        toast.warning("Ой, что-то пошло не так... Скоро исправимся!");
    }
}

onMounted(async () => {
    state.photoModal = new bootstrap.Modal(document.getElementById('photoModal'), {});
    translitTitle()
});


</script>

<template>

    <Head title="Страница исполнителя" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/dashboard">Главная страница</Link> /
            <Link class="text-primary" href="/artists">Испонители</Link> /
            Редактирование исполнителя
        </template>

        <template #PageTitle>
            Страница исполнителя
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
            <button class="btn btn-danger d-none d-sm-inline-block me-2" @click="denyModeration()"
                v-if="mainInfoForm.moderation_status != 3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-off">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.042 16.045a9 9 0 0 0 -12.087 -12.087m-2.318 1.677a9 9 0 1 0 12.725 12.73" />
                    <path d="M3 3l18 18" />
                </svg>
                Отклонить
            </button>
            <button class="btn btn-success d-none d-sm-inline-block" @click="acceptModeration()"
                v-if="mainInfoForm.moderation_status != 3">
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
                                <a href="#tabs-info" class="nav-link active" data-bs-toggle="tab">Основная
                                    информация</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-images" class="nav-link" data-bs-toggle="tab">Изображения и видео</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-settings" class="nav-link" data-bs-toggle="tab">Управление страницей
                                    на
                                    сайте</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Фамилия <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.last_name"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Имя <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.first_name"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Отчество</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.middle_name"
                                                @keyup="translitTitle()">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">Место рождения</label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.birth_place">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">Дата рождения</label>
                                            <input type="date" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.birth_date">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Инструменты, на которых играет исполнитель <span
                                                    class="text-danger">*</span></label>

                                            <Multiselect v-model="mainInfoForm.musical_instruments" mode="tags"
                                                placeholder="Начните печатать, потом нажмите Enter или выберите из списка"
                                                :create-option="true" :searchable="true" :on-create="handleTagCreate"
                                                label="title" :options="async (query) => {
                                                    return await asyncFind(query)
                                                }" />





                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Краткая информация об исполнителе <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="example-text-input"
                                                placeholder="Не заполнено" v-model="mainInfoForm.short_description">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Подробная биографическая информация <span
                                                    class="text-danger">*</span></label>
                                            <ckeditor :editor="editor" v-model="mainInfoForm.long_description"
                                                :config="editorConfig">
                                            </ckeditor>
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
                            <div class="tab-pane" id="tabs-images">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Изображение в каталоге исполнителей</h3>
                                                <div class="card-actions">
                                                    <button @click="openPhotoModal('main_photo')"
                                                        class="btn btn-primary">
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
                                                    :src="'https://baroquemusic.ru/storage/' + props.data.artist.main_photo">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Изображение на странице исполнителя</h3>
                                                <div class="card-actions">
                                                    <button @click="openPhotoModal('page_photo')"
                                                        class="btn btn-primary">
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
                                                    :src="'https://baroquemusic.ru/storage/' + props.data.artist.page_photo">

                                            </div>
                                        </div>
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
                                                <span class="form-check-label">Показывать страницу на сайте
                                                    BaroqueMusic.ru</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Адрес страницы на сайте (это значение нельзя
                                                изменить)</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    https://baroquemusic.ru/artists/
                                                </span>
                                                <input type="text" class="form-control" placeholder="имя_пользователя"
                                                    autocomplete="off" v-model="mainInfoForm.page_alias"
                                                    disabled="disabled">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-label">Видимость информации</div>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    v-model="mainInfoForm.show_birth_date"
                                                    @click="checkboxToggle('show_birth_date')"
                                                    :checked="mainInfoForm.show_birth_date">
                                                <span class="form-check-label">Показывать день рождения и
                                                    возраст</span>
                                            </label>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    v-model="mainInfoForm.show_birth_place"
                                                    @click="checkboxToggle('show_birth_place')"
                                                    :checked="mainInfoForm.show_birth_place">
                                                <span class="form-check-label">Показывать место рождения</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title" id="offcanvasEndLabel">Возможные причины отклонения вашей страницы
                    модератором</h2>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div>
                    <p>- Указаны не полные имя или фамилия либо в полях присутствуют недопустимые символы.</p>
                    <p>- В поле "Краткая информация о себе" указана недопустимая информация, символы или термины.</p>
                    <p>- Не указана подробная биографическая информация либо в тексте присутствуют недопустимые символы
                        или
                        термины.</p>
                    <p>- Загруженные фотографии не соответствуют требованиям качества либо изображения содержат
                        недопустимый
                        контент (тексты, изображения, запрещенные законодательством РФ и т. п.)</p>
                    <hr />
                    <p>Если вы уверены, что замечаний к содержанию анкеты быть не может, пожалуйста, <a
                            href="https://t.me/bmru_admin" target="_blank">напишите нам в
                            Телеграм</a>.
                    </p>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="offcanvas">
                        Закрыть
                    </button>
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

<style src="@vueform/multiselect/themes/default.css"></style>