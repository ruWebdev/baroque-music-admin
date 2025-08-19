<script>

// Импорт разметки для проекта
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

export default {
    layout: MainLayout
};

</script>

<script setup>

import { ref, reactive, computed, onBeforeMount, onMounted, onBeforeUnmount } from 'vue';

import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps(
    ["data"]
);

const page = usePage()

const state = reactive({
    newComposerModal: null,
});

// Список отображаемых композиторов управляем локально
const composers = ref(props.data?.composers ?? []);

// Пагинация для вкладки "Все"
const pageSize = ref(props.data?.pagination?.per_page ?? 50);
const nextPage = ref(props.data?.pagination?.next_page ?? null);
const isLoading = ref(false);
const sentinel = ref(null);
let observer = null;

// Текущая выбранная буква (может прийти с бэка через query ?letter=)
const selectedLetter = ref(props.data?.currentLetter ?? null);

// Русский алфавит для фильтрации
const alphabet = [
    'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
];

async function fetchComposersByLetter(letter) {
    try {
        // Если выбрана вкладка "Все" — используем пагинацию
        if (!letter) {
            composers.value = [];
            nextPage.value = 1; // заставим fetchAllPage забрать первую страницу
            await fetchAllPage(1);
            // инициализируем наблюдатель
            initObserver();
            return;
        }
        // Иначе — выбранная буква: получаем полный список без пагинации
        const res = await axios.post('/composers/get_all', { letter });
        composers.value = res.data;
        nextPage.value = null; // отключаем пагинацию
        if (observer) observer.disconnect();
    } catch (e) {
        // можно добавить уведомление об ошибке
        console.error(e);
    }
}

function setQueryLetter(letter) {
    const url = new URL(window.location.href);
    if (letter) {
        url.searchParams.set('letter', letter);
    } else {
        url.searchParams.delete('letter');
    }
    window.history.replaceState({}, '', url);
}

async function selectLetter(letter) {
    selectedLetter.value = letter;
    setQueryLetter(letter);
    await fetchComposersByLetter(letter);
}

async function fetchAllPage(page) {
    if (isLoading.value || page === null) return;
    isLoading.value = true;
    try {
        const res = await axios.post('/composers/get_all', {
            page: page,
            size: pageSize.value,
        });
        if (page === 1) {
            composers.value = res.data.data;
        } else {
            composers.value = composers.value.concat(res.data.data);
        }
        nextPage.value = res.data.next_page;
    } catch (e) {
        console.error(e);
    } finally {
        isLoading.value = false;
    }
}

function initObserver() {
    if (observer) observer.disconnect();
    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // Только для вкладки "Все"
                if (!selectedLetter.value && nextPage.value) {
                    fetchAllPage(nextPage.value);
                }
            }
        });
    });
    if (sentinel.value) {
        observer.observe(sentinel.value);
    }
}

const newComposerForm = ref({
    title: null
});

function openNewComposerModal() {
    newComposerForm.value = {
        last_name: null,
        first_name: null,
    }
    state.newComposerModal.show();
}

function closeNewComposerModal() {
    state.newComposerModal.hide();
}

async function createNewComposer() {
    try {
        const result = await axios.post('/composers/create', {
            data: newComposerForm.value,
        });
        closeNewComposerModal();
        composers.value.push(result.data);
    } catch (e) {

    }
}

onMounted(async () => {
    state.newComposerModal = new bootstrap.Modal(document.getElementById('newComposerModal'), {});
    // Если пришла буква из URL (Inertia initial props), список уже отфильтрован на сервере.
    // При прямой загрузке/изменении буквы через UI — подгружаем с клиента.
    if (!selectedLetter.value) {
        // Стартуем с первой страницы, если бэк прислал пагинацию
        if (composers.value.length === 0) {
            await fetchAllPage(1);
        }
        initObserver();
    }
});

onBeforeUnmount(() => {
    if (observer) observer.disconnect();
});


</script>

<template>

    <Head title="Композиторы" />

    <ContentLayout>

        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            Композиторы
        </template>

        <template #PageTitle>
            Композиторы
        </template>

        <template #RightButtons>
            <a href="#" class="btn btn-primary" @click="openNewComposerModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Добавить композитора
            </a>
        </template>

        <!-- Блок выбора буквы -->
        <div class="mb-3">
            <div class="btn-group flex-wrap" role="group" aria-label="letters">
                <button
                    type="button"
                    class="btn"
                    :class="{ 'btn-primary': !selectedLetter, 'btn-outline-primary': selectedLetter }"
                    @click="selectLetter(null)"
                >Все</button>
                <button
                    v-for="ltr in alphabet"
                    :key="ltr"
                    type="button"
                    class="btn"
                    :class="{ 'btn-primary': selectedLetter === ltr, 'btn-outline-primary': selectedLetter !== ltr }"
                    @click="selectLetter(ltr)"
                >{{ ltr }}</button>
            </div>
        </div>

        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список композиторов</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Фамилия</th>
                                        <th>Имя</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in composers" :key="row.id">
                                        <td>{{ row.last_name }}</td>
                                        <td>{{ row.first_name }}</td>
                                        <td>
                                            <Link :href="'/composers/view/' + row.id">Редактировать</Link>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <!-- Loader and sentinel for infinite scroll (ALL tab) -->
                        <div class="py-3 text-center" v-if="isLoading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div v-else-if="!selectedLetter && nextPage === null" class="text-center text-muted py-2">
                            Больше записей нет
                        </div>
                        <div ref="sentinel" style="height: 1px;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal modal-blur fade" id="newComposerModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление композитора</h5>
                        <button type="button" class="btn-close" @click="closeNewComposerModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Фамилия (на русском)</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newComposerForm.last_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Имя (на русском)</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Заполните поле" v-model="newComposerForm.first_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeNewComposerModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click="createNewComposer()">Создать
                            композитора</button>
                    </div>
                </div>
            </div>
        </div>

    </ContentLayout>
</template>

