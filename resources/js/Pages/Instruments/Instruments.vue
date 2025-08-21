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
    newinstrumentModal: null
});

const newInstrumentForm = ref({
    title: null
});

function openNewInstrumentModal() {
    newInstrumentForm.value = {
        title: null,
    }
    state.newInstrumentModal.show();
}

function closenewInstrumentModal() {
    state.newInstrumentModal.hide();
}

async function createNewComposer() {
    try {
        const result = await axios.post('/instruments/create', {
            data: newInstrumentForm.value,
        });
        closenewInstrumentModal();
        props.data.instruments.push(result.data);
    } catch (e) {

    }
}

onMounted(async () => {
    state.newInstrumentModal = new bootstrap.Modal(document.getElementById('newInstrumentModal'), {});
});


</script>

<template>

    <Head title="Композиторы" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            Музыкальные инструменты
        </template>

        <template #PageTitle>
            Музыкальные инструменты
        </template>

        <template #RightButtons>
            <a href="#" class="btn btn-primary" @click="openNewInstrumentModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Добавить музыкальный инструмент
            </a>
        </template>
        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список музыкальных инструментов</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="instrument in props.data.instruments">
                                        <td>{{ instrument.title }}</td>
                                        <td>
                                            <Link :href="'/instruments/view/' + instrument.id">Редактировать</Link>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-blur fade" id="newInstrumentModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление музыкального инструмента</h5>
                        <button type="button" class="btn-close" @click="closenewInstrumentModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Название</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newInstrumentForm.title">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closenewInstrumentModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="createNewComposer()">Создать
                            музыкальный инструмент</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>
</template>
