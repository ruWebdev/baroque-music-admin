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
    title: null
});

function openNewNewsModal() {
    newNewsForm.value = {
        title: null
    }
    state.newNewsModal.show();
}

function closeNewNewsModal() {
    state.newNewsModal.hide();
}

async function createNewNews() {
    try {
        const result = await axios.post('/news/create', {
            data: newNewsForm.value,
        });
        closeNewNewsModal();
        props.data.news.push(result.data);
    } catch (e) {

    }
}

onMounted(async () => {
    state.newNewsModal = new bootstrap.Modal(document.getElementById('newNewsModal'), {});
});


</script>

<template>

    <Head title="Новости" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            Новости
        </template>

        <template #PageTitle>
            Новости
        </template>

        <template #RightButtons>
            <a href="#" class="btn btn-primary" @click="openNewNewsModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Добавить новость
            </a>
        </template>
        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список новостей, нуждающихся в модерации</h3>
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
                                    <tr v-for="news in props.data.news_moderation">
                                        <td>{{ news.title }}</td>
                                        <td class="text-muted">
                                            <StatusBadge :enabled="news.enable_page" :status="news.moderation_status">
                                            </StatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            <ModerationStatusBadge :status="news.moderation_status">
                                            </ModerationStatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            {{ news.page_views }}
                                        </td>
                                        <td>
                                            <Link :href="'/news/view/' + news.id">Редактировать</Link>
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
                        <h3 class="card-title">Список одобренных новостей или только что добавленных новостей</h3>
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
                                    <tr v-for="news in props.data.news">
                                        <td>{{ news.title }}</td>
                                        <td class="text-muted">
                                            <StatusBadge :enabled="news.enable_page" :status="news.moderation_status">
                                            </StatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            <ModerationStatusBadge :status="news.moderation_status">
                                            </ModerationStatusBadge>
                                        </td>
                                        <td class="text-muted">
                                            {{ news.page_views }}
                                        </td>
                                        <td>
                                            <Link :href="'/news/view/' + news.id">Редактировать</Link>
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
                        <h5 class="modal-title">Добавление новости</h5>
                        <button type="button" class="btn-close" @click="closeNewNewsModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Заголовок</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newNewsForm.title">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p><small>Заголовок будет отображаться на страницах сайта</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeNewNewsModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="createNewNews()">Создать новость</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>
</template>
