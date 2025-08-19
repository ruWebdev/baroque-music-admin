<script setup>

import { ref, reactive, onMounted } from 'vue';
import Multiselect from '@vueform/multiselect'

const props = defineProps({
    event: Object
})

const state = reactive({
    addParticipantModal: null
});

const participantForm = ref({
    type: 1,
    value: null
})

function openParticipantModal(type) {
    participantForm.value = {
        type: 1,
        value: null
    }
    state.addParticipantModal.show();
}

function closeParticipantModal() {
    state.addParticipantModal.hide();
}

async function handleTagCreateA(option, select$) {
    const newArtist = await axios.post(
        "/artists/create_from_select", {
        full_name: option.value
    });
    return { value: newArtist.data.id, title: newArtist.data.last_name + ', ' + newArtist.data.first_name };
}

async function asyncFindA(query) {
    let result;
    try {
        result = await axios.post('/artists/get_all',
            {
                query: query
            });
    } catch (e) {

    }
    return result.data.map((item) => {
        return { value: item.id, title: item.last_name + ', ' + item.first_name }
    })
}

async function handleTagCreateB(option, select$) {
    const newBand = await axios.post(
        "/bands/create_from_select", {
        title: option.value
    });
    return { value: newBand.data.id, title: newBand.data.title };
}

async function asyncFindB(query) {
    let result;
    try {
        result = await axios.post('/bands/get_all',
            {
                query: query
            });
    } catch (e) {

    }
    return result.data.map((item) => {
        return { value: item.id, title: item.title }
    })
}

async function addParticipant() {
    try {
        const result = await axios.post('/events/add_participant/' + props.event.id, participantForm.value);
        props.event.participants.push(result.data);
        closeParticipantModal()
    } catch (e) {

    }
}

async function deleteParticipant(id, index) {
    try {
        await axios.post('/events/delete_participant/' + props.event.id, { id: id });
        props.event.participants.splice(index, 1);
    } catch (e) {

    }
}


onMounted(async () => {
    state.addParticipantModal = new bootstrap.Modal(document.getElementById('addParticipantModal'), {});
});

</script>

<template>
    <h4>Пожалуйста, укажите участников концерта, если они есть
    </h4>

    <div class="card mb-3" v-if="props.event.participants.length">
        <div class="list-group list-group-flush list-group-hoverable">
            <div class="list-group-item p-2" v-for="participant, index in props.event.participants">
                <div class="row align-items-center">
                    <div class="col text-truncate align-middle">
                        <a href="#" v-if="participant.type == 1" class="text-reset d-block">{{ participant.last_name }},
                            {{ participant.first_name }}</a>
                        <a href="#" v-if="participant.type == 2" class="text-reset d-block">{{ participant.title }}</a>
                    </div>
                    <div class="col-auto">
                        <button :disabled="props.event.moderation_status == 1" class="btn btn-icon btn-warning"
                            @click="deleteParticipant(participant.id, index)"><!-- Download SVG icon from http://tabler-icons.io/i/star -->
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
    <button :disabled="props.event.moderation_status == 1" class="btn btn-primary" @click="openParticipantModal()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
        </svg>
        Добавить строку
    </button>

    <div ref="modal" class="modal modal-blur fade" id="addParticipantModal" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавление участника</h5>
                    <button type="button" class="btn-close" @click="closeParticipantModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Тип участника</label>
                                <select class="form-select" v-model="participantForm.type">
                                    <option value="1">Исполнитель</option>
                                    <option value="2">Коллектив</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3" v-if="participantForm.type == 1">
                            <label class="form-label">Имя исполнителя (только Фамилия, Имя, через запятую)</label>
                            <Multiselect v-model="participantForm.value" mode="single"
                                placeholder="Начните печатать, потом выберите из списка или напишите полностью"
                                :create-option="true" :searchable="true" :on-create="handleTagCreateA" label="title"
                                :options="async (query) => {
                                    return await asyncFindA(query)
                                }" />
                            <p class="mb-0"><small><b>Пример:</b> Иванов, Иван</small></p>
                        </div>
                        <div class="col-md-12 mb-3" v-if="participantForm.type == 2">
                            <label class="form-label">Название (только название, без упоминаний "ансамбль,
                                оркестр")</label>
                            <Multiselect v-model="participantForm.value" mode="single"
                                placeholder="Начните печатать, потом выберите из списка или напишите полностью"
                                :create-option="true" :searchable="true" :on-create="handleTagCreateB" label="title"
                                :options="async (query) => {
                                    return await asyncFindB(query)
                                }" />
                            <p class="mb-0"><small><b>Пример:</b> Les Arts Florissants</small></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" @click="closeParticipantModal()">Отменить</button>
                    <button type="button" class="btn btn-primary" @click="addParticipant()"
                        :disabled="!participantForm.type || !participantForm.value">Добавить</button>
                </div>
            </div>
        </div>
    </div>

</template>

<style src="@vueform/multiselect/themes/default.css"></style>