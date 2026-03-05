<script>
import MainLayout from '@/Layouts/MainLayout.vue';
export default {
    layout: MainLayout
};
</script>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { useOperasStore } from '@/stores/operas';
import Multiselect from '@vueform/multiselect';

const props = defineProps(["data"]);

const state = reactive({
    newOperaModal: null,
});

const store = useOperasStore();
const operas = ref(props.data?.operas ?? []);

const pageSize = ref(props.data?.pagination?.per_page ?? 50);
const nextPage = ref(props.data?.pagination?.next_page ?? null);
const isLoading = ref(false);
const sentinel = ref(null);
let observer = null;

const highlightedId = ref(null);
const selectedLetter = ref(props.data?.currentLetter ?? null);

const alphabet = [
    'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
];

async function fetchOperasByLetter(letter) {
    try {
        if (!letter) {
            operas.value = [];
            nextPage.value = 1;
            await fetchAllPage(1);
            initObserver();
            return;
        }
        const res = await axios.post('/operas/get_all', { letter });
        operas.value = res.data;
        nextPage.value = null;
        if (observer) observer.disconnect();
        persistToStore();
    } catch (e) {
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
    await fetchOperasByLetter(letter);
    clearSearch();
    persistToStore();
}

async function fetchAllPage(page) {
    if (isLoading.value || page === null) return;
    isLoading.value = true;
    try {
        const res = await axios.post('/operas/get_all', {
            page: page,
            size: pageSize.value,
        });
        if (page === 1) {
            operas.value = res.data.data;
        } else {
            operas.value = operas.value.concat(res.data.data);
        }
        nextPage.value = res.data.next_page;
        persistToStore();
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
                if (!selectedLetter.value && !isSearching.value && nextPage.value) {
                    fetchAllPage(nextPage.value);
                }
            }
        });
    });
    if (sentinel.value) {
        observer.observe(sentinel.value);
    }
}

const newOperaForm = ref({
    title: null,
    composer: null,
});

function openNewOperaModal() {
    newOperaForm.value = {
        title: null,
        composer: null,
    };
    state.newOperaModal.show();
}

function closeNewOperaModal() {
    state.newOperaModal.hide();
}

async function createNewOpera() {
    try {
        if (!newOperaForm.value.composer) {
            return;
        }
        const result = await axios.post('/operas/create', {
            data: {
                title: newOperaForm.value.title,
                composer_id: newOperaForm.value.composer?.value ?? null,
            },
        });
        closeNewOperaModal();
        operas.value.push(result.data);
    } catch (e) {
        console.error(e);
    }
}

async function handleComposerCreate(option) {
    const newComposer = await axios.post(
        '/composers/create_from_select',
        { full_name: option.value }
    );
    return { value: newComposer.data.id, title: newComposer.data.last_name + ', ' + newComposer.data.first_name };
}

async function asyncFindComposers(query) {
    const search = typeof query === 'string' ? query : (query?.search ?? '');
    if (!search || !search.trim()) {
        return [];
    }
    let result;
    try {
        result = await axios.post('/composers/search', {
            q: search,
        });
    } catch (e) {
        return [];
    }

    const items = Array.isArray(result.data) ? result.data : [];
    return items.map((item) => {
        return { value: item.id, title: item.last_name + ', ' + item.first_name };
    });
}

onMounted(async () => {
    state.newOperaModal = new bootstrap.Modal(document.getElementById('newOperaModal'), {});
    hydrateFromStore();
    if (!selectedLetter.value) {
        if (operas.value.length === 0) {
            await fetchAllPage(1);
        }
        if (!isSearching.value) initObserver();
    }
    if (store.scrollTop && typeof window !== 'undefined') {
        setTimeout(() => window.scrollTo(0, store.scrollTop), 0);
    }
    if (store.lastVisitedOperaId && store.lastVisitedAt) {
        const age = Date.now() - store.lastVisitedAt;
        if (age < 10000) {
            highlightedId.value = store.lastVisitedOperaId;
            setTimeout(() => {
                highlightedId.value = null;
            }, 3000);
        }
    }
});

onBeforeUnmount(() => {
    if (observer) observer.disconnect();
    if (typeof window !== 'undefined') {
        store.scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
    }
    persistToStore();
});

const searchQuery = ref('');
const isSearching = ref(false);
const searchResults = ref([]);
let searchTimer = null;

function onSearchInput() {
    const q = searchQuery.value.trim();
    if (q === '') {
        clearSearch();
        return;
    }
    isSearching.value = true;
    if (observer) observer.disconnect();
    if (searchTimer) clearTimeout(searchTimer);
    searchTimer = setTimeout(runSearch, 300);
    persistToStore();
}

