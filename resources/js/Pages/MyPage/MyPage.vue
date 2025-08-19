<script>

// Импорт разметки для проекта
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';

export default {
    layout: MainLayout
};

</script>

<script setup>

import { ref } from 'vue';

import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const page = usePage()

const processing = ref(false);

const enableTelegramNotifications = async () => {
    processing.value = true
    try {
        const response = await axios.get('/telegram/temp-url');
        window.open(response.data.telegramUrl, '_blank').focus();

    } catch (error) {
        // TODO: Display error to user.
        console.error(error);
    }
};


</script>

<template>

    <Head title="Dashboard" />

    <ContentLayout>

        <template #PageTitle>
            Моя страница: основная информация
        </template>

        <div class="row row-cards">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Основная информация</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Фамилия</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Имя</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Отчество (необязательно)</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Место рождения (необязательно)</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Дата рождения (необязательно)</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Краткая информация о себе</label>
                                    <input type="text" class="form-control" name="example-text-input"
                                        placeholder="Не заполнено">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Подробная биографическая информация</label>
                                    <textarea class="form-control" rows="10"></textarea>
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="card-header">
                        <h3 class="card-title">Настройки страницы</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox">
                                        <span class="form-check-label">Показывать мою страницу на сайте
                                            BaroqueMusic.ru</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Адрес страницы на сайте</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            https://baroquemusic.ru/artists/
                                        </span>
                                        <input type="text" class="form-control" placeholder="имя_пользователя"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-label">Видимость информации</div>
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <span class="form-check-label">Показывать день рождения и возраст</span>
                                    </label>
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox">
                                        <span class="form-check-label">Показывать место рождения</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </ContentLayout>
</template>
