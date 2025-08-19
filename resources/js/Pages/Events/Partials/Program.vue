<script setup>

import { ref, reactive, onMounted } from 'vue';
import Multiselect from '@vueform/multiselect'

const props = defineProps({
    event: Object
})

const state = reactive({
    addProgrammModal: null
});

const programForm = ref({
    composer: null,
    title: null
})

function openProgrammModal(type) {
    programForm.value = {
        composer: null,
        title: null
    }
    state.addProgrammModal.show();
}

function closeProgrammModal() {
    state.addProgrammModal.hide();
}

async function handleTagCreate(option, select$) {
    const newComposer = await axios.post(
        "/composers/create_from_select", {
        full_name: option.value
    });
    return { value: newComposer.data.id, title: newComposer.data.last_name + ', ' + newComposer.data.first_name };
}

async function asyncFind(query) {
    let result;
    try {
        result = await axios.post('/composers/get_all',
            {
                query: query
            });
    } catch (e) {

    }

    return result.data.map((item) => {
        return { value: item.id, title: item.last_name + ', ' + item.first_name }
    })

}

async function addProgramm() {
    try {
        const result = await axios.post('/events/add_program/' + props.event.id, programForm.value);
        props.event.program.push(result.data);
        closeProgrammModal()
    } catch (e) {

    }
}

async function deleteProgram(id, index) {
    try {
        await axios.post('/events/delete_program/' + props.event.id, { id: id });
        props.event.program.splice(index, 1);
    } catch (e) {

    }
}


onMounted(async () => {
    state.addProgrammModal = new bootstrap.Modal(document.getElementById('addProgrammModal'), {});
});

</script>

<template>
    <h4>Пожалуйста, если мероприятие является концертом или спектаклем укажите программу
        вашего
        события
    </h4>
    <div class="card mb-3" v-if="props.event.program.length">
        <div class="list-group list-group-flush list-group-hoverable">
            <div class="list-group-item p-2" v-for="program, index in props.event.program">
                <div class="row align-items-center">
                    <div class="col text-truncate">
                        <a href="#" class="text-reset d-block">{{ program.last_name }}, {{ program.first_name }}</a>
                    </div>
                    <div class="col text-truncate">
                        <div class="d-block text-muted text-truncate mt-n1">{{ program.title }}</div>
                    </div>
                    <div class="col-auto">
                        <button :disabled="props.event.moderation_status == 1" class="btn btn-icon btn-warning"
                            @click="deleteProgram(program.id, index)"><!-- Download SVG icon from http://tabler-icons.io/i/star -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button :disabled="props.event.moderation_status == 1" class="btn btn-primary" @click="openProgrammModal()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
        </svg>
        Добавить строку
    </button>

    <div ref="modal" class="modal modal-blur fade" id="addProgrammModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавление пункта в программу</h5>
                    <button type="button" class="btn-close" @click="closeProgrammModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Композитор (только Фамилия, Имя, через запятую)</label>
                            <Multiselect v-model="programForm.composer" mode="single"
                                placeholder="Начните печатать, потом выберите из списка или напишите полностью"
                                :create-option="true" :searchable="true" :on-create="handleTagCreate" label="title"
                                :options="async (query) => {
                                    return await asyncFind(query)
                                }" />
                            <p><small><b>Пример:</b> Бах, Иоганн Себастьян</small></p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Название произведения</label>
                            <input type="text" class="form-control mb-3" v-model="programForm.title" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" @click="closeProgrammModal()">Отменить</button>
                    <button type="button" class="btn btn-primary" @click="addProgramm()"
                        :disabled="!programForm.composer">Добавить</button>
                </div>
            </div>
        </div>
    </div>

</template>

<style src="@vueform/multiselect/themes/default.css"></style>