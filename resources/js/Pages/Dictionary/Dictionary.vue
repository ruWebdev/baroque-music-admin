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
    newDictionaryModal: null
});

const newDictionaryForm = ref({
    title: null
});

function openNewDictionaryModal() {
    newDictionaryForm.value = {
        title: null,
    }
    state.newDictionaryModal.show();
}

function closeNewDictionaryModal() {
    state.newDictionaryModal.hide();
}

async function createNewDictionary() {
    try {
        const result = await axios.post('/dictionary/create', newDictionaryForm.value);
        closeNewDictionaryModal();
        props.data.dictionary.push(result.data);
    } catch (e) {

    }
}

async function deleteItem(id, index) {
    try {
        await axios.post('/dictionary/delete', { id: id });
        props.data.dictionary.splice(index, 1);
    } catch (e) {

    }
}

onMounted(async () => {
    state.newDictionaryModal = new bootstrap.Modal(document.getElementById('newDictionaryModal'), {});
});


</script>

<template>

    <Head title="Словарь музыкальных терминов" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            Словарь музыкальных терминов
        </template>

        <template #PageTitle>
            Словарь музыкальных терминов
        </template>

        <template #RightButtons>
            <a href="#" class="btn btn-primary" @click="openNewDictionaryModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Добавить новый музыкальный термин
            </a>
        </template>
        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список музыкальных терминов</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th class="w-1"></th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="dictionary, index in props.data.dictionary">
                                        <td>{{ dictionary.title }}</td>
                                        <td>
                                            <Link :href="'/dictionary/view/' + dictionary.id">Редактировать</Link>
                                        </td>
                                        <td>
                                            <button class="btn btn-link text-danger"
                                                @click="deleteItem(dictionary.id, index)">
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

        <div class="modal modal-blur fade" id="newDictionaryModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление музыкального термина</h5>
                        <button type="button" class="btn-close" @click="closeNewDictionaryModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Название</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newDictionaryForm.title">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeNewDictionaryModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="createNewDictionary()">Создать
                            музыкальный термин</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>
</template>
