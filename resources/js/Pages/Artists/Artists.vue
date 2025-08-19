<script>

// Импорт разметки для проекта
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

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
    newNewsModal: null
});

const newNewsForm = ref({
    last_name: null,
    first_name: null,
    middle_name: null,
});

function openNewNewsModal() {
    newNewsForm.value = {
        last_name: null,
        first_name: null,
        middle_name: null,
    }
    state.newNewsModal.show();
}

function closeNewNewsModal() {
    state.newNewsModal.hide();
}

async function createNewNews() {
    try {
        const result = await axios.post('/artists/create', {
            data: newNewsForm.value,
        });
        closeNewNewsModal();
        props.data.artists.push(result.data);

        props.data.artists.sort(function (a, b) {
            var textA = a.title.toUpperCase();
            var textB = b.title.toUpperCase();
            return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
        });

    } catch (e) {

    }
}

onMounted(async () => {
    state.newNewsModal = new bootstrap.Modal(document.getElementById('newNewsModal'), {});
});


</script>

<template>

    <Head title="Исполнители" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/dashboard">Главная страница</Link> /
            Исполнители
        </template>

        <template #PageTitle>
            Исполнители
        </template>

        <template #RightButtons>
            <a href="#" class="btn btn-primary" @click="openNewNewsModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Добавить исполнителя
            </a>
        </template>
        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список страниц исполнителей, нуждающихся в модерации</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Имя, Фамилия</th>
                                        <th>Подтвержденный пользователь</th>
                                        <th>Статус</th>
                                        <th>Модерация</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="artist in props.data.artist_moderation">
                                        <td>{{ artist.last_name }}, {{ artist.first_name }}</td>
                                        <td>
                                            <template v-if="artist.user_id == null"><span
                                                    class="badge bg-red-lt">Нет</span></template>
                                            <template v-else><span class="badge bg-green-lt">Да</span></template>
                                        </td>
                                        <td>
                                            <StatusBadge :enabled="artist.enable_page"
                                                :status="artist.moderation_status">
                                            </StatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            <ModerationStatusBadge :status="artist.moderation_status">
                                            </ModerationStatusBadge>
                                        </td>
                                        <td>
                                            <Link :href="'/artists/view/' + artist.id">Просмотр</Link>
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
                        <h3 class="card-title">Список одобренных страниц исполнителей или только что созданных страниц
                        </h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Имя, Фамилия</th>
                                        <th>Подтвержденный пользователь</th>
                                        <th>Статус</th>
                                        <th>Модерация</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="artist in props.data.artists">
                                        <td>{{ artist.last_name }}, {{ artist.first_name }}</td>
                                        <td>
                                            <template v-if="artist.user_id == null"><span
                                                    class="badge bg-red-lt">Нет</span></template>
                                            <template v-else><span class="badge bg-green-lt">Да</span></template>
                                        </td>
                                        <td>
                                            <StatusBadge :enabled="artist.enable_page"
                                                :status="artist.moderation_status">
                                            </StatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            <ModerationStatusBadge :status="artist.moderation_status">
                                            </ModerationStatusBadge>
                                        </td>
                                        <td>
                                            <Link :href="'/artists/view/' + artist.id">Просмотр</Link>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal modal-blur fade" id="newNewsModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление нового исполнителя</h5>
                        <button type="button" class="btn-close" @click="closeNewNewsModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Фамилия</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newNewsForm.last_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Имя</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newNewsForm.first_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Отчество</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newNewsForm.middle_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeNewNewsModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="createNewNews()">Создать
                            исполнителя</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>
</template>
