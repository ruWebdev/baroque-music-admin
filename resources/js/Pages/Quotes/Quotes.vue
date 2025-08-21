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

const props = defineProps(
    ["data"]
);

const page = usePage()

const state = reactive({
    newQuoteModal: null
});

const newQuoteForm = ref({
    author: null,
    title: null,
});

function openNewQuoteModal() {
    newQuoteForm.value = {
        author: null,
        title: null,
    }
    state.newQuoteModal.show();
}

function closeNewQuoteModal() {
    state.newQuoteModal.hide();
}

async function createNewQuote() {
    try {
        const result = await axios.post('/quotes/create', newQuoteForm.value);
        closeNewQuoteModal();
        props.data.quotes.push(result.data);
    } catch (e) {

    }
}

async function deleteItem(id, index) {
    try {
        await axios.post('/quotes/delete', { id: id });
        props.data.quotes.splice(index, 1);
    } catch (e) {

    }
}

onMounted(async () => {
    state.newQuoteModal = new bootstrap.Modal(document.getElementById('newQuoteModal'), {});
});


</script>

<template>

    <Head title="Цитаты" />

    <ContentLayout>

        <template #PageTitle>
            Цитаты
        </template>

        <template #RightButtons>
            <a href="#" class="btn btn-primary" @click="openNewQuoteModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Добавить цитату
            </a>
        </template>
        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список цитат</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Автор</th>
                                        <th>Заголовок</th>
                                        <th class="w-1"></th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="quote in props.data.quotes">
                                        <td>{{ quote.author }}</td>
                                        <td>{{ quote.title }}</td>
                                        <td>
                                            <Link :href="'/quotes/view/' + quote.id">Редактировать</Link>
                                        </td>
                                        <td>
                                            <button class="btn btn-link text-danger"
                                                @click="deleteItem(quote.id, index)">
                                                Удалить</button>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal modal-blur fade" id="newQuoteModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление цитаты</h5>
                        <button type="button" class="btn-close" @click="closeNewQuoteModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Автор</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newQuoteForm.author">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Заголовок</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newQuoteForm.title">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeNewQuoteModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="createNewQuote()">Создать
                            цитату</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>
</template>
