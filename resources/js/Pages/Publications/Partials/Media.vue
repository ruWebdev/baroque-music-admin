<script setup>

import { ref, reactive, onMounted } from 'vue';

import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css';

const props = defineProps({
    publication: Object
})

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
            aspectRatio: 9 / 14
        };
    } else if (imgType.value == 'page_photo') {
        ratio.value = {
            aspectRatio: 16 / 9
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
            axios.post('/upload/publication_photo/' + props.publication.id, form, config)
                .then(response => {
                    if (imgType.value == 'main_photo') {
                        props.publication.main_photo = response.data;
                    } else if (imgType.value == 'page_photo') {
                        props.publication.page_photo = response.data;
                    } else if (imgType.value == 'additional_photo') {
                        props.publication.artist_photos.push(response.data);
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

onMounted(async () => {
    state.photoModal = new bootstrap.Modal(document.getElementById('photoModal'), {});
});

</script>

<template>

    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Изображение в списке статей</h3>
                    <div class="card-actions">
                        <button :disabled="props.publication.moderation_status == 1"
                            @click="openPhotoModal('main_photo')" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
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
                        :src="'https://baroquemusic.ru/storage/' + props.publication.main_photo">

                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Изображение на странице статьи</h3>
                    <div class="card-actions">
                        <button :disabled="props.publication.moderation_status == 1"
                            @click="openPhotoModal('page_photo')" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
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
                        :src="'https://baroquemusic.ru/storage/' + props.publication.page_photo">

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

</template>