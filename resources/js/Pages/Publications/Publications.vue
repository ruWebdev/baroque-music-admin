<script>

// Импорт разметки для проекта
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    layout: MainLayout
};

</script>

<script setup>

import { ref, reactive, computed, onBeforeMount, onMounted } from 'vue';

import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

import ModerationStatusBadge from '@/Components/ModerationStatusBadge.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps(
    ["data"]
);

const page = usePage()

const state = reactive({
    newPublicationModal: null
});

const newPublicationForm = ref({
    title: null
});

function openNewPublicationModal() {
    newPublicationForm.value = {
        title: null
    }
    state.newPublicationModal.show();
}

function closeNewPublicationModal() {
    state.newPublicationModal.hide();
}

async function createNewPublication() {
    try {
        const result = await axios.post('/publications/create', {
            data: newPublicationForm.value,
        });
        closeNewPublicationModal();
        props.data.publications.push(result.data);
    } catch (e) {

    }
}

onMounted(async () => {
    state.newPublicationModal = new bootstrap.Modal(document.getElementById('newPublicationModal'), {});
});


</script>

<template>

    <Head title="Статьи" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            Статьи
        </template>

        <template #PageTitle>
            Статьи
        </template>

        <template #RightButtons>
            <a href="#" class="btn btn-primary" @click="openNewPublicationModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Добавить статью
            </a>
        </template>
        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список статей, нуждающихся в модерации</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Заголовок</th>
                                        <th>Статус</th>
                                        <th>Модерация</th>
                                        <th>Просмотры</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="publication in props.data.publications_moderation">
                                        <td>{{ publication.title }}</td>
                                        <td class="text-muted">
                                            <StatusBadge :enabled="publication.enable_page"
                                                :status="publication.moderation_status">
                                            </StatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            <ModerationStatusBadge :status="publication.moderation_status">
                                            </ModerationStatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            {{ publication.page_views }}
                                        </td>
                                        <td>
                                            <Link :href="'/publications/view/' + publication.id">Редактировать</Link>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список одобренных статей или только что добавленных статей</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Заголовок</th>
                                        <th>Статус</th>
                                        <th>Модерация</th>
                                        <th>Просмотры</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="publication in props.data.publications">
                                        <td>{{ publication.title }}</td>
                                        <td class="text-muted">
                                            <StatusBadge :enabled="publication.enable_page"
                                                :status="publication.moderation_status">
                                            </StatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            <ModerationStatusBadge :status="publication.moderation_status">
                                            </ModerationStatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            {{ publication.page_views }}
                                        </td>
                                        <td>
                                            <Link :href="'/publications/view/' + publication.id">Редактировать</Link>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal modal-blur fade" id="newPublicationModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление статьи</h5>
                        <button type="button" class="btn-close" @click="closeNewPublicationModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Заголовок</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newPublicationForm.title">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p><small>Заголовок будет отображаться на страницах сайта</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeNewPublicationModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="createNewPublication()">Создать
                            статью</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>
</template>
