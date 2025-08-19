<script setup>

import { ref, reactive, onMounted } from 'vue';
import Multiselect from '@vueform/multiselect'

const props = defineProps({
    band: Object
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

async function addParticipant() {
    try {
        const result = await axios.post('/bands/add_participant/' + props.band.id, participantForm.value);
        props.band.participants.push(result.data);
        closeParticipantModal()
    } catch (e) {

    }
}

async function deleteParticipant(id, index) {
    try {
        await axios.post('/bands/delete_participant/' + props.band.id, { id: id });
        props.band.participants.splice(index, 1);
    } catch (e) {

    }
}


onMounted(async () => {
    state.addParticipantModal = new bootstrap.Modal(document.getElementById('addParticipantModal'), {});
});

</script>

<template>
    <h4>Пожалуйста, укажите участников коллектива
    </h4>

    <div class="card mb-3" v-if="props.band.participants.length">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="participant, index in props.band.participants">
                    <td>{{ participant.last_name }},
                        {{ participant.first_name }}</td>
                    <td>
                        <button :disabled="props.band.moderation_status == 1 || participant.role == 'creator'"
                            class="btn btn-sm btn-warning"
                            @click="deleteParticipant(participant.id, index)"><!-- Download SVG icon from http://tabler-icons.io/i/star -->
                            Удалить участника
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <button :disabled="props.band.moderation_status == 1" class="btn btn-primary" @click="openParticipantModal()">
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