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
import { useDictionaryStore } from '@/stores/dictionary';

const props = defineProps(
    ["data"]
);

const page = usePage()

const store = useDictionaryStore();

// Динамический алфавит на основе реальных первых букв
const alphabet = ref(props.data.letters || []);

// Текущая выбранная буква (может прийти с бэка через query ?letter=)
const selectedLetter = ref(props.data.currentLetter || null);

// Список терминов для выбранной буквы
const dictionary = ref(props.data.dictionary || []);
const highlightedId = ref(null);

const filteredDictionary = computed(() => {

    // Список уже отфильтрован на сервере по выбранной букве,
    // поэтому здесь просто возвращаем данные без дополнительной фильтрации.
    return dictionary.value || [];
});

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
        dictionary.value.push(result.data);

        // Обновим алфавит, если появилась новая первая буква
        const title = (result.data.title || '').trim();
        if (title) {
            const first = title[0].toUpperCase();
            if (!alphabet.value.includes(first)) {
                alphabet.value.push(first);
                alphabet.value.sort();
            }
        }
    } catch (e) {

    }
}

async function fixAliases() {
    try {
        await axios.post('/dictionary/fix_aliases');
        alert('Алиасы словаря успешно обновлены');
    } catch (e) {

    }
}

async function deleteItem(id) {
    try {
        await axios.post('/dictionary/delete', { id: id });
        const idx = dictionary.value.findIndex(item => item.id === id);
        if (idx !== -1) {
            dictionary.value.splice(idx, 1);
        }
    } catch (e) {

    }
}

// Обновление query-параметра letter в адресной строке
function setQueryLetter(letter) {
    const url = new URL(window.location.href);
    if (letter) {
        url.searchParams.set('letter', letter);
    } else {
        url.searchParams.delete('letter');
    }
    window.history.replaceState({}, '', url);
}

// Загрузка терминов для выбранной буквы
async function fetchDictionaryByLetter(letter) {
    try {
        const res = await axios.post('/dictionary/get_all', { letter });
        dictionary.value = res.data;
        persistToStore();
    } catch (e) {
        console.error(e);
    }
}

async function selectLetter(letter) {
    selectedLetter.value = letter;
    setQueryLetter(letter);
    await fetchDictionaryByLetter(letter);
    persistToStore();
}

function hydrateFromStore() {
    if (!store) return;
    if (store.selectedLetter !== null && store.selectedLetter !== undefined) {
        selectedLetter.value = store.selectedLetter;
    }
}

function persistToStore() {
    if (!store) return;
    store.selectedLetter = selectedLetter.value;
}

onMounted(async () => {
    state.newDictionaryModal = new bootstrap.Modal(document.getElementById('newDictionaryModal'), {});
    hydrateFromStore();

    // Гарантируем, что список терминов соответствует выбранной букве
    if (selectedLetter.value) {
        await fetchDictionaryByLetter(selectedLetter.value);
    }

    if (store.scrollTop && typeof window !== 'undefined') {
        setTimeout(() => window.scrollTo(0, store.scrollTop), 0);
    }

    if (store.lastVisitedDictionaryId && store.lastVisitedAt) {
        const age = Date.now() - store.lastVisitedAt;
        if (age < 10000) {
            highlightedId.value = store.lastVisitedDictionaryId;
            setTimeout(() => {
                highlightedId.value = null;
            }, 3000);
        }
    }
});

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        store.scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
    }
    persistToStore();
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
            <button class="btn btn-outline-secondary ms-2" @click="fixAliases()">
                Исправить Алиасы
            </button>
        </template>
        <!-- Блок выбора буквы -->
        <div class="mb-3" v-if="alphabet && alphabet.length">
            <div class="btn-toolbar w-100" role="toolbar" aria-label="letters toolbar">
                <div class="btn-group flex-wrap w-100 letters-bar" role="group" aria-label="letters group">
                    <button v-for="ltr in alphabet" :key="ltr" type="button" class="btn text-center letter-btn"
                        :class="{ 'btn-primary': selectedLetter === ltr, 'btn-outline-primary': selectedLetter !== ltr }"
                        @click="selectLetter(ltr)">{{ ltr }}</button>
                </div>
            </div>
        </div>
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
                                    <tr v-for="item in filteredDictionary" :key="item.id"
                                        :class="{ 'row-highlight': highlightedId === item.id }">
                                        <td>{{ item.title }}</td>
                                        <td>
                                            <Link :href="'/dictionary/view/' + item.id"
                                                @click="store.markVisited(item.id)">
                                                Редактировать</Link>
                                        </td>
                                        <td>
                                            <button class="btn btn-link text-danger" @click="deleteItem(item.id)">
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

<style scoped>
.row-highlight {
    animation: rowFlash 2s ease-in-out 1;
    background-color: #fff3cd !important;
    /* light warning */
}

@keyframes rowFlash {
    0% {
        background-color: #fff3cd;
    }

    100% {
        background-color: transparent;
    }
}
</style>