async function runSearch() {
    const q = searchQuery.value.trim();
    if (q === '') {
        clearSearch();
        return;
    }
    try {
        const res = await axios.post('/operas/search', { q });
        searchResults.value = res.data;
        persistToStore();
    } catch (e) {
        console.error(e);
    }
}

function clearSearch() {
    isSearching.value = false;
    searchResults.value = [];
    if (!selectedLetter.value) {
        initObserver();
    }
    persistToStore();
}

function hydrateFromStore() {
    if (!store) return;
    if (store.pageSize) pageSize.value = store.pageSize;
    if (store.selectedLetter !== null && store.selectedLetter !== undefined) {
        selectedLetter.value = store.selectedLetter;
    }
    if (Array.isArray(store.operas) && store.operas.length) {
        operas.value = store.operas;
    }
    if (store.nextPage !== null && store.nextPage !== undefined) {
        nextPage.value = store.nextPage;
    }
    if (store.isSearching) {
        isSearching.value = true;
        searchQuery.value = store.searchQuery || '';
        searchResults.value = Array.isArray(store.searchResults) ? store.searchResults : [];
        if (observer) observer.disconnect();
    }
}

function persistToStore() {
    if (!store) return;
    store.pageSize = pageSize.value;
    store.selectedLetter = selectedLetter.value;
    store.operas = operas.value;
    store.nextPage = nextPage.value;
    store.isSearching = isSearching.value;
    store.searchQuery = searchQuery.value;
    store.searchResults = searchResults.value;
}
</script>

<template>
    <Head title="Оперы" />

    <ContentLayout>
        <template #BreadCrumbs>
            <Link class="text-primary" href="/">Главная страница</Link> /
            Оперы
        </template>

        <template #PageTitle>
            Оперы
        </template>

        <template #RightButtons>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end w-100">
                <div class="search-wrap">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Поиск оперы"
                        v-model="searchQuery"
                        @input="onSearchInput"
                    />
                </div>
                <button type="button" class="btn btn-primary" @click.prevent="openNewOperaModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Добавить оперу
                </button>
            </div>
        </template>

        <div class="mb-3">
            <div class="btn-toolbar w-100" role="toolbar" aria-label="letters toolbar">
                <div class="btn-group flex-wrap w-100 letters-bar" role="group" aria-label="letters group">
                    <button
                        type="button"
                        class="btn text-center letter-btn"
                        :class="{ 'btn-primary': !selectedLetter, 'btn-outline-primary': selectedLetter }"
                        @click="selectLetter(null)"
                    >Все</button>
                    <button
                        v-for="ltr in alphabet"
                        :key="ltr"
                        type="button"
                        class="btn text-center letter-btn"
                        :class="{ 'btn-primary': selectedLetter === ltr, 'btn-outline-primary': selectedLetter !== ltr }"
                        @click="selectLetter(ltr)"
                    >{{ ltr }}</button>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Список опер</h3>
                    </div>
                    <div class="card-body p-0 m-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th>Год</th>
                                        <th>Композитор</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in (isSearching ? searchResults : operas)" :key="row.id" :class="{ 'row-highlight': highlightedId === row.id }">
                                        <td>
                                            {{ row.title }}
                                            <span
                                                v-if="!(row.long_description && row.long_description.trim()) && !(row.page_alias && row.page_alias.trim())"
                                                class="text-danger ms-1"
                                                title="Отсутствуют описание и alias"
                                            >*</span>
                                        </td>
                                        <td>{{ row.year_created }}</td>
                                        <td>
                                            <span v-if="row.composer">
                                                {{ row.composer.last_name }}, {{ row.composer.first_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <Link :href="'/operas/view/' + row.id" @click="store.markVisited(row.id)">Редактировать</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="py-3 text-center" v-if="isLoading && !isSearching">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div v-else-if="!isSearching && !selectedLetter && nextPage === null" class="text-center text-muted py-2">
                            Больше записей нет
                        </div>
                        <div v-if="!isSearching" ref="sentinel" style="height: 1px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-blur fade" id="newOperaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавление оперы</h5>
                        <button type="button" class="btn-close" @click="closeNewOperaModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Композитор <span class="text-danger">*</span></label>
                                    <Multiselect
                                        v-model="newOperaForm.composer"
                                        mode="single"
                                        placeholder="Начните печатать и выберите композитора"
                                        :create-option="true"
                                        :searchable="true"
                                        :on-create="handleComposerCreate"
                                        label="title"
                                        :options="async (query) => asyncFindComposers(query)"
                                    />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Название (на русском)</label>
                                    <input type="text" class="form-control" placeholder="Заполните поле" v-model="newOperaForm.title">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" @click="closeNewOperaModal()">Отменить</button>
                        <button type="button" class="btn btn-primary" @click.prevent="createNewOpera()">Создать оперу</button>
                    </div>
                </div>
            </div>
        </div>
    </ContentLayout>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>
